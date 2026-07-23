<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GroqService implements ChatServiceInterface
{
    private string $apiKey;
    private string $model;
    private int $timeout;

    // ✅ تعديل #3 & #4: Constants للأرقام السحرية
    private const MIN_REQUIRED_ANSWERS = 24;
    private const MAX_ANALYSIS_TOKENS = 5000;
    private const RETRY_TOKENS = 3000;
    private const MAX_OPEN_ANSWER_LENGTH = 150;
    private const MAX_OPEN_ANSWER_TRUNCATE = 147;
    private const CHAT_HISTORY_LIMIT = 10;
    private const CHAT_MIN_WORDS = 20;
    private const CHAT_MAX_WORDS = 60;
    private const RETRY_TIMES = 3;
    private const RETRY_SLEEP_MS = 1000;
    private const ARABIC_THRESHOLD = 0.2;

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->model = config('services.groq.model', 'llama-3.3-70b-versatile');
        $this->timeout = config('services.groq.timeout', 60);

        if (empty($this->apiKey)) {
            Log::critical('❌ Groq API Key not configured in services.groq.api_key');
        }
    }

    /**
     * ✅ فحص الـ API Key قبل كل طلب
     */
    private function ensureApiKey(): void
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Groq API Key not configured.');
        }
    }

    private function getOutputLanguage(string $lang): string
    {
        return $lang === 'ar' ? 'Arabic' : 'English';
    }

    private function validateAnswers(array $answers): ?array
    {
        // ✅ تعديل #3: استخدام constant
        if (count($answers) < self::MIN_REQUIRED_ANSWERS) {
            Log::warning('⚠️ Incomplete answers sent to Groq', [
                'count' => count($answers)
            ]);
            return [
                'status' => 'error',
                'message' => 'بيانات الإجابات غير مكتملة. يرجى إكمال جميع الأسئلة.'
            ];
        }

        // ✅ تعديل #3 جديد: التحقق من أن question_id فريدة
        $ids = collect($answers)
            ->pluck('question_id')
            ->unique();

        if ($ids->count() < self::MIN_REQUIRED_ANSWERS) {
            Log::warning('⚠️ Duplicate question_ids detected', [
                'total' => count($answers),
                'unique' => $ids->count()
            ]);
            return [
                'status' => 'error',
                'message' => 'تم اكتشاف إجابات مكررة. يرجى إعادة الاختبار.'
            ];
        }

        // ✅ تعديل #12: فحص بنية كل إجابة
        foreach ($answers as $index => $answer) {
            if (!is_array($answer)) {
                Log::warning('⚠️ Invalid answer structure', ['index' => $index]);
                return [
                    'status' => 'error',
                    'message' => 'بنية الإجابات غير صحيحة.'
                ];
            }

            if (!isset($answer['question_id'])) {
                Log::warning('⚠️ Missing question_id', ['index' => $index]);
                return [
                    'status' => 'error',
                    'message' => 'بعض الإجابات تفتقد معرّف السؤال.'
                ];
            }

            if (!array_key_exists('answer', $answer)) {
                Log::warning('⚠️ Missing answer field', ['index' => $index]);
                return [
                    'status' => 'error',
                    'message' => 'بعض الإجابات تفتقد نص الإجابة.'
                ];
            }
        }

        return null;
    }

    /**
     * ✅ الدالة الوحيدة للاتصال بـ Groq
     */
    private function callGroq(string $prompt): array
    {
        try {
            $this->ensureApiKey();

            $url = "https://api.groq.com/openai/v1/chat/completions";

            Log::info('🤖 Groq Request', [
                'model' => $this->model,
                'prompt_length' => strlen($prompt),
                'prompt_size_kb' => round(strlen($prompt) / 1024, 2)
            ]);

            $payload = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.3,
                'top_p' => 0.9,
                'frequency_penalty' => 0.2,
                'presence_penalty' => 0.0,
                'max_tokens' => self::MAX_ANALYSIS_TOKENS,
            ];

            // ✅ تعديل #1: retry مبسّط
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiKey)
                ->retry(self::RETRY_TIMES, self::RETRY_SLEEP_MS)
                ->post($url, $payload);

            // معالجة 413: Payload Too Large
            if ($response->status() === 413) {
                Log::warning('⚠️ Payload too large, retrying with reduced tokens');
                $payload['max_tokens'] = self::RETRY_TOKENS;
                $response = Http::timeout($this->timeout)
                    ->withToken($this->apiKey)
                    ->post($url, $payload);
            }

            if (!$response->successful()) {
                Log::error('❌ Groq API Error', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500)
                ]);

                if ($response->status() === 429) {
                    return [
                        'status' => 'error',
                        'message' => 'تم تجاوز حد الطلبات. يرجى الانتظار دقيقة والمحاولة مرة أخرى.'
                    ];
                }

                return [
                    'status' => 'error',
                    'message' => "خطأ من Groq: HTTP {$response->status()}"
                ];
            }

            $data = $response->json();

            // ✅ تعديل #11: التحقق من وجود choices
            if (empty($data['choices'][0]['message']['content'])) {
                Log::error('❌ Empty response from Groq', [
                    'data' => json_encode($data)
                ]);
                return ['status' => 'error', 'message' => 'استجابة فارغة من Groq'];
            }

            $text = $data['choices'][0]['message']['content'];

            // تنظيف الرد
            $text = str_replace(['```json', '```', '```JSON', '```Json'], '', $text);
            $text = trim($text);

            // ✅ تعديل #8: regex أكثر مرونة
            if (preg_match('/\{.*\}/s', $text, $matches)) {
                $text = trim($matches[0]);
            }

            $result = json_decode($text, true);

            if (!is_array($result)) {
                Log::error('❌ Invalid JSON from Groq', [
                    'raw' => substr($text, 0, 500),
                    'json_error' => json_last_error_msg()
                ]);
                return [
                    'status' => 'error',
                    'message' => 'استجابة JSON غير صالحة',
                    'raw_response' => substr($text, 0, 500)
                ];
            }

            if (($result['status'] ?? null) === 'validation_failed') {
                Log::info('⚠️ Validation failed from Groq', [
                    'message' => $result['message'] ?? 'Unknown'
                ]);
                return $result;
            }

            Log::info('✅ Groq analysis successful', [
                'status' => $result['status'] ?? 'unknown',
                'best_major' => $result['best_major']['name'] ?? 'N/A',
                'response_length' => strlen($text)
            ]);

            return $result;

        // ✅ تعديل #13: Throwable بدلاً من Exception
        } catch (\Throwable $e) {
            Log::error('❌ Groq Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'status' => 'error',
                'message' => 'خطأ: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ جلب تفاصيل تخصص بديل محدد
     */
    public function getAlternativeDetails(string $majorName, array $profile, array $answers, string $language = 'ar'): array
    {
        $validationError = $this->validateAnswers($answers);
        if ($validationError !== null) {
            return $validationError;
        }

        $outputLanguage = $this->getOutputLanguage($language);

        $simplifiedAnswers = collect($answers)
            ->map(function($a) {
                $answer = $a['answer'] ?? '';
                if (is_string($answer) && strlen($answer) > self::MAX_OPEN_ANSWER_LENGTH) {
                    $answer = substr($answer, 0, self::MAX_OPEN_ANSWER_TRUNCATE) . '...';
                }
                return [
                    'q' => $a['question_id'] ?? 0,
                    'a' => $answer
                ];
            })
            ->toArray();

        $profileJson = json_encode($profile, JSON_UNESCAPED_UNICODE);
        $answersJson = json_encode($simplifiedAnswers, JSON_UNESCAPED_UNICODE);

        $prompt = <<<PROMPT
You are AlMostashar, an expert career advisor.

STUDENT PROFILE:
{$profileJson}

STUDENT ANSWERS:
{$answersJson}

TARGET MAJOR: {$majorName}

Provide detailed analysis for {$majorName} in {$outputLanguage}.

Return ONLY valid JSON:
{
"status": "success",
"major_name": "{$majorName}",
"match": 82,
"reason": "≤180 chars: why this fits the student",
"why_this_major": ["reason1", "reason2", "reason3"],
"what_you_will_study": ["subject1", "subject2", "subject3"],
"required_skills": ["skill1", "skill2", "skill3", "skill4", "skill5"],
"career_paths": ["job1", "job2", "job3", "job4"],
"study_description": "≤200 chars",
"work_description": "≤200 chars",
"strengths_for_this_major": ["strength1", "strength2", "strength3"],
"challenges": ["challenge1", "challenge2"],
"roadmap": {
"month1": ["task1", "task2", "task3"],
"month2": ["task1", "task2", "task3"],
"month3": ["task1", "task2", "task3"]
},
"market_info": {
"job_opportunities": "≤150 chars: description of job market opportunities",
"average_salary": "≤100 chars: average salary range",
"global_demand": "≤100 chars: demand level"
},
"encouragement": "≤250 chars"
}

Rules:
- Use {$outputLanguage} for all text
- Keys in English
- NO question numbers
- Be personal (use 'أنت' / 'You')
- Return ONLY JSON
PROMPT;

        return $this->callGroq($prompt);
    }

    /**
     * ✅ محادثة مع سياق الصفحة
     */
    public function chatWithContext(string $userMessage, array $chatHistory, string $context, array $contextData): array {
        $start = microtime(true);
        
        try {
            $this->ensureApiKey();
        } catch (\Throwable $e) {
            return [
                'status' => 'error',
                'reply' => 'عذراً، الخدمة غير متاحة حالياً.'
            ];
        }

        $userLang = $this->detectLanguage($userMessage);
        $outputLanguage = $this->getOutputLanguage($userLang);

        $appKnowledge = $this->getAppKnowledge($userLang);
        $contextPrompt = $this->buildContextPrompt($context, $contextData, $userLang);

        $recentHistory = array_slice($chatHistory, -self::CHAT_HISTORY_LIMIT);
        $historyText = '';
        foreach ($recentHistory as $msg) {
            $role = $msg['role'] === 'user' ? 'User' : 'Advisor';
            $historyText .= "{$role}: {$msg['content']}\n";
        }

        $minWords = self::CHAT_MIN_WORDS;
        $maxWords = self::CHAT_MAX_WORDS;

        $prompt = <<<PROMPT
        You are AlMostashar (المستشار), a smart career advisor chatbot.
        === APP KNOWLEDGE (USE THIS FOR ANSWERS) ===
        {$appKnowledge}
        === CURRENT PAGE CONTEXT ===
        {$contextPrompt}
        === RECENT CONVERSATION ===
        {$historyText}
        === USER MESSAGE ===
        {$userMessage}
        === STRICT RULES ===
        1. Answer in {$outputLanguage} (match user's language)
        2. Be DIRECT and SPECIFIC - answer the exact question asked
        3. Use ONLY the information from APP KNOWLEDGE and PAGE CONTEXT
        4. If asked about numbers (questions count, etc.), give EXACT numbers
        5. Keep responses SHORT: {$minWords}-{$maxWords} words maximum
        6. NO generic marketing language
        7. NO repetition of the question
        8. If you don't know, say "I don't have this information"
        9. Be friendly but concise
        10. Use emojis sparingly (max 1 per message)
        === RESPONSE FORMAT ===
        Return ONLY valid JSON:
        {
        "status": "success",
        "reply": "Your concise answer here ({$minWords}-{$maxWords} words)"
        }
        Return ONLY valid JSON object now.
        PROMPT;

        $result = $this->callGroq($prompt);

        if (($result['status'] ?? null) === 'error') {
            return $result;
        }

        $executionTime = (microtime(true) - $start) * 1000;
        $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

        Log::info('Chatbot Performance', [
            'execution_time_ms' => round($executionTime,2),
            'memory_usage_mb' => round($memoryUsage,2),
        ]);

        return [
            'status' => 'success',
            'reply' => $result['reply'] ?? 'عذراً، لم أتمكن من فهم سؤالك.'
        ];
    }

    /**
     * ✅ تعديل #5: كشف لغة رسالة المستخدم مع حماية من division by zero
     */
    private function detectLanguage(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return 'ar';
        }

        $arabicChars = preg_match_all('/[\p{Arabic}]/u', $text);
        $totalChars = mb_strlen($text);

        return ($arabicChars / $totalChars) > self::ARABIC_THRESHOLD ? 'ar' : 'en';
    }

    /**
     * ✅ قاعدة معرفة مبسّطة
     */
    private function getAppKnowledge(string $lang): string
    {
        $appName = $lang === 'ar' ? 'المستشار (AlMostashar)' : 'AlMostashar (المستشار)';
        $targetAudience = $lang === 'ar'
            ? 'طلاب المرحلة الثانوية وطلاب الجامعات الذين يختارون مسارهم المهني'
            : 'High school students and university students choosing their career path';

        return <<<KB
APP NAME: {$appName}
PURPOSE: Career guidance platform that helps students discover their ideal major/career
QUIZ STRUCTURE:
- Total questions: 26
- Multiple choice questions: 24
- Open-ended questions: 2 (questions 25 and 26)
- Estimated time: 5-7 minutes
FEATURES:
- Personality analysis across 10 dimensions (analysis, creativity, leadership, communication, research, business, technology, humanitarian, scientific, adaptability)
- Best major recommendation with match percentage
- 2 alternative majors
- Detailed career paths
- 3-month roadmap to get started
- Market information (job opportunities, average salary, global demand)
- Smart chatbot advisor (me!) for follow-up questions
PRICING: Completely free, no subscription required
TARGET AUDIENCE: {$targetAudience}
TECHNOLOGY: Gemini AI for personality analysis and career recommendations. Groq (Llama) for chatbot conversations.
LANGUAGES: Arabic and English
KB;
    }


    private function buildContextPrompt(string $context, array $contextData, string $lang): string
    {
        switch ($context) {
            case 'home':
                return $lang === 'ar'
                    ? "الصفحة: الرئيسية (قبل الاختبار)\nالطالب لم يأخذ الاختبار بعد.\nأجب عن أسئلة عامة حول التطبيق."
                    : "Page: Homepage (before quiz)\nStudent has NOT taken the quiz yet.\nAnswer general questions about the app.";

            case 'results':
                $bestMajor = $contextData['best_major'] ?? 'غير محدد';
                $matchPercentage = $contextData['match_percentage'] ?? 0;
                $aiResult = $contextData['ai_result'] ?? [];

                $strengths = implode(', ', $aiResult['strengths'] ?? []);
                $alternatives = collect($aiResult['alternatives'] ?? [])
                    ->pluck('name')
                    ->implode(', ');

                if ($lang === 'ar') {
                    return <<<CTX
                    الصفحة: النتائج (بعد الاختبار)
                    التخصص الأفضل: {$bestMajor}
                    نسبة التوافق: {$matchPercentage}%
                    نقاط القوة: {$strengths}
                    التخصصات البديلة: {$alternatives}
                    أجب عن أسئلة حول:
                    - سبب ملاءمة هذا التخصص
                    - نقاط القوة والضعف
                    - المسارات المهنية
                    - كيفية البدء
                    CTX;
                }

                return <<<CTX
                Page: Results (after quiz)
                Best major: {$bestMajor}
                Match: {$matchPercentage}%
                Strengths: {$strengths}
                Alternatives: {$alternatives}
                Answer questions about why this major fits, strengths, career paths, how to start.
                CTX;

            case 'alternative':
                $altMajor = $contextData['alternative_major'] ?? 'غير محدد';
                $altMatch = $contextData['alternative_match'] ?? 0;
                $bestMajor = $contextData['ai_result']['best_major']['name'] ?? 'غير محدد';

                if ($lang === 'ar') {
                    return <<<CTX
                    الصفحة: تفاصيل التخصص البديل
                    التخصص البديل الحالي: {$altMajor}
                    نسبة التوافق: {$altMatch}%
                    التخصص الأفضل كان: {$bestMajor}
                    أجب عن:
                    - هذا التخصص البديل
                    - المقارنة مع التخصص الأفضل
                    - المسارات المهنية
                    CTX;
                }

                return <<<CTX
                Page: Alternative major details
                Current alternative: {$altMajor}
                Match: {$altMatch}%
                Best major was: {$bestMajor}
                Answer about this alternative, comparison with best major, career paths.
                CTX;


            default:
                return $lang === 'ar'
                    ? 'لا يوجد سياق محدد.'
                    : 'No specific context.';
        }
    }
}
