<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            'aggregation' => 'required|in:sum,count,avg'
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
            'config' => [
                'x_axis' => $validated['x_axis'],
                'y_axis' => $validated['y_axis'],
                'aggregation' => $validated['aggregation']
            ]
        ]);

        return redirect()->route('dashboards.show', $dashboard)->with('success', 'Visualization added successfully!');
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
