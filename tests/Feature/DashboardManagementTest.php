<?php

namespace Tests\Feature;

use App\Models\Dashboard;
use App\Models\Dataset;
use App\Models\DatasetRow;
use App\Models\User;
use App\Models\UserColorTheme;
use App\Models\Visualization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DashboardManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_dashboard_with_csv(): void
    {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->createWithContent(
            'sales.csv',
            "month,revenue\nJan,100\nFeb,200\n"
        );

        $response = $this->actingAs($user)->post(route('dashboards.store'), [
            'name' => 'Sales dashboard',
            'description' => 'Tracks monthly revenue',
            'csv_file' => $file,
        ]);

        $dashboard = Dashboard::first();
        $dataset = Dataset::first();

        $response->assertRedirect(route('dashboards.show', $dashboard));
        $this->assertDatabaseHas('dashboards', [
            'id' => $dashboard->id,
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Sales dashboard',
        ]);
        $this->assertDatabaseHas('datasets', [
            'id' => $dataset->id,
            'user_id' => $user->id,
            'status' => 'completed',
            'row_count' => 2,
        ]);
        $this->assertDatabaseCount('dataset_rows', 2);
        $this->assertSame('builtin', $dashboard->layout_config['color_theme_mode']);
        $this->assertSame('default', $dashboard->layout_config['color_theme']);
        $this->assertNull($dashboard->layout_config['custom_theme_id']);
    }

    public function test_dashboard_creation_requires_csv_file(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboards.store'), [
            'name' => 'Sales dashboard',
            'description' => 'Tracks monthly revenue',
        ]);

        $response->assertSessionHasErrors('csv_file');
    }

    public function test_user_cannot_view_another_users_dashboard(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($owner);
        $dashboard = Dashboard::create([
            'user_id' => $owner->id,
            'dataset_id' => $dataset->id,
            'name' => 'Private dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);

        $response = $this->actingAs($intruder)->get(route('dashboards.show', $dashboard));

        $response->assertForbidden();
    }

    public function test_user_can_switch_dashboard_to_builtin_theme(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Theme dashboard',
            'layout_config' => [
                'color_theme_mode' => 'custom',
                'color_theme' => 'default',
                'custom_theme_id' => 999,
            ],
        ]);

        $response = $this->actingAs($user)->put(route('dashboards.settings.update', $dashboard), [
            'theme_mode' => 'builtin',
            'built_in_theme' => 'forest',
        ]);

        $response->assertRedirect(route('dashboards.settings', $dashboard));
        $dashboard->refresh();

        $this->assertSame('builtin', $dashboard->layout_config['color_theme_mode']);
        $this->assertSame('forest', $dashboard->layout_config['color_theme']);
        $this->assertNull($dashboard->layout_config['custom_theme_id']);
    }

    public function test_user_can_switch_dashboard_to_custom_theme_when_theme_is_owned(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Theme dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);
        $theme = UserColorTheme::create([
            'user_id' => $user->id,
            'name' => 'My Theme',
            'colors' => ['#AA0000', '#00AA00', '#0000AA'],
        ]);

        $response = $this->actingAs($user)->put(route('dashboards.settings.update', $dashboard), [
            'theme_mode' => 'custom',
            'custom_theme_id' => $theme->id,
        ]);

        $response->assertRedirect(route('dashboards.settings', $dashboard));
        $dashboard->refresh();
        $this->assertSame('custom', $dashboard->layout_config['color_theme_mode']);
        $this->assertSame($theme->id, $dashboard->layout_config['custom_theme_id']);
    }

    public function test_custom_theme_update_fails_when_theme_belongs_to_another_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($owner);
        $dashboard = Dashboard::create([
            'user_id' => $owner->id,
            'dataset_id' => $dataset->id,
            'name' => 'Theme dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);
        $foreignTheme = UserColorTheme::create([
            'user_id' => $otherUser->id,
            'name' => 'Not mine',
            'colors' => ['#111111', '#222222', '#333333'],
        ]);

        $response = $this->actingAs($owner)->from(route('dashboards.settings', $dashboard))
            ->put(route('dashboards.settings.update', $dashboard), [
                'theme_mode' => 'custom',
                'custom_theme_id' => $foreignTheme->id,
            ]);

        $response->assertRedirect(route('dashboards.settings', $dashboard));
        $response->assertSessionHasErrors('custom_theme_id');
    }

    public function test_user_can_create_and_delete_custom_theme(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Theme dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);

        $createResponse = $this->actingAs($user)->post(route('dashboards.settings.themes.store', $dashboard), [
            'theme_name' => 'My palette',
            'colors' => ['#111111', '#222222', '#333333'],
        ]);

        $theme = UserColorTheme::first();
        $this->assertNotNull($theme);
        $createResponse->assertRedirect(route('dashboards.settings', $dashboard));

        $dashboard->update([
            'layout_config' => [
                'color_theme_mode' => 'custom',
                'color_theme' => 'default',
                'custom_theme_id' => $theme->id,
            ],
        ]);

        $deleteResponse = $this->actingAs($user)
            ->delete(route('dashboards.settings.themes.destroy', [$dashboard, $theme]));

        $deleteResponse->assertRedirect(route('dashboards.settings', $dashboard));
        $this->assertDatabaseMissing('user_color_themes', ['id' => $theme->id]);
        $dashboard->refresh();
        $this->assertSame('builtin', $dashboard->layout_config['color_theme_mode']);
        $this->assertNull($dashboard->layout_config['custom_theme_id']);
    }

    public function test_user_can_delete_own_dashboard(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Disposable dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);

        $response = $this->actingAs($user)->delete(route('dashboards.destroy', $dashboard));

        $response->assertRedirect(route('home'));
        $this->assertDatabaseMissing('dashboards', ['id' => $dashboard->id]);
    }

    public function test_user_cannot_delete_dashboard_of_another_user(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($owner);
        $dashboard = Dashboard::create([
            'user_id' => $owner->id,
            'dataset_id' => $dataset->id,
            'name' => 'Protected dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);

        $response = $this->actingAs($intruder)->delete(route('dashboards.destroy', $dashboard));

        $response->assertForbidden();
    }

    public function test_home_only_lists_authenticated_users_dashboards(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $userDataset = $this->createCompletedDatasetForUser($user);
        $otherDataset = $this->createCompletedDatasetForUser($otherUser);
        $usersDashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $userDataset->id,
            'name' => 'My dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);
        Dashboard::create([
            'user_id' => $otherUser->id,
            'dataset_id' => $otherDataset->id,
            'name' => 'Other dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);
        Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $usersDashboard->id,
            'dataset_id' => $userDataset->id,
            'name' => 'Chart',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);

        $response = $this->actingAs($user)->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('dashboards', function ($dashboards) use ($user, $usersDashboard) {
            return $dashboards->count() === 1
                && $dashboards->first()->id === $usersDashboard->id
                && $dashboards->first()->user_id === $user->id
                && (int) $dashboards->first()->visualizations_count === 1;
        });
    }

    public function test_dashboard_show_redirects_to_home_when_linked_dataset_is_missing(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Broken dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);
        $dataset->delete();
        $dashboard->refresh();

        $response = $this->actingAs($user)->get(route('dashboards.show', $dashboard));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error');
    }

    public function test_dashboard_show_builds_aggregated_visualization_data_and_skips_mismatched_dataset_visualizations(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_index' => 2,
            'data' => ['month' => 'Jan', 'revenue' => '$50'],
        ]);
        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_index' => 3,
            'data' => ['month' => 'Feb', 'revenue' => '200'],
        ]);
        $otherDataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Other',
            'source_filename' => 'other.csv',
            'row_count' => 0,
            'status' => 'completed',
        ]);
        $dashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Analytics',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);

        Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Sum chart',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);
        Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Avg chart',
            'type' => 'line',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'avg'],
        ]);
        Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Count chart',
            'type' => 'pie',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'count'],
        ]);
        Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $otherDataset->id,
            'name' => 'Should be skipped',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);

        $response = $this->actingAs($user)->get(route('dashboards.show', $dashboard));

        $response->assertOk();
        $response->assertViewHas('visualizationsData', function (array $visualizationsData) {
            if (count($visualizationsData) !== 3) {
                return false;
            }

            $indexed = collect($visualizationsData)->keyBy('name');

            return $indexed->has('Sum chart')
                && $indexed->has('Avg chart')
                && $indexed->has('Count chart')
                && $indexed['Sum chart']['values'] === [150.0, 200.0]
                && $indexed['Avg chart']['values'] === [75.0, 200.0]
                && $indexed['Count chart']['values'] === [2, 1];
        });
    }

    private function createCompletedDatasetForUser(User $user): Dataset
    {
        $dataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Sales data',
            'source_filename' => 'sales.csv',
            'row_count' => 1,
            'status' => 'completed',
        ]);

        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_index' => 1,
            'data' => ['month' => 'Jan', 'revenue' => '100'],
        ]);

        return $dataset;
    }
}
