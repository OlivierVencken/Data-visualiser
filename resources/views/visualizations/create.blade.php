<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Visualization - {{ $dashboard->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background min-h-screen antialiased text-gray-800">
    <nav class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
        <a href="{{ route('dashboards.show', $dashboard) }}" class="flex items-center text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-bold text-lg hidden sm:block text-gray-900">Back to {{ $dashboard->name }}</span>
        </a>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-12">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Add Visualization</h1>
            <p class="text-gray-500 mt-2">Build a new chart for this dashboard.</p>
        </div>

        @if(session('error'))
            <div class="mb-8 p-4 bg-red-50 border border-red-100 text-red-700 text-sm rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('dashboards.visualizations.store', $dashboard) }}" method="POST" class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
            @csrf
            
            <div class="space-y-6">
                <!-- Data Source -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">1. Data Source</h3>
                    
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dashboard Dataset</label>
                    <div class="w-full rounded-lg py-2.5 px-4 bg-gray-50 border border-gray-300 text-gray-700">
                        {{ $dashboardDataset->name }}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">This dashboard can only use its own imported dataset.</p>
                </div>

                <!-- Chart Settings -->
                <div class="pt-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">2. Chart Settings</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Chart Title</label>
                            <input type="text" name="name" id="name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border" placeholder="e.g. Monthly Revenue">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Chart Type</label>
                            <select name="type" id="type" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border">
                                <option value="bar">Bar Chart</option>
                                <option value="line">Line Chart</option>
                                <option value="pie">Pie Chart</option>
                                <option value="doughnut">Donut Chart</option>
                            </select>
                            @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="aggregation" class="block text-sm font-medium text-gray-700 mb-1">Aggregation</label>
                            <select name="aggregation" id="aggregation" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border">
                                <option value="sum">Sum</option>
                                <option value="avg">Average</option>
                                <option value="count">Count Rows</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">How to combine Y values for the same X</p>
                        </div>

                        <div>
                            <label for="x_axis" class="block text-sm font-medium text-gray-700 mb-1">X-Axis (Labels)</label>
                            <select name="x_axis" id="x_axis" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border">
                                @foreach($columns as $column)
                                    <option value="{{ $column }}" @selected(old('x_axis') === $column)>{{ $column }}</option>
                                @endforeach
                            </select>
                            @error('x_axis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="y_axis" class="block text-sm font-medium text-gray-700 mb-1">Y-Axis (Values)</label>
                            <select name="y_axis" id="y_axis" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border">
                                @foreach($columns as $column)
                                    <option value="{{ $column }}" @selected(old('y_axis') === $column)>{{ $column }}</option>
                                @endforeach
                            </select>
                            @error('y_axis')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <a href="{{ route('dashboards.show', $dashboard) }}" class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl shadow-sm transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-hover text-white font-medium rounded-xl shadow-sm transition-colors">
                    Save Visualization
                </button>
            </div>
        </form>
    </main>
</body>
</html>