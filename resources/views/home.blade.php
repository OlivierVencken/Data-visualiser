<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Visualizer - Home</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background min-h-screen antialiased text-gray-800">
    <!-- Navigation Bar -->
    <nav class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <span class="font-bold text-lg hidden sm:block">Data Visualizer</span>
        </div>
        
        <div class="flex items-center gap-6">
            <span class="text-sm font-medium text-gray-600">Hello, {{ auth()->user()->name }}</span>
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Log out</button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Your Dashboards</h1>
            
            <a href="/dashboards/create" class="inline-flex items-center px-4 py-2.5 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Dashboard
            </a>
        </div>

        @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-100 text-green-700 text-sm rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if($dashboards->isEmpty())
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-gray-100 border-dashed p-12 text-center shadow-sm">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No dashboards yet</h3>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">Get started by creating your first dashboard. You'll be able to import datasets and build custom visualizations.</p>
                
                <a href="/dashboards/create" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-hover text-white font-medium rounded-xl shadow-sm transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create your first dashboard
                </a>
            </div>
        @else
            <!-- Dashboard Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($dashboards as $dashboard)
                    <a href="/dashboards/{{ $dashboard->id }}" class="group block bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md hover:border-primary/30 transition-all text-left">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                <svg class="w-6 h-6 text-primary group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2.5 py-1 rounded-full">
                                {{ $dashboard->visualizations_count ?? 0 }} charts
                            </span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 group-hover:text-primary transition-colors">{{ $dashboard->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $dashboard->description ?? 'No description provided.' }}</p>
                        
                        <div class="flex items-center text-xs text-gray-400 pt-4 border-t border-gray-50">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Updated {{ $dashboard->updated_at->diffForHumans() }}
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>