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
                        <button type="button" data-toast-close class="text-gray-400 hover:text-gray-600 transition-colors" aria-label="Close notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
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
                        <button type="button" data-toast-close class="text-gray-400 hover:text-gray-600 transition-colors" aria-label="Close notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @endif

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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($dashboards as $dashboard)
                    <div class="group relative bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md hover:border-primary/30 transition-all text-left block">
                        <a href="/dashboards/{{ $dashboard->id }}" class="absolute inset-0 z-0 rounded-2xl"></a>
                        
                        <div class="flex justify-between items-start mb-4 relative z-10 pointer-events-none">
                            <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                <svg class="w-6 h-6 text-primary group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </div>
                            <div class="flex items-center gap-2 pointer-events-auto">
                                <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2.5 py-1 rounded-full">
                                    {{ $dashboard->visualizations_count ?? 0 }} charts
                                </span>
                                <form id="delete-form-{{ $dashboard->id }}" action="{{ route('dashboards.destroy', $dashboard) }}" method="POST" class="m-0 hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" onclick="openDeleteModal('{{ $dashboard->id }}', '{{ htmlspecialchars($dashboard->name, ENT_QUOTES) }}')" class="text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50 pointer-events-auto" title="Delete Dashboard">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 mb-1 group-hover:text-primary transition-colors relative z-0 pointer-events-none">{{ $dashboard->name }}</h3>
                        <p class="text-sm text-gray-500 mb-4 line-clamp-2 relative z-0 pointer-events-none">{{ $dashboard->description ?? 'No description provided.' }}</p>
                        
                        <div class="flex items-center text-xs text-gray-400 pt-4 border-t border-gray-50 relative z-0 pointer-events-none">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Updated {{ $dashboard->updated_at->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <!-- Delete Confirmation -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div id="modalOverlay" class="fixed inset-0 bg-gray-900/30 backdrop-blur-[2px] opacity-0 transition-opacity duration-300 ease-out" aria-hidden="true" onclick="closeDeleteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div id="modalPanel" class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 transition-all duration-300 ease-out sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-10 border border-gray-100">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-50 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">Delete Dashboard</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to delete <span id="deleteDashboardName" class="font-bold text-gray-800"></span>? All data and visualizations will be permanently removed. This cannot be undone.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button type="button" id="confirmDeleteBtn" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:w-auto sm:text-sm transition-colors">
                        Delete
                    </button>
                    <button type="button" onclick="closeDeleteModal()" class="mt-3 sm:mt-0 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:w-auto sm:text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-toast]').forEach((toast) => {
            setTimeout(() => {
                toast.classList.remove('opacity-0', 'translate-x-8');
            }, 10);

            const hideToast = () => {
                toast.classList.add('opacity-0', 'translate-x-8');
                setTimeout(() => toast.remove(), 300);
            };

            toast.querySelector('[data-toast-close]')?.addEventListener('click', hideToast);
            setTimeout(hideToast, 5000);
        });

        let currentDeleteFormId = null;

        function openDeleteModal(id, name) {
            currentDeleteFormId = 'delete-form-' + id;
            document.getElementById('deleteDashboardName').textContent = name;
            
            const modal = document.getElementById('deleteModal');
            const overlay = document.getElementById('modalOverlay');
            const panel = document.getElementById('modalPanel');
            
            modal.classList.remove('hidden');
            
            // Trigger animation
            setTimeout(() => {
                overlay.classList.remove('opacity-0');
                overlay.classList.add('opacity-100');
                
                panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const overlay = document.getElementById('modalOverlay');
            const panel = document.getElementById('modalPanel');
            
            // Reverse animation
            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');
            
            panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
            panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                currentDeleteFormId = null;
            }, 300); // Wait for transition
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentDeleteFormId) {
                document.getElementById(currentDeleteFormId).submit();
            }
        });
    </script>
</body>
</html>