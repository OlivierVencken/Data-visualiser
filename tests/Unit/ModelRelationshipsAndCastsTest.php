<?php

namespace Tests\Unit;

use App\Models\Dashboard;
use App\Models\Dataset;
use App\Models\DatasetColumn;
use App\Models\DatasetRow;
use App\Models\DatasetUpload;
use App\Models\ImportRun;
use App\Models\User;
use App\Models\UserColorTheme;
use App\Models\Visualization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModelRelationshipsAndCastsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_relationships_resolve_correctly(): void
    {
        $user = User::factory()->create();
        $dataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Dataset',
            'source_filename' => 'data.csv',
            'row_count' => 1,
            'status' => 'completed',
        ]);
        $dashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);
        $visualization = Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Vis',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);

        $this->assertTrue($dashboard->user->is($user));
        $this->assertTrue($dashboard->dataset->is($dataset));
        $this->assertCount(1, $dashboard->visualizations);
        $this->assertTrue($dashboard->visualizations->first()->is($visualization));
    }

    public function test_dataset_relationships_resolve_correctly(): void
    {
        $user = User::factory()->create();
        $dataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Dataset',
            'source_filename' => 'data.csv',
            'row_count' => 1,
            'status' => 'completed',
        ]);
        $row = DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_index' => 1,
            'data' => ['month' => 'Jan', 'revenue' => '100'],
        ]);
        $upload = DatasetUpload::create([
            'dataset_id' => $dataset->id,
            'original_filename' => 'data.csv',
            'storage_path' => 'uploads/data.csv',
            'status' => 'pending',
        ]);
        $column = DatasetColumn::create([
            'dataset_id' => $dataset->id,
            'name' => 'month',
            'data_type' => 'string',
        ]);
        $visualization = Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => null,
            'dataset_id' => $dataset->id,
            'name' => 'Vis',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);

        $this->assertTrue($dataset->user->is($user));
        $this->assertTrue($dataset->rows->first()->is($row));
        $this->assertTrue($dataset->uploads->first()->is($upload));
        $this->assertTrue($dataset->columns->first()->is($column));
        $this->assertTrue($dataset->visualizations->first()->is($visualization));
    }

    public function test_dataset_upload_and_import_run_relationships_resolve_correctly(): void
    {
        $user = User::factory()->create();
        $dataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Dataset',
            'source_filename' => 'data.csv',
            'row_count' => 0,
            'status' => 'processing',
        ]);
        $upload = DatasetUpload::create([
            'dataset_id' => $dataset->id,
            'original_filename' => 'data.csv',
            'storage_path' => 'uploads/data.csv',
            'status' => 'pending',
        ]);
        $run = ImportRun::create([
            'dataset_upload_id' => $upload->id,
            'status' => 'in_progress',
            'rows_processed' => 10,
        ]);

        $this->assertTrue($upload->dataset->is($dataset));
        $this->assertTrue($upload->importRuns->first()->is($run));
        $this->assertTrue($run->datasetUpload->is($upload));
    }

    public function test_user_relationships_resolve_correctly(): void
    {
        $user = User::factory()->create();
        $dataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Dataset',
            'source_filename' => 'data.csv',
            'row_count' => 0,
            'status' => 'completed',
        ]);
        $dashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'default'],
        ]);
        $visualization = Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Vis',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);
        $theme = UserColorTheme::create([
            'user_id' => $user->id,
            'name' => 'Theme',
            'colors' => ['#111111', '#222222', '#333333'],
        ]);

        $this->assertTrue($user->datasets->first()->is($dataset));
        $this->assertTrue($user->dashboards->first()->is($dashboard));
        $this->assertTrue($user->visualizations->first()->is($visualization));
        $this->assertTrue($user->colorThemes->first()->is($theme));
    }

    public function test_json_casts_are_applied_for_model_attributes(): void
    {
        $user = User::factory()->create();
        $dataset = Dataset::create([
            'user_id' => $user->id,
            'name' => 'Dataset',
            'source_filename' => 'data.csv',
            'row_count' => 1,
            'status' => 'completed',
        ]);
        $dashboard = Dashboard::create([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'name' => 'Dashboard',
            'layout_config' => ['color_theme_mode' => 'builtin', 'color_theme' => 'forest'],
        ]);
        $row = DatasetRow::create([
            'dataset_id' => $dataset->id,
            'row_index' => 1,
            'data' => ['month' => 'Jan', 'revenue' => '100'],
        ]);
        $visualization = Visualization::create([
            'user_id' => $user->id,
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => 'Vis',
            'type' => 'bar',
            'config' => ['x_axis' => 'month', 'y_axis' => 'revenue', 'aggregation' => 'sum'],
        ]);
        $theme = UserColorTheme::create([
            'user_id' => $user->id,
            'name' => 'Theme',
            'colors' => ['#111111', '#222222', '#333333'],
        ]);

        $this->assertIsArray($dashboard->layout_config);
        $this->assertIsArray($row->data);
        $this->assertIsArray($visualization->config);
        $this->assertIsArray($theme->colors);
    }

    public function test_user_password_is_hashed_via_model_cast(): void
    {
        $user = User::create([
            'name' => 'Cast User',
            'email' => 'cast@example.com',
            'password' => 'plain-password',
        ]);

        $this->assertNotSame('plain-password', $user->password);
        $this->assertTrue(Hash::check('plain-password', $user->password));
    }
}
