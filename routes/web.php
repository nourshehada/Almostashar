<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ChatController;

Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');

Route::prefix('quiz')->group(function () {
    Route::get('/', [QuizController::class, 'index'])->name('quiz');

    Route::post('/submit', [QuizController::class, 'submit'])
        ->name('quiz.submit')
        ->middleware('throttle:5,1');

    Route::post('/save-name', [QuizController::class, 'saveName'])
        ->name('quiz.save-name')
        ->middleware('throttle:10,1');
});

Route::get('/results/{attempt}', [QuizController::class, 'results'])->name('results.show');

Route::prefix('alternative')->group(function () {
    Route::get('/{attempt}/{index}', [QuizController::class, 'alternativeDetails'])
        ->name('alternative.details');

    Route::post('/{attempt}/fetch', [QuizController::class, 'fetchAlternativeDetails'])
        ->name('alternative.fetch')
        ->middleware('throttle:10,1');
});

Route::prefix('chat')->group(function () {
    Route::post('/send', [ChatController::class, 'sendMessage'])
        ->name('chat.send')
        ->middleware('throttle:20,1');

    Route::get('/history', [ChatController::class, 'getHistory'])
        ->name('chat.history')
        ->middleware('throttle:30,1');

    Route::post('/clear', [ChatController::class, 'clearHistory'])
        ->name('chat.clear')
        ->middleware('throttle:10,1');
});

Route::get('/locale/{locale}', function($locale) {
    abort_unless(
        in_array($locale, config('app.supported_locales')),
        404
    );

    session(['locale' => $locale]);
    return redirect()->back();
})->name('locale.change');




Route::get('/debug/last-attempt', function() {
    $attempt = \App\Models\QuizAttempt::latest()->first();

    if (!$attempt) {
        return response()->json(['message' => 'لا توجد محاولات']);
    }

    return response()->json([
        'id' => $attempt->id,
        'has_answers' => !empty($attempt->answers),
        'has_profile' => !empty($attempt->profile),
        'has_ai_result' => !empty($attempt->ai_result),
        'ai_status' => $attempt->ai_result['status'] ?? 'N/A',
        'best_major' => $attempt->ai_result['best_major']['name'] ?? 'N/A',
        'created_at' => $attempt->created_at->diffForHumans(),
        'full_ai_result' => $attempt->ai_result
    ]);
});

Route::get('/debug/test-gemini', function() {
    $apiKey = config('services.gemini.api_key');
    $model = config('services.gemini.model', 'gemini-2.0-flash');

    if (empty($apiKey)) {
        return response()->json([
            'success' => false,
            'error' => 'API Key غير موجود في .env'
        ]);
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

    try {
        $response = \Illuminate\Support\Facades\Http::timeout(30)->post($url, [
            'contents' => [
                ['parts' => [['text' => 'قل: مرحبا']]]
            ]
        ]);

        return response()->json([
            'success' => $response->successful(),
            'status' => $response->status(),
            'model' => $model,
            'api_key_preview' => substr($apiKey, 0, 10) . '...',
            'response' => $response->json()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});

/**
 * اختبار الاتصال بـ Groq API
 */
Route::get('/debug/test-groq', function() {
    $apiKey = config('services.groq.api_key');
    $model = config('services.groq.model', 'llama-3.3-70b-versatile');

    if (empty($apiKey)) {
        return response()->json([
            'success' => false,
            'error' => 'GROQ_API_KEY غير موجود في .env',
            'hint' => 'احصل على مفتاح من https://console.groq.com'
        ]);
    }

    try {
        $response = \Illuminate\Support\Facades\Http::timeout(30)
            ->withToken($apiKey)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'user', 'content' => 'قل: مرحبا']
                ],
                'temperature' => 0.3,
                'max_tokens' => 100
            ]);

        return response()->json([
            'success' => $response->successful(),
            'status' => $response->status(),
            'model' => $model,
            'api_key_preview' => substr($apiKey, 0, 15) . '...',
            'response_time' => $response->header('x-groq-api-version'),
            'response' => $response->json(),
            'usage' => $response->json()['usage'] ?? null
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});
