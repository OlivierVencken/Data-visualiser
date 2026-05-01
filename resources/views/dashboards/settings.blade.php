<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Settings - {{ $dashboard->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background min-h-screen antialiased text-gray-800">
    <nav class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
        <a href="{{ route('dashboards.show', $dashboard) }}" class="flex items-center text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="font-bold text-lg hidden sm:block text-gray-900">Back to {{ $dashboard->name }}</span>
        </a>
    </nav>

    @if(session('success') || session('error'))
        <div class="fixed bottom-5 right-5 z-40 space-y-3 pointer-events-none">
            @if(session('success'))
                <div data-toast class="max-w-sm w-full bg-white border border-green-100 shadow-lg rounded-xl p-4 pointer-events-auto transition-all duration-300 ease-out opacity-0 translate-x-8">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5">
                            <div class="w-5 h-5 rounded-full bg-green-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Success</p>
                            <p class="text-sm text-gray-600 mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div data-toast class="max-w-sm w-full bg-white border border-red-100 shadow-lg rounded-xl p-4 pointer-events-auto transition-all duration-300 ease-out opacity-0 translate-x-8">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5">
                            <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">Error</p>
                            <p class="text-sm text-gray-600 mt-0.5">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <main class="max-w-3xl mx-auto px-4 py-12 space-y-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Settings</h1>
            <p class="text-gray-500 mt-2">Configure appearance and layout settings for this dashboard.</p>
        </div>

        <form action="{{ route('dashboards.settings.update', $dashboard) }}" method="POST" class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Theme Source</h2>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 text-sm text-gray-700">
                        <input type="radio" name="theme_mode" value="builtin" class="text-primary focus:ring-primary" @checked(old('theme_mode', $selectedThemeMode) === 'builtin')>
                        Use built-in theme
                    </label>
                    <label class="flex items-center gap-3 text-sm text-gray-700">
                        <input type="radio" name="theme_mode" value="custom" class="text-primary focus:ring-primary" @checked(old('theme_mode', $selectedThemeMode) === 'custom')>
                        Use my custom theme
                    </label>
                </div>
                @error('theme_mode')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div id="builtInThemeBlock">
                <label for="built_in_theme" class="block text-sm font-medium text-gray-700 mb-1">Built-in Theme</label>
                <select name="built_in_theme" id="built_in_theme" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border">
                    <option value="default" @selected(old('built_in_theme', $selectedBuiltInTheme) === 'default')>Default</option>
                    <option value="ocean" @selected(old('built_in_theme', $selectedBuiltInTheme) === 'ocean')>Ocean</option>
                    <option value="sunset" @selected(old('built_in_theme', $selectedBuiltInTheme) === 'sunset')>Sunset</option>
                    <option value="forest" @selected(old('built_in_theme', $selectedBuiltInTheme) === 'forest')>Forest</option>
                    <option value="mono" @selected(old('built_in_theme', $selectedBuiltInTheme) === 'mono')>Monochrome</option>
                </select>
                @error('built_in_theme')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div id="customThemeBlock">
                <label for="custom_theme_id" class="block text-sm font-medium text-gray-700 mb-1">Custom Theme</label>
                <select name="custom_theme_id" id="custom_theme_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border">
                    <option value="">Select one of your custom themes</option>
                    @foreach($customThemes as $theme)
                        <option value="{{ $theme->id }}" @selected((string) old('custom_theme_id', $selectedCustomThemeId) === (string) $theme->id)>{{ $theme->name }}</option>
                    @endforeach
                </select>
                @error('custom_theme_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-6 border-t border-gray-100">
                <label for="visualizations_per_row" class="block text-sm font-medium text-gray-700 mb-1">Visualizations per Row</label>
                <select name="visualizations_per_row" id="visualizations_per_row" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-gray-50 border">
                    @for($count = 1; $count <= 4; $count++)
                        <option value="{{ $count }}" @selected((int) old('visualizations_per_row', $selectedVisualizationsPerRow) === $count)>{{ $count }}</option>
                    @endfor
                </select>
                <p class="text-xs text-gray-500 mt-1">Choose how many visualizations fit next to each other on wide screens.</p>
                @error('visualizations_per_row')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-primary hover:bg-primary-hover text-white font-medium rounded-xl shadow-sm transition-colors">
                    Save Dashboard Settings
                </button>
            </div>
        </form>

        <section class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">My Custom Themes</h2>
                <button type="button" id="toggleNewThemeBtn" class="inline-flex items-center px-4 py-2 text-sm rounded-lg bg-primary hover:bg-primary-hover text-white font-medium transition-colors">
                    + Add New Theme
                </button>
            </div>
            <p class="text-sm text-gray-500">Manage your saved custom themes.</p>

            <form id="newThemeForm" action="{{ route('dashboards.settings.themes.store', $dashboard) }}" method="POST" class="hidden rounded-xl border border-gray-100 p-5 bg-gray-50 space-y-4">
                @csrf

                <div>
                    <label for="theme_name" class="block text-sm font-medium text-gray-700 mb-1">Theme Name</label>
                    <input type="text" name="theme_name" id="theme_name" value="{{ old('theme_name') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 py-2.5 px-4 bg-white border" placeholder="e.g. Corporate Brand">
                    @error('theme_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Theme Colors</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @for($i = 0; $i < 6; $i++)
                            <input type="color" name="colors[]" value="{{ old('colors.' . $i, ['#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444', '#0EA5E9'][$i]) }}" class="w-full h-12 rounded-lg border border-gray-300 bg-white p-1">
                        @endfor
                    </div>
                    @error('colors')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('colors.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="pt-1">
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-primary hover:bg-primary-hover text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        Save Custom Theme
                    </button>
                </div>
            </form>

            @if($customThemes->isEmpty())
                <p class="text-sm text-gray-500">No custom themes saved yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($customThemes as $theme)
                        <div class="flex items-center justify-between rounded-xl border border-gray-100 px-4 py-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $theme->name }}</p>
                                <div class="flex items-center gap-1 mt-1">
                                    @foreach(($theme->colors ?? []) as $color)
                                        <span class="inline-block w-4 h-4 rounded-full border border-gray-200" style="background-color: {{ $color }};"></span>
                                    @endforeach
                                </div>
                            </div>
                            <form action="{{ route('dashboards.settings.themes.destroy', [$dashboard, $theme]) }}" method="POST" onsubmit="return confirm('Delete this custom theme?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Delete custom theme" aria-label="Delete custom theme">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </main>

    <script>
        const themeModeInputs = document.querySelectorAll('input[name="theme_mode"]');
        const builtInThemeBlock = document.getElementById('builtInThemeBlock');
        const customThemeBlock = document.getElementById('customThemeBlock');
        const newThemeForm = document.getElementById('newThemeForm');
        const toggleNewThemeBtn = document.getElementById('toggleNewThemeBtn');
        const hasThemeValidationError = @json($errors->has('theme_name') || $errors->has('colors') || $errors->has('colors.*'));

        function syncThemeBlocks() {
            const selected = document.querySelector('input[name="theme_mode"]:checked')?.value || 'builtin';
            builtInThemeBlock.style.display = selected === 'builtin' ? 'block' : 'none';
            customThemeBlock.style.display = selected === 'custom' ? 'block' : 'none';
        }

        themeModeInputs.forEach(input => input.addEventListener('change', syncThemeBlocks));
        syncThemeBlocks();

        function toggleNewThemeForm(forceOpen = null) {
            const shouldOpen = forceOpen ?? newThemeForm.classList.contains('hidden');

            if (shouldOpen) {
                newThemeForm.classList.remove('hidden');
                toggleNewThemeBtn.textContent = 'Cancel';
            } else {
                newThemeForm.classList.add('hidden');
                toggleNewThemeBtn.textContent = '+ Add New Theme';
            }
        }

        toggleNewThemeBtn.addEventListener('click', () => toggleNewThemeForm());
        if (hasThemeValidationError) toggleNewThemeForm(true);

        document.querySelectorAll('[data-toast]').forEach((toast) => {
            setTimeout(() => toast.classList.remove('opacity-0', 'translate-x-8'), 10);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-x-8');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        });
    </script>
</body>
</html>
