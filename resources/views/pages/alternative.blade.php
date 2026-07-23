@extends('layouts.simple')

@section('title', __('alternative.title', ['major' => $majorName]))

@section('content')

@php
    $hasCachedData = !empty($cachedData);
    $alternatives = $attempt->ai_result['alternatives'] ?? [];
    $originalMatch = $alternatives[$alternativeIndex]['match'] ?? 0;
    $originalReason = $alternatives[$alternativeIndex]['reason'] ?? '';

    // ✅ تمرير الترجمات إلى JavaScript
    $translations = [
        'loading' => [
            'title' => __('alternative.loading.title', ['major' => $majorName]),
            'subtitle' => __('alternative.loading.subtitle'),
        ],
        'error' => [
            'title' => __('alternative.error.title'),
            'retry' => __('alternative.error.retry'),
            'connection' => __('alternative.error.connection'),
            'unexpected' => __('alternative.error.unexpected'),
        ],
        'back' => __('alternative.back'),
        'header' => [
            'label' => __('alternative.header.label'),
            'match' => __('alternative.header.match'),
        ],
        'market' => [
            'opportunities' => __('alternative.market.opportunities'),
            'salary' => __('alternative.market.salary'),
            'demand' => __('alternative.market.demand'),
        ],
        'why' => __('alternative.why'),
        'study' => [
            'title' => __('alternative.study.title'),
        ],
        'work' => [
            'title' => __('alternative.work.title'),
        ],
        'skills' => __('alternative.skills'),
        'roadmap' => [
            'title' => __('alternative.roadmap.title'),
            'month' => __('alternative.roadmap.month'),
        ],
        'actions' => [
            'retake' => __('alternative.actions.retake'),
        ],
    ];
@endphp

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-5xl mx-auto px-4">

        {{-- زر الرجوع --}}
        <div class="mb-6">
            <a href="{{ route('results.show', $attempt->id) }}"
               class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }}"></i>
                {{ $translations['back'] }}
            </a>
        </div>

        {{-- شاشة التحميل --}}
        <div id="loading" class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-12 text-center">
            <div class="relative mb-8 inline-block">
                <div class="w-24 h-24 border-4 border-blue-100 rounded-full relative">
                    <div class="absolute inset-0 border-4 border-transparent border-t-blue-500 border-r-blue-400 rounded-full animate-spin" style="animation-duration: 2s;"></div>
                    <div class="absolute inset-2 border-4 border-transparent border-b-cyan-400 border-l-cyan-300 rounded-full animate-spin" style="animation-duration: 1.5s; animation-direction: reverse;"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-blue-600 text-2xl"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3" id="loading-title">
                {{ $translations['loading']['title'] }}
            </h3>
            <p class="text-gray-500" id="loading-subtitle">{{ $translations['loading']['subtitle'] }}</p>
        </div>

        {{-- المحتوى --}}
        <div id="content" class="hidden space-y-8"></div>

        {{-- خطأ --}}
        <div id="error" class="hidden bg-red-50 border border-red-200 rounded-2xl p-8 text-center">
            <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-4"></i>
            <h3 class="text-xl font-bold text-red-900 mb-2">{{ $translations['error']['title'] }}</h3>
            <p id="error-message" class="text-red-700 mb-4"></p>
            <button onclick="fetchDetails()" class="px-6 py-2 bg-red-600 text-white rounded-lg">
                {{ $translations['error']['retry'] }}
            </button>
        </div>
    </div>
</div>

