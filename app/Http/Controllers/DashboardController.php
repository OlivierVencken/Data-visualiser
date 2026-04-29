<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dashboard;
use App\Models\Dataset;
use App\Models\DatasetRow;

class DashboardController extends Controller
{
    public function create()
    {
        return view('dashboards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'csv_file' => 'required|file|mimes:csv,txt|max:10240' // Max 10MB
        ]);

        // Process CSV
        $file = $request->file('csv_file');
        $fileName = $file->getClientOriginalName();

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            if (!$header) {
                return back()->withInput()->with('error', 'Invalid CSV format.');
            }
            
            $dataset = Dataset::create([
                'user_id' => auth()->id(),
                'name' => 'Data for ' . $request->name,
                'source_filename' => $fileName,
                'status' => 'processing'
            ]);

            $records = [];
            $rowIndex = 1;
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($header) === count($row)) {
                    $rowData = array_combine($header, $row);
                    $records[] = [
                        'dataset_id' => $dataset->id,
                        'row_index' => $rowIndex++,
                        'data' => json_encode($rowData),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
            fclose($handle);

            if (!empty($records)) {
                foreach (array_chunk($records, 1000) as $chunk) {
                    DatasetRow::insert($chunk);
                }
                
                $dataset->update([
                    'row_count' => $rowIndex - 1,
                    'status' => 'completed'
                ]);
            } else {
                $dataset->delete();
                return back()->withInput()->with('error', 'CSV file appears to be empty or misformatted.');
            }
        } else {
            return back()->withInput()->with('error', 'Failed to read the file.');
        }

        $dashboard = Dashboard::create([
            'user_id' => auth()->id(),
            'dataset_id' => $dataset->id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('dashboards.show', $dashboard)->with('success', 'Dashboard created successfully! You can now add visualizations.');
    }

    public function show(Dashboard $dashboard)
    {
        // Ensure user owns this dashboard
        if ($dashboard->user_id !== auth()->id()) {
            abort(403);
        }
        
        $dashboard->load(['visualizations', 'dataset']);

        if (!$dashboard->dataset || $dashboard->dataset->user_id !== auth()->id()) {
            return redirect()->route('home')->with('error', 'The dataset linked to this dashboard could not be found.');
        }
        
        $visualizationsData = [];
        foreach ($dashboard->visualizations as $vis) {
            $config = is_string($vis->config) ? json_decode($vis->config, true) : $vis->config;
            $x_axis = $config['x_axis'] ?? '';
            $y_axis = $config['y_axis'] ?? '';
            $agg = $config['aggregation'] ?? 'sum';
            
            // For now only let a dashboard visualize its own linked dataset
            if ((int) $vis->dataset_id !== (int) $dashboard->dataset_id) {
                continue;
            }

            $rows = DatasetRow::where('dataset_id', $dashboard->dataset_id)->get();
            
            $groups = [];
            foreach ($rows as $row) {
                $data = is_string($row->data) ? json_decode($row->data, true) : $row->data;

                $x_val = $data[$x_axis] ?? 'Unknown';
                // Strip out currency/commas
                $y_val_raw = $data[$y_axis] ?? 0;
                $y_val = (float) preg_replace('/[^0-9.-]/', '', (string)$y_val_raw);
                
                if (!isset($groups[$x_val])) {
                    $groups[$x_val] = ['sum' => 0, 'count' => 0];
                }
                
                $groups[$x_val]['sum'] += $y_val;
                $groups[$x_val]['count'] += 1;
            }
            
            // Limit to 50 items
            $groups = array_slice($groups, 0, 50, true);
            
            $labels = [];
            $values = [];
            
            foreach ($groups as $x => $stats) {
                $labels[] = (string)$x;
                if ($agg === 'avg') {
                    $values[] = $stats['count'] > 0 ? round($stats['sum'] / $stats['count'], 2) : 0;
                } elseif ($agg === 'count') {
                    $values[] = $stats['count'];
                } else {
                    $values[] = round($stats['sum'], 2);
                }
            }
            
            $visualizationsData[] = [
                'id' => $vis->id,
                'name' => $vis->name,
                'type' => strtolower($vis->type),
                'labels' => $labels,
                'values' => $values,
            ];
        }

        return view('dashboards.show', compact('dashboard', 'visualizationsData'));
    }

    public function destroy(Dashboard $dashboard)
    {
        // Ensure user owns this dashboard
        if ($dashboard->user_id !== auth()->id()) {
            abort(403);
        }

        $dashboard->delete();

        return redirect()->route('home')->with('success', 'Dashboard deleted successfully.');
    }
}
