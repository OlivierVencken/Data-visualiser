<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Dashboard - Data Visualizer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background min-h-screen antialiased text-gray-800">
    <nav class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
        <a href="/home" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
            <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-sm">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <span class="font-bold text-lg hidden sm:block">Data Visualizer</span>
        </a>
        
        <div class="flex items-center gap-6">
            <span class="text-sm font-medium text-gray-600">Hello, {{ auth()->user()->name }}</span>
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">Log out</button>
            </form>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto px-4 py-12">
        <div class="mb-8 flex items-center">
            <a href="/home" class="mr-4 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Create New Dashboard</h1>
        </div>

        @if(session('error'))
            <div class="mb-8 p-4 bg-red-50 border border-red-100 text-red-700 text-sm rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('dashboards.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Dashboard Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border" placeholder="e.g. Sales Q3 Report">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border" placeholder="What does this dashboard show?">{{ old('description') }}</textarea>
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Dataset</h3>
                    <p class="text-sm text-gray-500 mb-4">Start by importing your first dataset for this dashboard (CSV/TXT). You can add more later.</p>
                    
                    <div class="w-full">
                        <label for="csv_file" id="dropzone_label" class="flex flex-col items-center justify-center w-full min-h-[8rem] border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors overflow-hidden">
                            <!-- Default state -->
                            <div id="upload_prompt" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-3 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500"><span class="font-semibold text-primary">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-500">CSV or TXT files (MAX. 10MB)</p>
                            </div>
                            
                            <!-- Selected state -->
                            <div id="file_selected_info" class="flex-col items-center justify-center pt-5 pb-6 hidden w-full px-4 text-center">
                                <div class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-3 text-primary mx-auto">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p id="selected_filename" class="mb-1 text-sm font-medium text-gray-900 truncate w-full max-w-sm mx-auto"></p>
                                <p class="text-xs text-primary font-semibold mt-1">Click to change file</p>
                            </div>
                            
                            <input id="csv_file" name="csv_file" type="file" class="hidden" accept=".csv,.txt" required onchange="updateFileName(this)" />
                        </label>
                    </div>
                    @error('csv_file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-hover text-white font-medium rounded-xl shadow-sm transition-colors">
                    Upload & Create Dashboard
                </button>
            </div>
        </form>
    </main>

    <script>
        function updateFileName(input) {
            const prompt = document.getElementById('upload_prompt');
            const info = document.getElementById('file_selected_info');
            const filename = document.getElementById('selected_filename');
            const label = document.getElementById('dropzone_label');
            
            if (input.files && input.files.length > 0) {
                // Hide prompt, show file info
                prompt.classList.add('hidden');
                info.classList.remove('hidden');
                info.classList.add('flex');
                
                // Update filename text
                filename.textContent = input.files[0].name;
                
                // Change border/background to indicate success
                label.classList.add('border-primary', 'bg-primary-light', 'bg-opacity-10');
                label.classList.remove('border-gray-300', 'bg-gray-50');
            } else {
                // Revert to prompt
                prompt.classList.remove('hidden');
                info.classList.add('hidden');
                info.classList.remove('flex');
                
                // Reset text
                filename.textContent = '';
                
                // Revert styling
                label.classList.remove('border-primary', 'bg-primary-light', 'bg-opacity-10');
                label.classList.add('border-gray-300', 'bg-gray-50');
            }
        }
    </script>
</body>
</html>