<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use App\Services\AI\ChatServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ChatController extends Controller
{
    private ChatServiceInterface $groq;

    public function __construct(ChatServiceInterface $groq)
    {
        $this->groq = $groq;
    }

    /**
     * إرسال رسالة إلى الـ chatbot
     */
    public function sendMessage(Request $request)
    {
        $start = microtime(true);

        $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'context' => ['required', 'string', 'in:home,results,alternative'],
            'attempt_id' => ['nullable', 'integer'],
            'alternative_index' => ['nullable', 'integer', 'in:0,1'],
        ]);

        $message = $request->message;
        $context = $request->context;
        $attemptId = $request->attempt_id;
        $alternativeIndex = $request->alternative_index;
        $key = 'chat_history_' . ($attemptId ?? 'guest');

        // ✅ جلب المحادثة السابقة من Session
        $chatHistory = $request->session()->get($key, []);

        // ✅ إضافة رسالة المستخدم
        $chatHistory[] = [
            'role' => 'user',
            'content' => $message,
            'timestamp' => now()->format('H:i')
        ];

        // ✅ بناء السياق
        $contextData = $this->buildContext($context, $attemptId, $alternativeIndex);

        try {
            $response = $this->groq->chatWithContext(
                $message,
                $chatHistory,
                $context,
                $contextData
            );

        if (($response['status'] ?? null) === 'error') {
            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'خطأ في الرد'
            ], 500);
        }

        $aiMessage = $response['reply'] ?? 'عذراً، لم أتمكن من فهم سؤالك.';

        // ✅ إضافة رد AI إلى المحادثة
        $chatHistory[] = [
            'role' => 'assistant',
            'content' => $aiMessage,
            'timestamp' => now()->format('H:i')
        ];

        // ✅ حفظ المحادثة في Session (آخر 20 رسالة فقط)
        if (count($chatHistory) > 20) {
            $chatHistory = array_slice($chatHistory, -20);
        }
        $request->session()->put($key, $chatHistory);

        $executionTime = (microtime(true) - $start) * 1000;
        $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

        logger()->info('Quiz Submit', [
            'Execution Time (ms)' => $executionTime,
            'Memory (MB)' => $memoryUsage,
        ]);

        return response()->json([
            'success' => true,
            'reply' => $aiMessage,
            'timestamp' => now()->format('H:i')
        ]);

    } catch (\Throwable $e) {
        Log::error('Chat Error',[
        'message'=>$e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في الاتصال. حاول مرة أخرى.'
        ], 500);
    }
}

/**
 * جلب المحادثة السابقة
 */
public function getHistory(Request $request)
{
    $request->validate([
        'attempt_id' => ['nullable','integer']
    ]);

    $attemptId = $request->attempt_id;

    if ((int)$attemptId !== (int)session('current_attempt_id')) {
        abort(403);
    }

    $key = 'chat_history_' . ($attemptId ?? 'guest');

    $chatHistory = $request->session()->get($key, []);

    return response()->json([
        'success' => true,
        'history' => $chatHistory
    ]);
}

/**
 * مسح المحادثة
 */
public function clearHistory(Request $request)
{
    $request->validate([
        'attempt_id' => ['nullable','integer']
    ]);

    $attemptId = $request->attempt_id;

    if ((int)$attemptId !== (int)session('current_attempt_id')) {
        abort(403);
    }

    $key = 'chat_history_' . ($attemptId ?? 'guest');

    $request->session()->forget($key);

    return response()->json([
        'success' => true,
        'message' => 'تم مسح المحادثة'
    ]);
}

/**
 * بناء السياق حسب الصفحة
 */
private function buildContext(string $context, ?int $attemptId, ?int $alternativeIndex): array
{
    $contextData = [
        'context' => $context,
        'student_name' => 'الطالب',
    ];

    if ($attemptId && in_array($context, ['results', 'alternative'])) {
        $attempt = QuizAttempt::select([
                   'id',
                   'profile',
                   'ai_result',
                   'alternative_details'
                   ])->find($attemptId);

        if (!$attempt || (int) session('current_attempt_id') !== (int) $attempt->id) {
            abort(403);
        }

        $contextData['profile'] = $attempt->profile ?? [];
        $contextData['ai_result'] = $attempt->ai_result ?? [];

        if ($context === 'results') {
            $contextData['best_major'] = $attempt->ai_result['best_major']['name'] ?? 'غير محدد';
            $contextData['match_percentage'] = $attempt->ai_result['match_percentage'] ?? 0;
        }

        if ($context === 'alternative' && $alternativeIndex !== null) {
            $alternatives = $attempt->ai_result['alternatives'] ?? [];
            if (isset($alternatives[$alternativeIndex])) {
                $contextData['alternative_major'] = $alternatives[$alternativeIndex]['name'];
                $contextData['alternative_match'] = $alternatives[$alternativeIndex]['match'] ?? 0;

                // جلب التفاصيل المحفوظة
                $altDetails = $attempt->alternative_details ?? [];
                if (is_string($altDetails)) {
                    $altDetails = json_decode($altDetails, true) ?? [];
                }
                if (isset($altDetails[$alternativeIndex])) {
                    $contextData['alternative_details'] = $altDetails[$alternativeIndex];
                }
            }
        }
    }

    return $contextData;
}
}