<script>
    const attemptId = {{ $attempt->id }};
    const alternativeIndex = {{ $alternativeIndex }};
    const majorName = @json($majorName);
    const originalMatch = {{ $originalMatch ?? 0 }};
    const originalReason = @json($originalReason ?? '');

    // ✅ الترجمات من Laravel
    const t = @json($translations);

    console.log('✅ Page loaded');
    console.log('Attempt ID:', attemptId);
    console.log('Alternative Index:', alternativeIndex);
    console.log('Major Name:', majorName);

    document.addEventListener('DOMContentLoaded', function() {
        console.log('✅ DOM loaded, calling fetchDetails()');
        fetchDetails();
    });

    function fetchDetails() {
        console.log('🔄 Fetching details...');

        document.getElementById('loading').classList.remove('hidden');
        document.getElementById('content').classList.add('hidden');
        document.getElementById('error').classList.add('hidden');

        fetch('/alternative/' + attemptId + '/fetch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ index: alternativeIndex })
        })
        .then(res => {
            console.log('📥 Response status:', res.status);
            return res.json();
        })
        .then(data => {
            console.log('📦 Response data:', data);

            if (data.success) {
                displayContent(data.data, originalMatch, originalReason);
            } else {
                showError(data.message || t.error.unexpected);
            }
        })
        .catch(err => {
            console.error('❌ Fetch error:', err);
            showError(t.error.connection);
        });
    }

    function displayContent(data, originalMatch = null, originalReason = null) {
        console.log('🎨 Displaying content...');

        document.getElementById('loading').classList.add('hidden');
        document.getElementById('content').classList.remove('hidden');

        const content = document.getElementById('content');

        const displayMatch = originalMatch || data.match || 0;
        const displayReason = originalReason || data.reason || '';

        // Header
        const headerHTML = `
            <div class="bg-[#2563eb] dark:bg-blue-800 rounded-2xl shadow-xl p-8 text-white">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <span class="text-white text-sm">${t.header.label}</span>
                        <h1 class="text-3xl font-bold mt-2">${data.major_name || majorName}</h1>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-black">${displayMatch}%</div>
                        <div class="text-purple-200 text-sm">${t.header.match}</div>
                    </div>
                </div>
                <p class="text-lg bg-white/10 rounded-xl p-4">${displayReason}</p>
            </div>
        `;

        // Market Info
        const marketHTML = data.market_info ? `
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-briefcase text-green-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3">${t.market.opportunities}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">${data.market_info.job_opportunities || ''}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-dollar-sign text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3">${t.market.salary}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">${data.market_info.average_salary || ''}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas fa-globe text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3">${t.market.demand}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">${data.market_info.global_demand || ''}</p>
                </div>
            </div>
        ` : '';

        // Why This Major
        const whyHTML = (data.why_this_major && data.why_this_major.length > 0) ? `
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
                <h2 class="text-xl font-bold mb-5 flex items-center gap-2">
                    <i class="fas fa-star text-blue-600"></i>
                    ${t.why}
                </h2>
                <ul class="space-y-3">
                    ${data.why_this_major.map(r => `
                        <li class="flex items-start gap-3 p-4 bg-blue-50 rounded-xl">
                            <i class="fas fa-check-circle text-blue-600 mt-1"></i>
                            <span>${r}</span>
                        </li>
                    `).join('')}
                </ul>
            </div>
        ` : '';

        // Study & Work
        const studyWorkHTML = `
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
                    <i class="fas fa-graduation-cap text-green-600 text-2xl mb-3"></i>
                    <h3 class="font-bold mb-3">${t.study.title}</h3>
                    <p class="text-sm text-gray-600">${data.study_description || ''}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
                    <i class="fas fa-briefcase text-blue-600 text-2xl mb-3"></i>
                    <h3 class="font-bold mb-3">${t.work.title}</h3>
                    <p class="text-sm text-gray-600">${data.work_description || ''}</p>
                </div>
            </div>
        `;

        // Skills
        const skillsHTML = (data.required_skills && data.required_skills.length > 0) ? `
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6">
                <h3 class="text-xl font-bold mb-5 flex items-center gap-2">
                    <i class="fas fa-tools text-cyan-600"></i>
                    ${t.skills}
                </h3>
                <div class="flex flex-wrap gap-2">
                    ${data.required_skills.map(s => `
                        <span class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 rounded text-sm">${s}</span>
                    `).join('')}
                </div>
            </div>
        ` : '';

        // Roadmap
        const roadmapHTML = data.roadmap ? `
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl shadow-xl p-6 text-white">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-map-signs text-blue-400"></i>
                    ${t.roadmap.title}
                </h2>
                <div class="grid md:grid-cols-3 gap-6">
                    ${['month1', 'month2', 'month3'].map((m, i) => `
                        <div class="bg-gray-800/50 p-5 rounded-xl border border-gray-700">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center font-bold mb-3">${i + 1}</div>
                            <h4 class="font-bold mb-2">${t.roadmap.month} ${i + 1}</h4>
                            <ul class="text-sm text-gray-300 space-y-1 list-disc list-inside">
                                ${data.roadmap[m] ? data.roadmap[m].map(task => `<li>${task}</li>`).join('') : ''}
                            </ul>
                        </div>
                    `).join('')}
                </div>
            </div>
        ` : '';

        // Actions
        const actionsHTML = `
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/results/${attemptId}" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-center">
                    <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'right' : 'left' }} {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    ${t.back}
                </a>
                <a href="/quiz" class="px-8 py-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-900 rounded-xl font-semibold text-center">
                    <i class="fas fa-redo {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    ${t.actions.retake}
                </a>
            </div>
        `;

        // Combine all sections
        content.innerHTML = `
            ${headerHTML}
            ${marketHTML}
            ${whyHTML}
            ${studyWorkHTML}
            ${skillsHTML}
            ${roadmapHTML}
            ${actionsHTML}
        `;
    }

    function showError(message) {
        console.error('❌ Error:', message);
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('content').classList.add('hidden');
        document.getElementById('error').classList.remove('hidden');
        document.getElementById('error-message').textContent = message;
    }
</script>

@endsection

@push('chatbot')
    <x-chatbot context="alternative" :attemptId="$attempt->id" :alternativeIndex="$alternativeIndex" />
@endpush
