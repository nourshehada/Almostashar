<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Question;
use App\Models\QuizAttempt;
use App\Services\AI\AIServiceInterface;

class QuizController extends Controller
{
    private AIServiceInterface $ai;

    private const PROFILE_DIMENSIONS = [
        'analysis',
        'creativity',
        'leadership',
        'communication',
        'research',
        'business',
        'technology',
        'humanitarian',
        'scientific',
        'adaptability',
    ];

    private const MIN_REQUIRED_ANSWERS = 24;

    public function __construct(AIServiceInterface $ai)
    {
        $this->ai = $ai;
    }

    public function index()
    {
        $start = microtime(true);

        $locale = app()->getLocale();
        $isArabic = $locale === 'ar';

        $questions = Question::with([
                'options:id,question_id,option_ar,option_en,analysis,creativity,leadership,communication,research,business,technology,humanitarian,scientific,adaptability'
            ])
            ->orderBy('order')
            ->get()
            ->map(function ($question) use ($isArabic) {
                return [
                    'id' => $question->id,
                    'question' => $isArabic ? $question->question_ar : ($question->question_en ?? $question->question_ar),
                    'type' => $question->type,
                    'suggestions' => $question->options
                        ->map(function ($option) use ($isArabic) {
                            return [
                                'id' => $option->id,
                                'text' => $isArabic ? $option->option_ar : ($option->option_en ?? $option->option_ar),
                                'analysis' => $option->analysis,
                                'creativity' => $option->creativity,
                                'leadership' => $option->leadership,
                                'communication' => $option->communication,
                                'research' => $option->research,
                                'business' => $option->business,
                                'technology' => $option->technology,
                                'humanitarian' => $option->humanitarian,
                                'scientific' => $option->scientific,
                                'adaptability' => $option->adaptability,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            });



        $executionTime = (microtime(true) - $start) * 1000;
        $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

        Log::info('Quiz Page Performance', [
            'execution_time_ms' => round($executionTime,2),
            'memory_usage_mb' => round($memoryUsage,2),
        ]);

        return view('pages.quiz', [
        'questionsJson' => $questions->toJson(),
        ]);
    }

    public function submit(Request $request)
    {
        try {
        $start = microtime(true);
            $validationRules = [
                'answers' => ['required', 'array'],
                'answers.*.question_id' => ['required', 'integer'],
                'answers.*.answer' => ['nullable'],
                'ignore_open_answers' => ['sometimes', 'boolean'],
            ];

            $validationRules['profile'] = ['required', 'array'];
            foreach (self::PROFILE_DIMENSIONS as $dimension) {
                $validationRules["profile.{$dimension}"] = ['required', 'numeric'];
            }

            $validated = $request->validate($validationRules);

            if (count($validated['answers']) < self::MIN_REQUIRED_ANSWERS) {
                return response()->json([
                    'success' => false,
                    'message' => __('quiz.errors.invalid_answers')
                ], 422);
            }

            $answers = $validated['answers'];

            if ($request->ignore_open_answers) {
                $answers = collect($answers)
                    ->reject(fn($a) => in_array($a['question_id'], [25, 26]))
                    ->values()
                    ->toArray();
            }

            $currentLang = app()->getLocale();
            $aiResult = $this->ai->analyze($answers, $validated['profile'], $currentLang);

            if (($aiResult['status'] ?? null) === 'validation_failed') {
                return response()->json([
                    'success' => false,
                    'type' => 'validation_failed',
                    'allow_skip' => true,
                    'message' => $aiResult['message']
                ]);
            }

            if (($aiResult['status'] ?? null) === 'error') {
                Log::error('❌ AI Error', ['result' => $aiResult]);

                $userMessage = 'حدث خطأ في التحليل. ';

                if (str_contains($aiResult['message'] ?? '', '429')) {
                    $userMessage .= 'تم تجاوز حد الطلبات. يرجى الانتظار دقيقة والمحاولة مرة أخرى.';
                } elseif (str_contains($aiResult['message'] ?? '', '413')) {
                    $userMessage .= 'البيانات كبيرة جداً. حاول مرة أخرى.';
                } else {
                    $userMessage .= 'حاول مرة أخرى بعد قليل.';
                }

                return response()->json([
                    'success' => false,
                    'type' => 'ai_error',
                    'message' => $userMessage
                ], 500);
            }

            $attempt = QuizAttempt::create([
                'answers' => $validated['answers'],
                'profile' => $validated['profile'],
                'ai_result' => $aiResult
            ]);

            session([
                'current_attempt_id' => $attempt->id,
            ]);

            $request->session()->save();

        $executionTime = (microtime(true) - $start) * 1000;
        $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

        Log::info('Quiz Submit Performance', [
            'execution_time_ms' => round($executionTime,2),
            'memory_usage_mb' => round($memoryUsage,2),
        ]);

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id
        ]);

        } catch (\Throwable $e) {
            Log::error('Quiz Submit Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('quiz.errors.general')
            ], 500);
        }
    }

    public function results(QuizAttempt $attempt)
    {
        $start = microtime(true);

        $sessionAttemptId = session('current_attempt_id');

        if ($sessionAttemptId !== $attempt->id) {
            abort(403);
        }

        if (empty($attempt->ai_result)) {
            abort(404);
        }

        $executionTime = (microtime(true) - $start) * 1000;
        $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

        logger()->info('Result page Loading', [
            'Execution Time (ms)' => $executionTime,
            'Memory (MB)' => $memoryUsage,
        ]);

        return view('pages.result', compact('attempt'));
    }

    public function alternativeDetails(QuizAttempt $attempt, int $index)
    {
        // ✅ حماية الملكية
        $sessionAttemptId = session('current_attempt_id');

        if ($sessionAttemptId !== $attempt->id) {
            abort(403);
        }

        // ✅ تعديل #1: التحقق من وجود ai_result
        if (empty($attempt->ai_result)) {
            abort(404);
        }

        $alternatives = $attempt->ai_result['alternatives'] ?? [];

        if (!isset($alternatives[$index])) {
            return redirect()->route('results.show', $attempt->id)
                ->with('error', 'التخصص غير موجود');
        }

        $alternativeDetails = $attempt->alternative_details ?? [];
        $cachedData = $alternativeDetails[$index] ?? null;

        return view('pages.alternative', [
            'attempt' => $attempt,
            'alternativeIndex' => $index,
            'majorName' => $alternatives[$index]['name'],
            'cachedData' => $cachedData,
            'originalMatch' => $alternatives[$index]['match'] ?? 0,
            'originalReason' => $alternatives[$index]['reason'] ?? '',
        ]);
    }

    public function fetchAlternativeDetails(Request $request, QuizAttempt $attempt)
    {
        $start = microtime(true);

        // ✅ حماية الملكية
        $sessionAttemptId = session('current_attempt_id');

        if ($sessionAttemptId !== $attempt->id) {
            abort(403);
        }

        // ✅ تعديل #2: التحقق من وجود ai_result
        if (empty($attempt->ai_result)) {
            abort(404);
        }

        $request->validate([
            'index' => ['required', 'integer', 'in:0,1'],
        ]);

        $index = $request->index;
        $alternatives = $attempt->ai_result['alternatives'] ?? [];

        if (!isset($alternatives[$index])) {
            return response()->json([
                'success' => false,
                'message' => 'التخصص غير موجود'
            ], 404);
        }

        $alternativeDetails = $attempt->alternative_details ?? [];

        if (isset($alternativeDetails[$index])) {
            Log::info('✅ Using cached alternative details', ['index' => $index]);

            return response()->json([
                'success' => true,
                'data' => $alternativeDetails[$index],
                'cached' => true
            ]);
        }

        Log::info('🔄 Fetching from AI', ['index' => $index]);

        $majorName = $alternatives[$index]['name'];

        try {
            $aiResult = $this->ai->getAlternativeDetails(
                $majorName,
                $attempt->profile,
                $attempt->answers,
                app()->getLocale()
            );

            if (($aiResult['status'] ?? null) === 'error') {
                return response()->json([
                    'success' => false,
                    'message' => $aiResult['message']
                ], 500);
            }

            $alternativeDetails[$index] = $aiResult;
            $attempt->update([
                'alternative_details' => $alternativeDetails
            ]);

            $executionTime = (microtime(true) - $start) * 1000;
            $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

            logger()->info('Alternative Major Details', [
                'Execution Time (ms)' => $executionTime,
                'Memory (MB)' => $memoryUsage,
            ]);

            Log::info('💾 Saved alternative details to database', ['index' => $index]);

            return response()->json([
                'success' => true,
                'data' => $aiResult,
                'cached' => false
            ]);

        } catch (\Throwable $e) {
            // ✅ تعديل #3: عدم كشف تفاصيل الخطأ للمستخدم
            Log::error('Alternative Details Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('quiz.errors.general')
            ], 500);
        }
    }

    public function saveName(Request $request)
    {
        $validated = $request->validate([
            'user_name' => [
                'required',
                'string',
                'min:2',
                'max:50',
                'regex:/^[\pL\s]+$/u'
            ],
        ]);

        session(['user_name' => $validated['user_name']]);

        $request->session()->save();

        return response()->json([
            'success' => true,
            'message' => 'Name saved successfully'
        ]);
    }
}
