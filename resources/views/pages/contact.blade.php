@extends('layouts.app')

@section('title', __('contact.title'))

@section('content')

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 md:px-10">

        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ __('contact.page_title') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                {{ __('contact.page_subtitle') }}
            </p>
        </div>

        <!-- Contact Section -->
        <div class="grid lg:grid-cols-3 gap-8 mb-12">

            <!-- Contact Info Cards (Left Side) -->
            <div class="space-y-6">
                <!-- Email Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-envelope text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ __('contact.email_us') }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('contact.email_response_time') }}</p>
                    <a href="mailto:{{ __('contact.email_address') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                        {{ __('contact.email_address') }}
                    </a>
                </div>

                <!-- Phone Card -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-phone text-green-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ __('contact.call_us') }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ __('contact.call_hours') }}</p>
                    <a href="tel:{{ __('contact.call_number') }}" class="text-green-600 hover:text-green-700 font-medium">
                        {{ __('contact.call_number') }}
                    </a>
                </div>

                <!-- Social Media Card -->
                <div class="bg-blue-600 rounded-2xl p-6 text-white">
                    <h3 class="font-bold mb-4">{{ __('contact.follow_us') }}</h3>
                    <div class="flex gap-3">
                        <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form (Right Side) -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700 shadow-lg">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                        {{ __('contact.form_title') }}
                    </h2>

                    <form action="" method="GET" class="space-y-6">
                        @csrf

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('contact.full_name') }} *
                                </label>
                                <input
                                    type="text"
                                    id="full_name"
                                    name="full_name"
                                    required
                                    placeholder="{{ __('contact.full_name_placeholder') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                >
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('contact.email_address') }} *
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    required
                                    placeholder="{{ __('contact.email_placeholder') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                >
                            </div>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('contact.subject') }} *
                            </label>
                            <select
                                id="subject"
                                name="subject"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                            >
                                <option value="">{{ __('contact.select_subject') }}</option>
                                @foreach(__('contact.subjects') as $key => $subject)
                                    <option value="{{ $key }}">{{ $subject }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('contact.message') }} *
                            </label>
                            <textarea
                                id="message"
                                name="message"
                                rows="5"
                                required
                                placeholder="{{ __('contact.message_placeholder') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-y"
                            ></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 rounded-xl transition shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-paper-plane"></i>
                            {{ __('contact.send_message') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
