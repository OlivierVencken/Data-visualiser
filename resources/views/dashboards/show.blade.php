<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $dashboard->name }} - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background min-h-screen antialiased text-gray-800">
    <nav class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <a href="/home" class="flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $dashboard->name }}</h1>
                <p class="text-xs text-gray-500">{{ $dashboard->description ?? 'No description' }}</p>
            </div>
        </div>
        
        <div class="flex flex-col items-end">
            <a href="#" class="inline-flex py-2 px-4 shadow-sm text-sm border-gray-300 font-medium rounded text-gray-700 bg-white hover:bg-gray-50">+ Add Visualization</a>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 py-12">
        @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-100 text-green-700 text-sm rounded-xl">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="grid grid-cols-1 gap-6">
            @if($dashboard->visualizations->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 border-dashed p-12 text-center shadow-sm">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Your dashboard is empty</h3>
                    <p class="text-gray-500 text-sm mx-auto my-4 max-w-sm">You have important data ready. Start building your view by adding visualizations from your datasets.</p>
                    <a href="#" class="inline-flex py-3 px-6 shadow-sm border border-transparent font-medium rounded-lg text-white bg-primary hover:bg-primary-hover">+ Create Visualization</a>
                </div>
            @else
                <div class="grid grid-cols-2 gap-4">
                    @foreach($dashboard->visualizations as $visualization)
                        <div class="bg-white p-6 shadow-md rounded-lg flex flex-col">
                            <h4 class="font-bold text-lg mb-2">{{ $visualization->title }}</h4>
                            <p class="text-sm text-gray-500">Dataset reference required: {{ $visualization->dataset_id }}</p>
                            <div class="mt-4 bg-gray-100 flex items-center justify-center h-48 rounded">
                                <span class="text-xs text-gray-400">Chart placeholder</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
</body>
</html>