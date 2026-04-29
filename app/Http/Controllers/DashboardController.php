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
        
        $dashboard->load('visualizations');
        $datasets = Dataset::where('user_id', auth()->id())->get();

        return view('dashboards.show', compact('dashboard', 'datasets'));
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
