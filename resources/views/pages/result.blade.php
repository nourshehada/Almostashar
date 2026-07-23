@extends('layouts.simple')

@section('title', __('results.title'))

@section('content')

@php
    $result = $attempt->ai_result ?? [];
    $bestMajor = $result['best_major'] ?? [];
    $alternatives = $result['alternatives'] ?? [];
    $strengths = $result['strengths'] ?? [];
    $developmentAreas = $result['development_areas'] ?? [];
    $roadmap = $result['roadmap'] ?? [];
    $matchPercentage = $result['match_percentage'] ?? 0;
    $personalitySummary = $result['personality_summary'] ?? '';
    $encouragement = $result['encouragement'] ?? '';
    $profile = $attempt->profile ?? [];
@endphp

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 md:px-10 space-y-8">

        <!-- 1️⃣ البطاقة الأولى -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-xl overflow-hidden">
            <div class="relative h-64 md:h-72 w-full">
                <img src="{{ asset('images/result-bg.png') }}"
                     alt="Results Background"
                     class="w-full h-full object-cover">
                <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white/40 dark:from-gray-800/60 to-transparent rounded-t-2xl pointer-events-none"></div>

                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10">
                    <div class="absolute inset-0 bg-blue-600/20 rounded-full blur-xl scale-125"></div>
                    <div class="relative w-36 h-36 md:w-44 md:h-44 bg-blue-600 rounded-full flex flex-col items-center justify-center shadow-2xl border-[5px] border-white dark:border-gray-800 text-white">
                        <span class="text-5xl md:text-6xl font-bold leading-none tracking-tight">{{ $matchPercentage }}<span class="text-2xl md:text-3xl">%</span></span>
                        <span class="text-sm md:text-base font-semibold mt-1 opacity-90 tracking-wide uppercase">{{ __('results.match_label') }}</span>
                    </div>
                </div>
            </div>

            <div class="text-center mb-6 pt-4 px-4">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ __('results.best_path_title') }} <span class="text-blue-600 dark:text-blue-400">{{ $bestMajor['name'] ?? 'غير محدد' }}</span>
                </h1>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto line-clamp-3">
                    {{ $attempt->ai_result['best_major']['overview'] }} {{ $bestMajor['reason'] ?? '' }}
                </p>
            </div>

            <!-- لماذا هذا المسار؟ -->
            @if(!empty($bestMajor['why_this_major']))
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 max-w-3xl mx-auto border border-blue-100 dark:border-blue-800 mb-8">
                <h3 class="font-bold text-blue-900 dark:text-blue-300 mb-3 flex items-center gap-2 justify-center">
                    <i class="fas fa-sparkles"></i> {{ __('results.why_this_path') }}
                </h3>
                <ul class="grid md:grid-cols-3 gap-3 text-sm text-blue-800 dark:text-blue-200 text-center md:text-left">
                    @foreach($bestMajor['why_this_major'] as $reason)
                        <li class="flex items-start justify-center md:justify-start gap-2">
                            <i class="fas fa-check-circle mt-0.5 text-blue-600 flex-shrink-0"></i>
                            <span class="line-clamp-2">{{ $reason }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <!-- 2️⃣ تحليل المهارات -->
        @if(!empty($profile))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6 md:p-8 mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <span class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-purple-600"></i>
                </span>
                {{ __('results.skills_distribution') }}
            </h3>

            <div class="grid md:grid-cols-2 gap-4">
                @php
                    $skillLabels = [
                        'analysis' => ['name' => __('results.skills.analytical'), 'icon' => '🧠', 'color' => 'blue'],
                        'creativity' => ['name' => __('results.skills.creativity'), 'icon' => '🎨', 'color' => 'pink'],
                        'leadership' => ['name' => __('results.skills.leadership'), 'icon' => '👑', 'color' => 'amber'],
                        'communication' => ['name' => __('results.skills.communication'), 'icon' => '💬', 'color' => 'green'],
                        'research' => ['name' => __('results.skills.research'), 'icon' => '🔬', 'color' => 'indigo'],
                        'business' => ['name' => __('results.skills.business'), 'icon' => '💼', 'color' => 'yellow'],
                        'technology' => ['name' => __('results.skills.technology'), 'icon' => '', 'color' => 'cyan'],
                        'humanitarian' => ['name' => __('results.skills.humanitarian'), 'icon' => '❤️', 'color' => 'red'],
                        'scientific' => ['name' => __('results.skills.scientific'), 'icon' => '🧪', 'color' => 'emerald'],
                        'adaptability' => ['name' => __('results.skills.adaptability'), 'icon' => '🔄', 'color' => 'violet'],
                    ];

                    // ✅ حساب أعلى قيمة في الـ profile كمرجع (100%)
                    $maxValue = max($profile);
                @endphp

                @foreach($skillLabels as $key => $skill)
                    @if(isset($profile[$key]))
                    @php
                        // ✅ النسبة المئوية الصحيحة: (القيمة / أعلى قيمة) × 100
                        $percentage = $maxValue > 0 ? round(($profile[$key] / $maxValue) * 100) : 0;
                    @endphp
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">{{ $skill['icon'] }}</span>
                                <span class="font-semibold text-gray-800 dark:text-white text-sm">
                                    {{ $skill['name'] }}
                                </span>
                            </div>
                            {{-- ✅ عرض النسبة المئوية الصحيحة --}}
                            <span class="text-sm font-bold text-{{ $skill['color'] }}-600">
                                {{ $percentage }}%
                            </span>
                        </div>
                        {{-- ✅ الشريط يعكس النسبة الحقيقية --}}
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 overflow-hidden">
                            <div class="bg-{{ $skill['color'] }}-500 h-2 rounded-full transition-all duration-700"
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- 4️⃣ معلومات المسار -->
        <div class="grid md:grid-cols-3 gap-6">
            @if(!empty($bestMajor['work_description']))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-5">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-briefcase text-blue-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ __('results.what_to_work') }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-4">
                    {{ $bestMajor['work_description'] }}
                </p>
            </div>
            @endif

            @if(!empty($bestMajor['required_skills']))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-5">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-tools text-purple-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ __('results.required_skills') }}</h3>
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach($bestMajor['required_skills'] as $skill)
                        <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded whitespace-nowrap"
                              title="{{ $skill }}">
                            {{ $skill }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            @if(!empty($bestMajor['study_description']))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-5">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-graduation-cap text-green-600"></i>
                </div>
                <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ __('results.what_to_study') }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed line-clamp-4">
                    {{ $bestMajor['study_description'] }}
                </p>
            </div>
            @endif
        </div>

        {{-- ================= MARKET INFO - BEST MAJOR ================= --}}
        @if(!empty($result['market_info']))
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6 md:p-8 mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <span class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </span>
                {{ app()->getLocale() === 'ar' ? 'معلومات سوق العمل' : 'Market Information' }}
            </h3>

            <div class="grid md:grid-cols-3 gap-6">
                {{-- Job Opportunities --}}
                <div class="bg-gradient-to-br from-green-50 to-white dark:from-green-900/10 dark:to-gray-800 rounded-xl p-5 border border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                            <i class="fas fa-briefcase text-green-600 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'فرص العمل' : 'Job Opportunities' }}
                        </h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ $result['market_info']['job_opportunities'] ?? '' }}
                    </p>
                </div>

                {{-- Average Salary --}}
                <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/10 dark:to-gray-800 rounded-xl p-5 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-blue-600 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'متوسط الراتب' : 'Average Salary' }}
                        </h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ $result['market_info']['average_salary'] ?? '' }}
                    </p>
                </div>

                {{-- Global Demand --}}
                <div class="bg-gradient-to-br from-purple-50 to-white dark:from-purple-900/10 dark:to-gray-800 rounded-xl p-5 border border-purple-200 dark:border-purple-800">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                            <i class="fas fa-globe text-purple-600 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'الطلب عالمياً' : 'Global Demand' }}
                        </h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ $result['market_info']['global_demand'] ?? '' }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- 3️ التخصصات البديلة -->
        @if(count($alternatives) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 p-6 md:p-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                {{ __('results.alternative_paths_title') }}
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-center border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-sm text-gray-500 dark:text-gray-400">
                            <th class="pb-3 font-medium">{{ __('results.table_headers.career_path') }}</th>
                            <th class="pb-3 font-medium">{{ __('results.table_headers.match_rate') }}</th>
                            <th class="pb-3 font-medium hidden md:table-cell">{{ __('results.table_headers.reason') }}</th>
                            <th class="pb-3 font-medium text-right">{{ __('results.table_headers.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach(array_slice($alternatives, 0, 2) as $alt)
                            @php
                                $matchPercent = (int) ($alt['match'] ?? 0);
                                $textColor = $matchPercent >= 75 ? 'green-700 dark:text-green-400' : ($matchPercent >= 70 ? 'blue-700 dark:text-blue-400' : 'orange-700 dark:text-orange-400');
                                $bgLight = $matchPercent >= 75 ? 'green-100 dark:bg-green-900/30' : ($matchPercent >= 70 ? 'blue-100 dark:bg-blue-900/30' : 'orange-100 dark:bg-orange-900/30');
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="py-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $alt['name'] }}
                                </td>
                                <td class="py-4">
                                    <span class="bg-{{ $bgLight }} text-{{ $textColor }} px-2 py-1 rounded-md font-bold">
                                        {{ $matchPercent }}%
                                    </span>
                                </td>
                                <td class="px-2 py-4 hidden md:table-cell text-gray-600 dark:text-gray-400">
                                    <span class="line-clamp-2">{{ $alt['reason'] ?? '' }}</span>
                                </td>
                                <td class="py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('alternative.details', ['attempt' => $attempt->id, 'index' => $loop->index]) }}"
                                       class="text-blue-600 hover:underline font-medium whitespace-nowrap">
                                        {{ __('results.view_details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- 5️⃣ خارطة الطريق -->
        @if(!empty($roadmap))
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-xl p-6 md:p-8 text-white">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <i class="fas fa-route text-blue-400"></i>
                {{ __('results.roadmap_title') }}
            </h2>

            <div class="grid md:grid-cols-3 gap-6 relative">
                <div class="hidden md:block absolute top-8 left-0 w-full h-0.5 bg-gray-700 -z-0"></div>

                @foreach(['month1', 'month2', 'month3'] as $index => $monthKey)
                    @php
                        $month = __('results.months.' . $monthKey);
                    @endphp
                    @if(!empty($roadmap[$monthKey]))
                    <div class="relative z-10 bg-gray-800/50 dark:bg-gray-700/50 p-5 rounded-xl border border-gray-700">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm font-bold mb-3">
                            {{ $month['number'] }}
                        </div>
                        <h4 class="font-bold mb-2">{{ $month['title'] }}</h4>
                        <ul class="text-sm text-gray-300 space-y-1 list-disc list-inside">
                            @foreach($roadmap[$monthKey] as $item)
                                <li class="line-clamp-2">{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif

        <!-- رسالة التشجيع -->
        @if($encouragement)
        <div class="bg-gradient-to-br from-blue-50 to-blue-50 dark:from-blue-900/20 dark:to-blue-900/20 rounded-2xl shadow-md p-6 md:p-8 border-2 border-blue-200 dark:border-blue-800">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <i class="fas fa-heart text-red-500"></i>
                {{ __('results.encouragement_title') }}
            </h3>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-lg italic line-clamp-4">
                "{{ $encouragement }}"
            </p>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-10">
            <a href="{{ route('quiz') }}"
               class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition shadow-lg shadow-blue-600/30 text-center">
                <i class="fas fa-redo ml-2"></i>
                {{ __('results.retake_quiz') }}
            </a>
            <button type="button"
               id="open-chat-btn"
               class="px-8 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-semibold transition">
                <i class="fas fa-comment-dots"></i>
                <span>{{ __('home.cta.btn_secondary') }}</span>
            </button>

            <a href="{{ route('home') }}"
               class="px-8 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white rounded-xl font-semibold transition text-center">
                <i class="fas fa-home ml-2"></i>
                {{ __('results.back_to_home') }}
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('open-chat-btn').addEventListener('click', function() {
        // محاكاة الضغط على زر الشات
        const chatToggle = document.getElementById('chatbot-toggle');
        if (chatToggle) {
            chatToggle.click();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // ✅ منع الرجوع لصفحة الاختبار
        PreventBack.prevent();
    });
</script>
@endpush

@push('chatbot')
    <x-chatbot context="results" :attemptId="$attempt->id" />
@endpush
