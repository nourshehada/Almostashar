<nav class="sticky top-0 z-50 border-b border-blue-100 dark:border-gray-700 shadow-sm dark:shadow-lg dark-transition bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">

            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo-' . app()->getLocale() . '.png') }}"
                     alt="AlMostashar Logo"
                     class="h-10 w-auto"
                     id="logo-img">
             </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 font-medium transition dark:text-gray-200 dark:hover:text-blue-400">{{ __('home.nav.home') }}</a>
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-600 font-medium transition dark:text-gray-200 dark:hover:text-blue-400">{{ __('home.nav.about') }}</a>
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-blue-600 font-medium transition dark:text-gray-200 dark:hover:text-blue-400">{{ __('home.nav.contact') }}</a>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <a href="{{ route('locale.change', 'en') }}" class="px-3 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-primary text-white' : '' }}">EN</a>
                    <a href="{{ route('locale.change', 'ar') }}" class="px-3 py-1 rounded {{ app()->getLocale() === 'ar' ? 'bg-primary text-white' : '' }}">عربي</a>
                </div>
                <div x-data="{ open: false }" class="md:hidden">
                    <button
                        @click="open = !open"
                        class="text-gray-700 dark:text-gray-200">
                        <i x-show="!open" class="fas fa-bars text-2xl"></i>
                        <i x-show="open" class="fas fa-xmark text-2xl"></i>
                    </button>

                    <div
                        x-show="open"
                        @click.outside="open = false"
                        class="absolute top-full left-0 w-full bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">

                        <div class="px-4 py-4 space-y-3">
                            <a href="{{ route('home') }}" class="block py-2">
                                {{ __('home.nav.home') }}
                            </a>

                            <a href="{{ route('about') }}" class="block py-2">
                                {{ __('home.nav.about') }}
                            </a>

                            <a href="{{ route('contact') }}" class="block py-2">
                                {{ __('home.nav.contact') }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
