<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Visualizer - Welcome</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background min-h-screen antialiased text-gray-800">
    @auth
        <!-- Header -->
        <div class="fixed top-0 right-0 p-6 flex items-center gap-4">
            <span class="text-sm font-medium">Logged in</span>
            <form action="/logout" method="POST" class="m-0">
                @csrf
                <button class="text-sm font-medium text-gray-500 hover:text-gray-900">Log out</button>
            </form>
        </div>
    @endauth

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16 flex flex-col items-center text-center">
        <!-- Logo Icon -->
        <div class="w-24 h-24 bg-primary rounded-3xl flex items-center justify-center mb-8 shadow-xl shadow-primary/20">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>

        <!-- Hero Text -->
        <h1 class="text-5xl md:text-6xl font-bold mb-6 tracking-tight text-blue-600">
            Data Visualizer
        </h1>
        <p class="mt-4 text-xl text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
            Transform your data into beautiful, interactive visualizations. Analyze trends, discover insights, and make data-driven decisions with ease.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 mb-24">
            <a href="{{ auth()->check() ? url('/home') : url('/login') }}" class="inline-flex justify-center items-center px-8 py-3.5 border border-transparent text-base font-medium rounded-lg text-white bg-primary hover:bg-primary-hover shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <svg class="w-5 h-5 mr-2 -ml-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                {{ auth()->check() ? 'Go to Home' : 'Get Started' }}
            </a>
        </div>

        <!-- Feature Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-5xl text-left">
            <!-- Line Charts Card -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-primary-light rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Line Charts</h3>
                <p class="text-gray-500 leading-relaxed text-sm">
                    Track trends over time with smooth, interactive line charts. Perfect for time-series data and historical analysis.
                </p>
            </div>

            <!-- Pie & Donut Charts Card -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-accent-purple-light rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-accent-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Pie & Donut Charts</h3>
                <p class="text-gray-500 leading-relaxed text-sm">
                    Visualize proportions and distributions with elegant pie and donut charts. Compare segments at a glance.
                </p>
            </div>

            <!-- Bar Charts Card -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-accent-green-light rounded-xl flex items-center justify-center mb-6">
                    <svg class="w-6 h-6 text-accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Bar Charts</h3>
                <p class="text-gray-500 leading-relaxed text-sm">
                    Compare categories and values with customizable bar charts. Support for grouped and stacked data.
                </p>
            </div>
        </div>
    </main>
</body>
</html>