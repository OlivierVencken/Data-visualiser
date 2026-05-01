<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Dashboard;
use App\Models\Dataset;
use App\Models\Visualization;

class VisualizationController extends Controller
{
    public function create(Request $request, Dashboard $dashboard)
    {
        // Ensure user owns this dashboard
        if ($dashboard->user_id !== auth()->id()) {
            abort(403);
        }

        $dashboardDataset = Dataset::where('id', $dashboard->dataset_id)
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->first();

        if (!$dashboardDataset) {
            return redirect()->route('dashboards.show', $dashboard)->with('error', 'No completed dataset found for this dashboard.');
        }

        $firstRow = $dashboardDataset->rows()->first();
        $columns = $firstRow && is_array($firstRow->data) ? array_keys($firstRow->data) : [];

        return view('visualizations.create', compact('dashboard', 'dashboardDataset', 'columns'));
    }

    public function store(Request $request, Dashboard $dashboard)
    {
        // Ensure user owns this dashboard
        if ($dashboard->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:line,bar,pie,doughnut',
            'x_axis' => 'required|string',
            'y_axis' => 'required|string',
            'aggregation' => 'required|in:sum,count,avg',
            'color_override' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/']
        ]);

        $dataset = Dataset::where('id', $dashboard->dataset_id)
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->first();

        if (!$dataset) {
            return back()->withInput()->withErrors([
                'name' => 'The dataset for this dashboard is not available.',
            ]);
        }

        Visualization::create([
            'user_id' => auth()->id(),
            'dashboard_id' => $dashboard->id,
            'dataset_id' => $dataset->id,
            'name' => $validated['name'],
            'type' => $validated['type'],
            'position' => Visualization::where('dashboard_id', $dashboard->id)->max('position') + 1,
            'config' => [
                'x_axis' => $validated['x_axis'],
                'y_axis' => $validated['y_axis'],
                'aggregation' => $validated['aggregation'],
                'color_override' => $validated['color_override'] ?? null,
            ]
        ]);

        return redirect()->route('dashboards.show', $dashboard)->with('success', 'Visualization added successfully!');
    }

    public function edit(Dashboard $dashboard, Visualization $visualization)
    {
        if ($dashboard->user_id !== auth()->id() || $visualization->dashboard_id !== $dashboard->id || $visualization->user_id !== auth()->id()) {
            abort(403);
        }

        $dashboardDataset = Dataset::where('id', $dashboard->dataset_id)
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->first();

        if (!$dashboardDataset || (int) $visualization->dataset_id !== (int) $dashboardDataset->id) {
            return redirect()->route('dashboards.show', $dashboard)->with('error', 'The visualization dataset is not available.');
        }

        $firstRow = $dashboardDataset->rows()->first();
        $columns = $firstRow && is_array($firstRow->data) ? array_keys($firstRow->data) : [];
        $config = is_array($visualization->config) ? $visualization->config : [];

        return view('visualizations.edit', compact('dashboard', 'dashboardDataset', 'columns', 'visualization', 'config'));
    }

    public function update(Request $request, Dashboard $dashboard, Visualization $visualization)
    {
        if ($dashboard->user_id !== auth()->id() || $visualization->dashboard_id !== $dashboard->id || $visualization->user_id !== auth()->id()) {
            abort(403);
        }

        $dataset = Dataset::where('id', $dashboard->dataset_id)
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->first();

        if (!$dataset || (int) $visualization->dataset_id !== (int) $dataset->id) {
            return back()->withInput()->withErrors([
                'name' => 'The dataset for this visualization is not available.',
            ]);
        }

        $firstRow = $dataset->rows()->first();
        $columns = $firstRow && is_array($firstRow->data) ? array_keys($firstRow->data) : [];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:line,bar,pie,doughnut',
            'x_axis' => ['required', 'string', Rule::in($columns)],
            'y_axis' => ['required', 'string', Rule::in($columns)],
            'aggregation' => 'required|in:sum,count,avg',
            'color_override' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $visualization->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'config' => [
                'x_axis' => $validated['x_axis'],
                'y_axis' => $validated['y_axis'],
                'aggregation' => $validated['aggregation'],
                'color_override' => $validated['color_override'] ?? null,
            ],
        ]);

        return redirect()->route('dashboards.show', $dashboard)->with('success', 'Visualization updated successfully!');
    }

    public function reorder(Request $request, Dashboard $dashboard)
    {
        if ($dashboard->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'visualization_ids' => 'required|array',
            'visualization_ids.*' => 'required|integer',
        ]);

        $ids = array_map('intval', $validated['visualization_ids']);
        $uniqueIds = array_values(array_unique($ids));
        if ($ids !== $uniqueIds) {
            return response()->json([
                'message' => 'The visualization order contains a duplicate visualization.',
            ], 422);
        }

        $dashboardVisualizationIds = Visualization::where('dashboard_id', $dashboard->id)
            ->where('user_id', auth()->id())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $invalidIds = array_diff($ids, $dashboardVisualizationIds);

        if (!empty($invalidIds)) {
            return response()->json([
                'message' => 'The visualization order contains an invalid visualization.',
            ], 422);
        }

        $orderedIds = array_merge($ids, array_values(array_diff($dashboardVisualizationIds, $ids)));

        foreach ($orderedIds as $position => $id) {
            Visualization::where('id', $id)
                ->where('dashboard_id', $dashboard->id)
                ->where('user_id', auth()->id())
                ->update(['position' => $position + 1]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Dashboard $dashboard, Visualization $visualization)
    {
        if ($dashboard->user_id !== auth()->id() || $visualization->dashboard_id !== $dashboard->id || $visualization->user_id !== auth()->id()) {
            abort(403);
        }

        $visualization->delete();
        return back()->with('success', 'Visualization removed.');
    }
}
