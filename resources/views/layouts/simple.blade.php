<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AlMostashar')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="{{ asset('js/prevent-back.js') }}"></script>

    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        cairo: ['Cairo', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#2563eb',
                        'primary-dark': '#1d4ed8',
                        'primary-light': '#dbeafe',
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 text-gray-800 dark-transition dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 dark:text-gray-100">
    <nav class="sticky top-0 z-50 border-b border-blue-100 dark:border-gray-700 shadow-sm dark:shadow-lg dark-transition bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-' . app()->getLocale() . '.png') }}"
                         alt="AlMostashar Logo"
                         class="h-10 w-auto"
                         id="logo-img">
                </div>

                <div class="flex items-center gap-3">
                    @if(session('user_name'))
                        <span class="text-gray-700 hover:text-blue-600 font-medium transition dark:text-gray-200 dark:hover:text-blue-400">
                            {{ session('user_name') }}
                        </span>
                    @else
                        <span class="text-gray-700 hover:text-blue-600 font-medium transition dark:text-gray-200 dark:hover:text-blue-400">
                            {{ __('home.guest') }}
                        </span>
                    @endif

                    <div class="w-px h-6 bg-gray-300 dark:bg-gray-600"></div>

                    <a href="{{ route('home') }}"
                       class="text-blue-600 dark:text-gray-400 hover:text-blue-700 dark:hover:text-blue-400 transition p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                       title="{{ __('home.nav.home') }}">
                        <i class="fas fa-home text-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border-t border-gray-200 dark:border-gray-700 py-6 mt-auto">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center space-y-3">

                <p class="text-gray-600 dark:text-gray-400 text-xs leading-relaxed">
                    {{ __('home.footer.disclaimer') }}
                </p>

                <div class="flex items-center justify-center gap-3">
                    <div class="h-px w-8 bg-gray-300 dark:bg-gray-600"></div>
                    <span class="text-gray-400 dark:text-gray-500 text-xs">•</span>
                    <div class="h-px w-8 bg-gray-300 dark:bg-gray-600"></div>
                </div>

                <p class="text-gray-600 dark:text-gray-300 text-sm font-medium">
                    Made with <span class="text-red-500 animate-pulse">❤️</span> by
                    <span class="text-blue-600 dark:text-blue-400 font-bold">AlMostashar</span>
                </p>

            </div>
        </div>
    </footer>

    @stack('chatbot')
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
