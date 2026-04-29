<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $dashboard->name }} - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-background min-h-screen antialiased text-gray-800">
    <nav class="bg-white border-b border-gray-100 px-6 py-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
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
            <a href="{{ route('dashboards.visualizations.create', $dashboard) }}" class="inline-flex py-2 px-4 shadow-sm text-sm border-gray-300 font-medium rounded-lg text-white bg-primary hover:bg-primary-hover transition-colors">+ Add Visualization</a>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-4 py-12">
        @if(session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-100 text-green-700 text-sm rounded-xl">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="grid grid-cols-1 gap-8">
            @if($dashboard->visualizations->isEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 border-dashed p-12 text-center shadow-sm">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Your dashboard is empty</h3>
                    <p class="text-gray-500 text-sm mx-auto my-4 max-w-sm">You have important data ready. Start building your view by adding visualizations from your datasets.</p>
                    <a href="{{ route('dashboards.visualizations.create', $dashboard) }}" class="inline-flex py-3 px-6 shadow-sm border border-transparent font-medium rounded-lg text-white bg-primary hover:bg-primary-hover">+ Create Visualization</a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    @foreach($visualizationsData as $idx => $vis)
                        <div class="relative group bg-white p-6 shadow-sm border border-gray-100 rounded-2xl flex flex-col h-96">
                            
                            <form id="delete-vis-form-{{ $vis['id'] }}" action="{{ route('dashboards.visualizations.destroy', [$dashboard->id, $vis['id']]) }}" method="POST" class="m-0 hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button" onclick="openDeleteModal('{{ $vis['id'] }}', '{{ htmlspecialchars($vis['name'], ENT_QUOTES) }}')" class="absolute top-4 right-4 m-0 opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50" title="Delete Chart">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>

                            <h4 class="font-bold text-lg mb-4 text-gray-900 pr-10">{{ $vis['name'] }}</h4>
                            <div class="flex-1 w-full relative min-h-0 pb-2">
                                <canvas id="chart-{{ $idx }}"></canvas>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

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
                        <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">Delete Visualization</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Are you sure you want to delete <span id="deleteVisualizationName" class="font-bold text-gray-800"></span>? This cannot be undone.</p>
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

    @if(!$dashboard->visualizations->isEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const visualizations = @json($visualizationsData);
            
            // Re-usable color palette matching Tailwind's primary & accent colors
            const baseColors = [
                'rgba(59, 130, 246, 0.8)', // Primary blue
                'rgba(16, 185, 129, 0.8)', // Emerald
                'rgba(139, 92, 246, 0.8)', // Violet
                'rgba(245, 158, 11, 0.8)', // Amber
                'rgba(239, 68, 68, 0.8)',  // Red
                'rgba(14, 165, 233, 0.8)', // Sky
                'rgba(236, 72, 153, 0.8)', // Pink
            ];

            visualizations.forEach((vis, index) => {
                const canvas = document.getElementById('chart-' + index);
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                
                let bgColors = baseColors[0];
                let isPieType = vis.type === 'pie' || vis.type === 'doughnut';
                let isLineType = vis.type === 'line';
                
                // If it's a pie/doughnut, we need multiple colors for the slices
                if (isPieType) {
                    bgColors = vis.labels.map((_, i) => baseColors[i % baseColors.length]);
                }
                
                new Chart(ctx, {
                    type: vis.type,
                    data: {
                        labels: vis.labels,
                        datasets: [{
                            label: vis.name,
                            data: vis.values,
                            backgroundColor: isLineType ? 'rgba(59, 130, 246, 0.15)' : bgColors,
                            borderWidth: 1,
                            borderColor: isLineType ? 'rgba(59, 130, 246, 1)' : '#ffffff',
                            borderRadius: isPieType ? 0 : 4,
                            tension: isLineType ? 0.25 : 0,
                            fill: isLineType ? false : undefined,
                            showLine: isLineType ? true : undefined,
                            pointRadius: isLineType ? 3 : undefined,
                            pointHoverRadius: isLineType ? 5 : undefined,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: isPieType,
                                position: 'right'
                            },
                            tooltip: {
                                padding: 12,
                                cornerRadius: 8
                            }
                        },
                        scales: isPieType ? {} : {
                            y: { 
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: 'rgba(0,0,0,0.05)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            });
        });

        let currentDeleteFormId = null;

        function openDeleteModal(id, name) {
            currentDeleteFormId = 'delete-vis-form-' + id;
            document.getElementById('deleteVisualizationName').textContent = name;

            const modal = document.getElementById('deleteModal');
            const overlay = document.getElementById('modalOverlay');
            const panel = document.getElementById('modalPanel');

            modal.classList.remove('hidden');

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

            overlay.classList.remove('opacity-100');
            overlay.classList.add('opacity-0');

            panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
            panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                currentDeleteFormId = null;
            }, 300);
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentDeleteFormId) {
                document.getElementById(currentDeleteFormId).submit();
            }
        });
    </script>
    @endif
</body>
</html>