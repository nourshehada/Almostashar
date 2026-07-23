@extends('layouts.app')

@section('title', __('about.title'))

@push('styles')
<style>
body{
        font-family: 'Cairo', sans-serif;
    }
</style>
@endpush
@section('content')

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">

     <section class="relative min-h-[600px] md:min-h-[700px] flex items-center overflow-hidden">
         <!-- Background Image -->
         <div class="absolute inset-0 z-0">
             <img src="{{ asset('images/about-robot.png') }}"
                  alt="AI Robot Background"
                  class="w-full h-full object-cover">
         </div>

         <!-- Content - Positioned on Right, Text Centered Inside -->
         <div class="relative z-10 max-w-7xl mx-auto px-4 md:px-10 w-full">
             <div class="max-w-2xl ml-auto">
                 <div class="text-center">
                     <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-900 dark:text-white mb-4 leading-tight">
                         {{ __('about.hero.title') }}
                     </h1>
                     <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-blue-600 dark:text-blue-400 mb-6">
                         {{ __('about.hero.subtitle') }}
                     </h2>
                     <p class="text-gray-700 dark:text-gray-300 text-base md:text-lg leading-relaxed">
                         {{ __('about.hero.description') }}
                     </p>
                 </div>
             </div>
         </div>
     </section>

    <!-- ========================================== -->
    <!-- 2️⃣ Problem Section -->
    <!-- ========================================== -->
    <section class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-5xl mx-auto px-4 md:px-10">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                        {{ __('about.problem.title') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ __('about.problem.description') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- 3️⃣ How It Works - 5 خطوات -->
    <!-- ========================================== -->
    <section class="py-12 bg-white dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 md:px-10">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-10">
                {{ __('about.how_it_works.title') }}
            </h2>

            <div class="max-w-3xl mx-auto space-y-3">
                @foreach(range(1,5) as $num)
                    @php $step = __('about.how_it_works.steps.' . $num); @endphp
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-5 flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold flex-shrink-0">
                            {{ $num }}
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1">{{ $step['title'] }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $step['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- 6️⃣ Technologies - محسّنة -->
    <!-- ========================================== -->
    <section class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 md:px-10">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-3">
                {{ __('about.technologies.title') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-center mb-10">
                {{ __('about.technologies.subtitle') }}
            </p>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach(['laravel', 'mysql', 'openai', 'tailwind'] as $tech)
                    @php $data = __('about.technologies.items.' . $tech); @endphp
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 text-center border border-gray-200 dark:border-gray-700">
                        @if($tech === 'laravel')
                            <img src="https://cdn.worldvectorlogo.com/logos/laravel-2.svg" alt="Laravel" class="h-10 mx-auto mb-2">
                        @elseif($tech === 'mysql')
                            <img src="https://cdn.worldvectorlogo.com/logos/mysql.svg" alt="MySQL" class="h-10 mx-auto mb-2">
                        @elseif($tech === 'openai')
                            <img src="https://cdn.worldvectorlogo.com/logos/openai-2.svg" alt="OpenAI" class="h-10 mx-auto mb-2">
                        @else
                            <img src="https://cdn.worldvectorlogo.com/logos/tailwind-css-2.svg" alt="Tailwind" class="h-10 mx-auto mb-2">
                        @endif
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm mb-1">{{ $data['name'] }}</h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $data['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- 7️⃣ Vision, Mission, Goals - 3 بطاقات -->
    <!-- ========================================== -->
    <section class="py-16 bg-blue-600 dark:bg-blue-900">
        <div class="max-w-7xl mx-auto px-4 md:px-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white text-center mb-12">
                {{ __('about.vision.title') }}
            </h2>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach(['vision', 'mission', 'goals'] as $item)
                    @php $data = __('about.vision.' . $item); @endphp
                    <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 text-center border border-white/20 hover:bg-white/20 transition">
                        <div class="text-5xl mb-4">{{ $data['icon'] }}</div>
                        <h3 class="text-2xl font-bold text-white mb-4">{{ $data['title'] }}</h3>
                        <p class="text-blue-50 leading-relaxed">{{ $data['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>

@endsection

