<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dataset;
use App\Models\DatasetRow;

class DataController extends Controller
{
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240' // Max 10MB
        ]);

        $file = $request->file('csv_file');
        $fileName = $file->getClientOriginalName();

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 1000, ',');
            if (!$header) {
                return redirect('/home')->with('error', 'Invalid CSV format.');
            }
            
            $dataset = Dataset::create([
                'user_id' => auth()->id(),
                'name' => 'Imported from ' . $fileName,
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
                
                return redirect('/home')->with('success', 'Dataset imported successfully!');
            }
            
            $dataset->delete(); // Clean up if empty
            return redirect('/home')->with('error', 'CSV file appears to be empty or misformatted.');
        }

        return redirect('/home')->with('error', 'Failed to read the file.');
    }
}
