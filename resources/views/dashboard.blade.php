<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Dashboard</h1>
    
    @if(session('success'))
        <div style="color: green; margin-bottom: 20px;">
            <strong>{{ session('success') }}</strong>
        </div>
    @endif
    
    @if(session('error') || $errors->any())
        <div style="color: red; margin-bottom: 20px;">
            <strong>{{ session('error') ?? $errors->first() }}</strong>
        </div>
    @endif

    <h2>Import Data</h2>
    <form action="/import" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="csv_file">Upload CSV file only:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
        <br><br>
        <button type="submit">Import Data</button>
    </form>

    <hr>

    @if(isset($datasets) && count($datasets) > 0)
        <h2>Your Imported Datasets</h2>
        @foreach($datasets as $dataset)
            <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                <h3>{{ $dataset->name }} ({{ $dataset->row_count }} rows)</h3>
                <p>Status: {{ $dataset->status }} | Source: {{ $dataset->source_filename }} | Uploaded: {{ $dataset->created_at->diffForHumans() }}</p>
                
                @php
                    $previewRows = $dataset->rows->take(50);
                @endphp

                @if(count($previewRows) > 0)
                    <h4>Preview (First {{ count($previewRows) }} rows)</h4>
                    <table border="1" cellpadding="5" cellspacing="0" style="margin-bottom: 20px;">
                        <thead style="background-color: #f2f2f2;">
                            <tr>
                                <th>#</th>
                                @foreach(array_keys($previewRows->first()->data) as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewRows as $row)
                                <tr>
                                    <td>{{ $row->row_index }}</td>
                                    @foreach($row->data as $cell)
                                        <td>{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    @else
        <p>No data imported yet.</p>
    @endif

    <hr>
    
    <form action="/logout" method="POST">
        @csrf
        <button>Log out</button>
    </form>
</body>
</html>