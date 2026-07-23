<footer id="contact" class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 dark-transition">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Main Footer Content - 3 Columns -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">

            <!-- العمود الأول: الشعار + الوصف -->
            <div class="md:col-span-2">
                <div class="mb-4">
                    <img src="{{ asset('images/logo-en.png') }}" alt="AlMostashar Logo" class="h-10 w-auto">
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed max-w-[180px]">
                    {{ __('footer.description') }}
                </p>
            </div>

            <!-- العمود الثاني: روابط سريعة -->
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">{{ __('footer.quick_links') }}</h3>
                <ul class="space-y-3 text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            {{ __('footer.links.home') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('home') }}#careers" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            {{ __('footer.links.careers') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            {{ __('footer.links.about') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                            {{ __('footer.links.contact') }}
                        </a>
                    </li>
                </ul>
            </div>

            <!-- العمود الثالث: معلومات التواصل -->
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">{{ __('footer.contact_info') }}</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-envelope text-blue-500 dark:text-blue-400"></i>
                        <span>{{ __('footer.email') }}</span>
                    </li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-phone text-blue-500 dark:text-blue-400"></i>
                        <span>{{ __('footer.phone') }}</span>
                    </li>
                    <li class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
                        <i class="fas fa-map-marker-alt text-blue-500 dark:text-blue-400"></i>
                        <span>{{ __('footer.location') }}</span>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Bottom Bar: Copyright -->
        <div class="border-t border-gray-100 dark:border-gray-700 pt-8 flex justify-center items-center text-sm text-gray-500 dark:text-gray-400">
            <p class="text-center">{{ __('footer.copyright') }}</p>
        </div>
    </div>
</footer>
