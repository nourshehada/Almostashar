@extends('layouts.app')

@section('title', __('home.hero.title'))

@php
$start = microtime(true);
@endphp

@push('styles')
<style>

    .dark-transition { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
    .faq-content { transition: max-height 0.3s ease; }

     /* التدرجات العربية */
    .hero-gradient-overlay-en {
        background: linear-gradient(to right, rgba(255,255,255,1), rgba(255,255,255,0.3), rgba(219,234,254,0.2));
    }
    .dark .hero-gradient-overlay-en {
        background: linear-gradient(to right, rgba(17,24,39,1), rgba(17,24,39,0.5), rgba(31,41,55,0.3));
    }

    /* التدرجات الإنجليزية */
    .hero-gradient-overlay-ar {
        background: linear-gradient(to left, rgba(255,255,255,1), rgba(255,255,255,0.3), rgba(219,234,254,0.2));
    }
    .dark .hero-gradient-overlay-ar {
        background: linear-gradient(to left, rgba(17,24,39,1), rgba(17,24,39,0.5), rgba(31,41,55,0.3));
    }

    /* عكس الأسهم في RTL تلقائياً */
    [dir="rtl"] .rtl-flip {
        transform: scaleX(-1);
    }
</style>
@endpush

@section('content')

{{-- ================= HERO SECTION ================= --}}
<section id="home" class="relative pt-16 pb-20 overflow-hidden dark-transition" style="min-height: 90vh;">
    <div class="absolute inset-0">
        @php
            $locale = app()->getLocale();

            $heroImage = $locale === 'ar'
                ? 'images/hero-bg-ar.png'
                : 'images/hero-bg-en.png';
            $gradientClass = 'hero-gradient-overlay-' . $locale;
        @endphp

        <img src="{{ asset($heroImage) }}"
             alt="Hero Background"
             class="absolute inset-0 w-full h-full object-cover dark:opacity-40">

        <div class="absolute inset-0 {{ $gradientClass }}"></div>

        @if($locale === 'ar')
            <div class="absolute inset-0 bg-gradient-to-t from-white/60 via-transparent to-white/40 dark:from-gray-900/80 dark:via-transparent dark:to-gray-900/50"></div>
        @else
            <div class="absolute inset-0 bg-gradient-to-b from-white/60 via-transparent to-white/40 dark:from-gray-900/80 dark:via-transparent dark:to-gray-900/50"></div>
        @endif
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center min-h-[70vh]">
            <div>
                <h1 class="text-4xl lg:text-5xl xl:text-6xl font-bold text-blue-900 dark:text-blue-300 leading-tight mb-6">
                    {{ __('home.hero.title_1') }}<br>
                    <span class="text-blue-600 dark:text-blue-400">{{ __('home.hero.title_2') }}</span><br>
                    {{ __('home.hero.title_3') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg mb-8 leading-relaxed max-w-lg">
                    {{ __('home.hero.subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mb-8 justify-start">
                    <div class="flex flex-col items-start gap-2">
                        <button type="button" onclick="openNameModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-bold text-lg transition shadow-xl shadow-blue-600/40 flex items-center justify-center gap-2">
                            {{ __('home.hero.btn_primary') }}
                            <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                        </button>
                        <div class="flex flex-col gap-1">
                            <span class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                                {{ __('home.hero.personal_report') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col items-start gap-2">
                        <a href="#features" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-blue-900 dark:text-blue-300 border-2 border-blue-200 dark:border-gray-600 px-8 py-4 rounded-xl font-bold text-lg transition">
                            {{ __('home.hero.btn_secondary') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= FEATURES SECTION ================= --}}
<section id="features" class="py-20 bg-white dark:bg-gray-800 dark-transition">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ __('home.features.title') }}</h2>
            <p class="text-gray-600 dark:text-gray-300 text-lg max-w-2xl mx-auto">{{ __('home.features.subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $features = [
                    ['icon' => 'clipboard-list', 'color' => 'blue', 'key' => 'smart_tests'],
                    ['icon' => 'brain', 'color' => 'green', 'key' => 'ai_recommendations'],
                    ['icon' => 'chart-line', 'color' => 'orange', 'key' => 'market_insights'],
                    ['icon' => 'comments', 'color' => 'purple', 'key' => 'ai_assistant'],
                ];
            @endphp
            @foreach($features as $feature)
                @php
                    // متغيرات النصوص للسهولة
                    $tKey = 'home.features.items.' . $feature['key'];
                    $title = __($tKey . '.title');
                    $desc = __($tKey . '.desc');
                    $tag = __($tKey . '.tag');
                @endphp
                <div class="bg-white dark:bg-gray-700 rounded-2xl p-6 border border-gray-100 dark:border-gray-600 shadow-sm transition cursor-pointer dark-transition">
                    <div class="w-14 h-14 bg-{{ $feature['color'] }}-100 dark:bg-{{ $feature['color'] }}-900/30 rounded-xl flex items-center justify-center mb-5">
                        <i class="fas fa-{{ $feature['icon'] }} text-{{ $feature['color'] }}-600 dark:text-{{ $feature['color'] }}-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ $title }}</h3>
                    <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-4">{{ $desc }}</p>
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-sparkles text-{{ $feature['color'] }}-500"></i>
                        <span>{{ $tag }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= CAREERS SECTION ================= --}}
<section id="careers" class="py-16 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ __('home.careers.title') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">{{ __('home.careers.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(__('home.careers.items') as $key => $career)
                @php
                    // تحديد لون البادج حسب مستوى الطلب
                    $opportunityLevel = $career['opportunities'];
                    $badgeText = __('home.careers.labels.' . $opportunityLevel);

                    $badgeColors = [
                        'very_high_demand' => 'green',
                        'high_demand' => 'blue',
                        'good_demand' => 'purple',
                        'medium_demand' => 'orange',
                    ];

                    $badgeColor = $badgeColors[$opportunityLevel] ?? 'green';
                @endphp

                <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col">
                    <div class="relative h-48 overflow-hidden">
                        <img src="{{ asset('images/careers/' . $key . '.png') }}" alt="{{ $career['title'] }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-white dark:from-gray-800 to-transparent"></div>
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-{{ $badgeColor }}-500 text-white text-xs font-bold rounded-full shadow-lg">
                                {{ $badgeText }}
                            </span>
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                            {{ $career['title'] }}
                        </h3>

                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-4 flex-1">
                            {{ $career['description'] }}
                        </p>

                        <!-- متوسط الراتب فقط -->
                        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-dollar-sign text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">
                                     {{ __('home.careers.labels.salary') }}
                                </div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $career['salary'] }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= HOW IT WORKS SECTION ================= --}}
<section id= "how_it_works" class="py-20 bg-gray-50 dark:bg-gray-800 dark-transition">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ __('home.how_it_works.title') }}</h2>
            <p class="text-gray-600 dark:text-gray-300 text-lg">{{ __('home.how_it_works.subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 items-start">
            @php
                $steps = [
                    ['icon' => 'clipboard-check', 'color' => 'blue', 'key' => 'step_1'],
                    ['icon' => 'microchip', 'color' => 'purple', 'key' => 'step_2'],
                    ['icon' => 'chart-bar', 'color' => 'green', 'key' => 'step_3'],
                ];
                $isRTL = app()->getLocale() === 'ar';
            @endphp

            @foreach($steps as $index => $step)
                <div class="relative">
                    <div class="text-center">
                        <div class="relative inline-block mb-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-{{ $step['color'] }}-500 to-{{ $step['color'] }}-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-{{ $step['icon'] }} text-white text-3xl"></i>
                            </div>
                            <div class="absolute -top-2 {{ $isRTL ? '-left-2' : '-right-2' }} w-8 h-8 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center shadow-md border-2">
                                <span class="text-{{ $step['color'] }}-600 dark:text-{{ $step['color'] }}-400 font-bold text-sm">{{ $index + 1 }}</span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                            {{ __('home.how_it_works.steps.' . $step['key'] . '.title') }}
                        </h3>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ __('home.how_it_works.steps.' . $step['key'] . '.desc') }}
                        </p>
                    </div>

                    {{-- ✅ الأسهم بين الخطوات --}}
                    @if($index < 2)
                        <div class="hidden md:block absolute top-20 {{ $isRTL ? '-left-8' : '-right-8' }} text-gray-300 dark:text-gray-600">
                            <i class="fas fa-chevron-{{ $isRTL ? 'left' : 'right' }} text-2xl"></i>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="text-center mt-16">
            <button type="button" onclick="openNameModal()" class="inline-flex items-center gap-2 bg-blue-600 text-white px-8 py-3.5 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                <i class="fas fa-sparkles"></i>
                <span>{{ __('home.how_it_works.btn') }}</span>
                <i class="fas fa-arrow-right rtl-flip"></i>
            </button>
        </div>
    </div>
</section>

{{-- ================= TESTIMONIALS SECTION ================= --}}
<section class="py-20 bg-white dark:bg-gray-900 dark-transition">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ __('home.testimonials.title') }}</h2>
            <p class="text-gray-600 dark:text-gray-300 text-lg">{{ __('home.testimonials.subtitle') }}</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach(__('home.testimonials.items') as $t)
                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition dark-transition">
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < 5; $i++) <i class="fas fa-star text-yellow-400"></i> @endfor
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-6">{{ $t['text'] }}</p>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="font-bold text-gray-900 dark:text-white">{{ $t['name'] }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $t['role'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= FAQ SECTION ================= --}}
<section class="py-20 bg-white dark:bg-gray-800 dark-transition">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-3">{{ __('home.faq.title') }}</h2>
            <p class="text-gray-600 dark:text-gray-300 text-lg">{{ __('home.faq.subtitle') }}</p>
        </div>

        <div class="space-y-4">
            @foreach(__('home.faq.items') as $faq)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden dark-transition">
                    <button class="faq-toggle w-full px-6 py-4 text-left bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition flex justify-between items-center">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $faq['q'] }}</span>
                        <i class="fas fa-chevron-down text-gray-400 transform transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-4 text-gray-600 dark:text-gray-300">{{ $faq['a'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= TRUST SECTION ================= --}}
<section class="py-16 bg-gradient-to-b from-blue-50/50 to-white dark:from-gray-800 dark:to-gray-900 dark-transition">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ __('home.trust.title') }}</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach(__('home.trust.items') as $item)
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700 text-center dark-transition">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mx-auto mb-5">
                        <i class="fas fa-{{ $item['icon'] }} text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $item['title'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">{{ $item['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ================= CTA SECTION ================= --}}
<section class="py-20 bg-[#2563eb] dark:bg-blue-800 dark-transition">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl lg:text-5xl font-bold text-white mb-6">{{ __('home.cta.title') }}</h2>
        <p class="text-blue-100 dark:text-blue-200 text-lg mb-8 max-w-2xl mx-auto">{{ __('home.cta.subtitle') }}</p>

        <div class="flex flex-wrap justify-center gap-6 mb-10">
            @foreach(__('home.cta.features') as $feature)
                <div class="flex items-center gap-2 text-white">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ $feature }}</span>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button type="button" onclick="openNameModal()" class="inline-flex items-center gap-2 bg-white text-blue-600 px-8 py-3.5 rounded-lg font-semibold hover:bg-blue-50 transition shadow-lg">
                <i class="fas fa-sparkles"></i>
                <span>{{ __('home.cta.btn_primary') }}</span>
                <i class="fas fa-arrow-right rtl-flip"></i>
            </button>

            <button type="button"
                    id="open-chat-btn"
                    class="inline-flex items-center gap-2 bg-transparent text-white border border-white/30 px-8 py-3.5 rounded-lg font-semibold hover:bg-white/10 transition cursor-pointer">
                <i class="fas fa-comment-dots"></i>
                <span>{{ __('home.cta.btn_secondary') }}</span>
            </button>
        </div>
    </div>
</section>

{{-- ================= NAME MODAL ================= --}}
<div id="name-modal" class="fixed inset-0 z-[80] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeNameModal()"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-8">

        <h3 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-2">
            {{ __('home.name_modal.title') }}
        </h3>

        <p class="text-gray-600 dark:text-gray-400 text-center mb-6 text-sm">
            {{ __('home.name_modal.description') }}
        </p>

        <form id="name-form" onsubmit="submitName(event)">
            @csrf
            <div class="mb-6">
                <input
                    type="text"
                    id="user-name-input"
                    name="user_name"
                    placeholder="{{ __('home.name_modal.placeholder') }}"
                    class="w-full px-5 py-3.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-center focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition text-gray-700 dark:text-gray-300 placeholder-gray-400"
                    required
                    minlength="2"
                    maxlength="50"
                    autofocus
                >
            </div>

            {{-- الأزرار --}}
            <div class="flex flex-row gap-3">
                <button type="submit"
                    class="w-full px-5 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition shadow-lg shadow-blue-600/30">
                    <i class="fas fa-rocket {{ app()->getLocale() === 'ar' ? 'ml-2' : 'mr-2' }}"></i>
                    {{ __('home.name_modal.start') }}
                </button>

                <button type="button" onclick="closeNameModal()"
                    class="w-full px-5 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold transition">
                    {{ __('home.name_modal.cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.faq-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const content = btn.nextElementSibling;
            const icon = btn.querySelector('.fa-chevron-down');
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });
    });

    document.getElementById('open-chat-btn').addEventListener('click', function() {
        const openChatBtn = document.getElementById('open-chat-btn');

        if (openChatBtn) {
            openChatBtn.addEventListener('click', function() {
                const chatToggle = document.getElementById('chatbot-toggle');
                if (chatToggle) {
                    chatToggle.click();
                }
            });
        }
    });

    function openNameModal() {
        const modal = document.getElementById('name-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        // التركيز على حقل الإدخال
        setTimeout(() => {
            document.getElementById('user-name-input').focus();
        }, 100);
    }

    function closeNameModal() {
        const modal = document.getElementById('name-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function submitName(event) {
        event.preventDefault();

        const nameInput = document.getElementById('user-name-input');
        const userName = nameInput.value.trim();

        if (userName.length < 2) {
            nameInput.classList.add('border-red-500');
            return;
        }

        // إرسال الاسم للـ server
        fetch('{{ route("quiz.save-name") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                user_name: userName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("quiz") }}';
            } else {
                alert(data.message || 'Something went wrong');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // إغلاق الـ modal بزر Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeNameModal();
        }
    });
</script>
@endpush

@push('chatbot')
    <x-chatbot context="home" />
@endpush

@php
$executionTime = (microtime(true) - $start) * 1000;
$memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

\Log::info('Home Page Performance', [
    'Execution Time (ms)' => round($executionTime,2),
    'Memory Usage (MB)' => round($memoryUsage,2),
]);
@endphp
