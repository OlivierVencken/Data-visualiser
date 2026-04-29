<?php

namespace Tests\Feature;

use App\Models\Dashboard;
use App\Models\Dataset;
use App\Models\DatasetRow;
use App\Models\User;
use App\Models\Visualization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualizationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_open_visualization_create_page_for_own_dashboard(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = $this->createDashboardForUser($user, $dataset);

        $response = $this->actingAs($user)->get(route('dashboards.visualizations.create', $dashboard));

        $response->assertOk();
        $response->assertViewIs('visualizations.create');
    }

    public function test_user_cannot_open_create_page_for_someone_elses_dashboard(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($owner);
        $dashboard = $this->createDashboardForUser($owner, $dataset);

        $response = $this->actingAs($intruder)->get(route('dashboards.visualizations.create', $dashboard));

        $response->assertForbidden();
    }

    public function test_create_page_redirects_if_dashboard_dataset_is_not_completed(): void
    {
        $user = User::factory()->create();
        $dataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Pending data',
            'source_filename' => 'pending.csv',
            'row_count' => 0,
            'status' => 'processing',
        ]);
        $dashboard = $this->createDashboardForUser($user, $dataset);

        $response = $this->actingAs($user)->get(route('dashboards.visualizations.create', $dashboard));

        $response->assertRedirect(route('dashboards.show', $dashboard));
        $response->assertSessionHas('error');
    }

    public function test_user_can_store_visualization_with_valid_data(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = $this->createDashboardForUser($user, $dataset);

        $response = $this->actingAs($user)->post(route('dashboards.visualizations.store', $dashboard), [
            'name' => 'Revenue by month',
            'type' => 'bar',
            'x_axis' => 'month',
            'y_axis' => 'revenue',
            'aggregation' => 'sum',
            'color_override' => '#A1B2C3',
        ]);

        $response->assertRedirect(route('dashboards.show', $dashboard));
        $this->assertDatabaseHas('visualizations', [
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Revenue by month',
            'type' => 'bar',
        ]);
    }

    public function test_store_visualization_fails_when_dashboard_dataset_not_available(): void
    {
        $user = User::factory()->create();
        $dataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Pending data',
            'source_filename' => 'pending.csv',
            'row_count' => 0,
            'status' => 'processing',
        ]);
        $dashboard = $this->createDashboardForUser($user, $dataset);

        $response = $this->actingAs($user)->from(route('dashboards.visualizations.create', $dashboard))
            ->post(route('dashboards.visualizations.store', $dashboard), [
                'name' => 'Revenue by month',
                'type' => 'bar',
                'x_axis' => 'month',
                'y_axis' => 'revenue',
                'aggregation' => 'sum',
            ]);

        $response->assertRedirect(route('dashboards.visualizations.create', $dashboard));
        $response->assertSessionHasErrors('name');
    }

    public function test_update_visualization_validates_axis_columns_against_dataset(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = $this->createDashboardForUser($user, $dataset);
        $visualization = Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Revenue by month',
            'type' => 'bar',
            'config' => [
                'x_axis' => 'month',
                'y_axis' => 'revenue',
                'aggregation' => 'sum',
            ],
        ]);

        $response = $this->actingAs($user)->from(route('dashboards.visualizations.edit', [$dashboard, $visualization]))
            ->put(route('dashboards.visualizations.update', [$dashboard, $visualization]), [
                'name' => 'Updated chart',
                'type' => 'line',
                'x_axis' => 'invalid_column',
                'y_axis' => 'revenue',
                'aggregation' => 'avg',
            ]);

        $response->assertRedirect(route('dashboards.visualizations.edit', [$dashboard, $visualization]));
        $response->assertSessionHasErrors('x_axis');
    }

    public function test_user_can_open_edit_page_for_own_visualization(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = $this->createDashboardForUser($user, $dataset);
        $visualization = Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Revenue chart',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);

        $response = $this->actingAs($user)->get(route('dashboards.visualizations.edit', [$dashboard, $visualization]));

        $response->assertOk();
        $response->assertViewIs('visualizations.edit');
    }

    public function test_edit_visualization_redirects_if_visualization_dataset_differs_from_dashboard_dataset(): void
    {
        $user = User::factory()->create();
        $dashboardDataset = $this->createCompletedDatasetForUser($user);
        $otherDataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Other data',
            'source_filename' => 'other.csv',
            'row_count' => 1,
            'status' => 'completed',
        ]);
        DatasetRow::create([
            'dataset_id' => $otherDataset->id,
            'row_index' => 1,
            'data' => ['month' => 'Mar', 'revenue' => '300'],
        ]);
        $dashboard = $this->createDashboardForUser($user, $dashboardDataset);
        $visualization = Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $otherDataset->id,
            'name' => 'Mismatched chart',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);

        $response = $this->actingAs($user)->get(route('dashboards.visualizations.edit', [$dashboard, $visualization]));

        $response->assertRedirect(route('dashboards.show', $dashboard));
        $response->assertSessionHas('error');
    }

    public function test_user_can_update_own_visualization_with_valid_data(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = $this->createDashboardForUser($user, $dataset);
        $visualization = Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Revenue by month',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);

        $response = $this->actingAs($user)->put(route('dashboards.visualizations.update', [$dashboard, $visualization]), [
            'name' => 'Updated chart',
            'type' => 'line',
            'x_axis' => 'month',
            'y_axis' => 'revenue',
            'aggregation' => 'avg',
            'color_override' => '#445566',
        ]);

        $response->assertRedirect(route('dashboards.show', $dashboard));
        $visualization->refresh();
        $this->assertSame('Updated chart', $visualization->name);
        $this->assertSame('line', $visualization->type);
        $this->assertSame('avg', $visualization->config['aggregation']);
        $this->assertSame('#445566', $visualization->config['color_override']);
    }

    public function test_user_cannot_update_visualization_of_another_user(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($owner);
        $dashboard = $this->createDashboardForUser($owner, $dataset);
        $visualization = Visualization::create([
            'user_id' => $owner->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Revenue by month',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);

        $response = $this->actingAs($intruder)->put(route('dashboards.visualizations.update', [$dashboard, $visualization]), [
            'name' => 'Hijacked chart',
            'type' => 'line',
            'x_axis' => 'month',
            'y_axis' => 'revenue',
            'aggregation' => 'avg',
        ]);

        $response->assertForbidden();
    }

    public function test_user_can_delete_own_visualization(): void
    {
        $user = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($user);
        $dashboard = $this->createDashboardForUser($user, $dataset);
        $visualization = Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Revenue chart',
            'type' => 'bar',
            'config' => [
                'x_axis' => 'month',
                'y_axis' => 'revenue',
                'aggregation' => 'sum',
            ],
        ]);

        $response = $this->actingAs($user)->delete(route('dashboards.visualizations.destroy', [$dashboard, $visualization]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('visualizations', ['id' => $visualization->id]);
    }

    public function test_user_cannot_delete_visualization_of_another_user(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $dataset = $this->createCompletedDatasetForUser($owner);
        $dashboard = $this->createDashboardForUser($owner, $dataset);
        $visualization = Visualization::create([
            'user_id' => $owner->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Revenue chart',
            'type' => 'bar',
            'config' => [
                'x_axis' => 'month',
                'y_axis' => 'revenue',
                'aggregation' => 'sum',
            ],
        ]);

        $response = $this->actingAs($intruder)
            ->delete(route('dashboards.visualizations.destroy', [$dashboard, $visualization]));

        $response->assertForbidden();
    }

    private function createDashboardForUser(User $user, Dataset $dataset): Dashboard
    {
        return Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Sales dashboard',
            'description' => 'Tracks sales',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);
    }

    private function createCompletedDatasetForUser(User $user): Dataset
    {
        $dataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Sales data',
            'source_filename' => 'sales.csv',
            'row_count' => 2,
            'status' => 'completed',
        ]);

        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_index' => 1,
            'data' => ['month' => 'Jan', 'revenue' => '100'],
        ]);

        DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_index' => 2,
            'data' => ['month' => 'Feb', 'revenue' => '200'],
        ]);

        return $dataset;
    }
}
