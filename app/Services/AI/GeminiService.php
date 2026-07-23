<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AIServiceInterface
{
    private string $apiKey;
    private string $model;
    private int $timeout;

    // ✅ تعديل #5: أسماء النماذج ثابتة
    private const FALLBACK_MODELS = [
        'gemini-1.5-flash',
        'gemini-1.5-pro',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
        $this->timeout = config('services.gemini.timeout', 90);

        if (empty($this->apiKey)) {
            Log::critical('❌ Gemini API Key not configured in services.gemini.api_key');
        }
    }

    private function ensureApiKey(): void
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Gemini API Key not configured.');
        }
    }

    private function callGemini(
        string $prompt,
        string $requestType = 'analysis'
    ): array
    {
        try {
            $this->ensureApiKey();

            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

            Log::info('🤖 Gemini Request', [
                'model' => $this->model,
                'prompt_length' => strlen($prompt)
            ]);

            $response = Http::timeout($this->timeout)
                ->retry(3, 2000, throw: false)
                ->post($url, [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => 8192,
                        'topP' => 0.9,
                    ]
                ]);

            if ($response->status() === 503) {
                Log::warning('⚠️ Model 503, trying fallback');
                $response = $this->tryFallbackModels($prompt);
            }

            if (!$response->successful()) {
                Log::error('❌ Gemini API Error', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500)
                ]);

                return [
                    'status' => 'error',
                    'message' => "خطأ من Gemini: HTTP {$response->status()}. حاول مرة أخرى بعد قليل."
                ];
            }

            $data = $response->json();

            $finishReason = $data['candidates'][0]['finishReason'] ?? null;

            if ($finishReason === 'MAX_TOKENS') {
                Log::warning('Response truncated by Gemini');
                return [
                    'status' => 'error',
                    'message' => 'AI response was truncated.'
                ];
            }

            if (in_array($finishReason, ['SAFETY', 'RECITATION', 'BLOCKLIST'], true)) {
                return [
                    'status' => 'error',
                    'message' => 'تم رفض الرد من Gemini بسبب سياسات الأمان'
                ];
            }

            $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (empty($text)) {
                Log::error('❌ Empty response from Gemini', [
                    'data_keys' => array_keys($data ?? [])
                ]);
                return [
                    'status' => 'error',
                    'message' => 'استجابة فارغة من Gemini'
                ];
            }

            // تنظيف الرد
            $text = str_replace(['```json', '```', '```JSON', '```Json'], '', $text);
            $text = trim($text);

            // ✅ تعديل #3: استخراج JSON بدالة بسيطة
            $text = $this->extractJson($text);

            $result = json_decode($text, true);

            if (!is_array($result)) {
                Log::error('❌ Invalid JSON from Gemini', [
                    'raw' => substr($text, 0, 500),
                    'json_error' => json_last_error_msg()
                ]);

                return [
                    'status' => 'error',
                    'message' => 'استجابة JSON غير صالحة',
                    'raw_response' => substr($text, 0, 500)
                ];
            }

            $result = json_decode($text, true);

            if (!is_array($result)) {
                Log::error('❌ Invalid JSON from Gemini', [
                    'raw' => substr($text, 0, 500),
                    'json_error' => json_last_error_msg()
                ]);

                return [
                    'status' => 'error',
                    'message' => 'استجابة JSON غير صالحة',
                    'raw_response' => substr($text, 0, 500)
                ];
            }

            // ✅ تعديل #7: إذا كان الرد validation_failed، إرجاعه مباشرة
            if (($result['status'] ?? null) === 'validation_failed') {
                Log::info('⚠️ Validation failed from Gemini', [
                    'message' => $result['message'] ?? 'Unknown'
                ]);
                return $result;
            }

            // ✅ التحقق من البنية حسب نوع الطلب
            $validationError = $requestType === 'alternative'
                ? $this->validateAlternativeResponse($result)
                : $this->validateAnalysisResponse($result);

            if ($validationError !== null) {
                return $validationError;
            }

            // ✅ تعديل #4: تحويل الأنواع للـ percentages
            $result = $this->normalizePercentages($result);

            Log::info('✅ Gemini analysis successful', [
                'status' => $result['status'] ?? 'unknown',
                'request_type' => $requestType,
                'best_major' => $result['best_major']['name'] ?? $result['major_name'] ?? 'N/A'
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('❌ Gemini Exception: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'خطأ: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ✅ تعديل #3: استخراج JSON بدالة بسيطة
     */
    private function extractJson(string $text): string
    {
        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start === false || $end === false) {
            return $text;
        }

        return substr($text, $start, $end - $start + 1);
    }

    /**
     * ✅ تعديل #5: محاولة نماذج بديلة مع أسماء ثابتة
     */
    private function tryFallbackModels(string $prompt)
    {
        $lastResponse = null;

        foreach (self::FALLBACK_MODELS as $fallbackModel) {
            if ($fallbackModel === $this->model) continue;

            Log::info("🔄 Trying fallback: $fallbackModel");

            $fallbackUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$fallbackModel}:generateContent?key={$this->apiKey}";

            $lastResponse = Http::timeout($this->timeout)
                ->retry(2, 1000, throw: false)
                ->post($fallbackUrl, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => 8192,
                    ]
                ]);

            if ($lastResponse->successful()) {
                Log::info("✅ Fallback successful: $fallbackModel");
                return $lastResponse;
            }

            Log::warning("❌ Fallback failed: $fallbackModel", [
                'status' => $lastResponse->status()
            ]);
        }

        if ($lastResponse) {
            return $lastResponse;
        }

        throw new \Exception('All Gemini fallback models failed.');
    }

    /**
     * ✅ تعديل #4: تحويل الأنواع للـ percentages
     */
    private function normalizePercentages(array $result): array
    {
        // تحويل match_percentage
        if (isset($result['match_percentage'])) {
            $result['match_percentage'] = (int)$result['match_percentage'];
        }

        // تحويل best_major.match
        if (isset($result['best_major']['match'])) {
            $result['best_major']['match'] = (int)$result['best_major']['match'];
        }

        // تحويل alternatives[].match
        if (isset($result['alternatives']) && is_array($result['alternatives'])) {
            foreach ($result['alternatives'] as &$alt) {
                if (isset($alt['match'])) {
                    $alt['match'] = (int)$alt['match'];
                }
            }
        }

        // تحويل match للتخصص البديل
        if (isset($result['match']) && isset($result['major_name'])) {
            $result['match'] = (int)$result['match'];
        }

        return $result;
    }

    /**
     * ✅ التحقق من بنية التحليل الرئيسي
     */
    private function validateAnalysisResponse(array $result): ?array
    {
        if (count($result['strengths'] ?? []) !== 3) {
            return [
                'status' => 'error',
                'message' => 'Invalid strengths count'
            ];
        }

        if (count($result['development_areas'] ?? []) !== 2) {
            return [
                'status' => 'error',
                'message' => 'Invalid development areas count'
            ];
        }

        if (count($result['alternatives'] ?? []) !== 2) {
            return [
                'status' => 'error',
                'message' => 'Invalid alternatives count'
            ];
        }

        if (
            !is_numeric($result['match_percentage'] ?? null) ||
            $result['match_percentage'] < 0 ||
            $result['match_percentage'] > 100
        ) {
            return [
                'status' => 'error',
                'message' => 'Invalid match percentage'
            ];
        }

        if (($result['status'] ?? null) !== 'success') {
            return [
                'status' => 'error',
                'message' => 'Invalid AI response status'
            ];
        }

        $requiredFields = [
            'match_percentage',
            'best_major',
            'market_info',
            'personality_summary',
            'strengths',
            'development_areas',
            'alternatives',
            'roadmap',
            'encouragement'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($result[$field])) {
                return [
                    'status' => 'error',
                    'message' => "Missing field: {$field}"
                ];
            }
        }

        return null;
    }

    /**
     * التحقق من بنية التخصص البديل
     */
    private function validateAlternativeResponse(array $result): ?array
    {
        $requiredFields = [
            'major_name',
            'match',
            'reason',
            'why_this_major',
            'what_you_will_study',
            'required_skills',
            'study_description',
            'work_description',
            'roadmap',
            'market_info',
            'encouragement'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($result[$field])) {
                return [
                    'status' => 'error',
                    'message' => "Missing field: {$field}"
                ];
            }
        }

        if (($result['status'] ?? null) !== 'success') {
            return [
                'status' => 'error',
                'message' => 'Invalid AI response status'
            ];
        }

        return null;
    }

    public function analyze(array $answers, array $profile, string $language = 'ar'): array
    {
        $start = microtime(true);

        $outputLanguage = $language === 'ar' ? 'Arabic language' : 'English language';

        $openAnswers = collect($answers)
            ->filter(fn($a) => in_array($a['question_id'] ?? 0, [25, 26]))
            ->pluck('answer')
            ->filter()
            ->values()
            ->toArray();

        $openAnswersJson = !empty($openAnswers)
            ? $this->jsonEncode($openAnswers)
            : '[]';

    $prompt = "You are AlMostashar, an expert career psychologist and academic advisor.

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    📊 INPUT DATA
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    STUDENT'S PROFILE SCORES (0-10 scale, higher = stronger tendency):
    " . $this->jsonEncode($profile) . "

    STUDENT'S ANSWERS (26 questions):
    " . $this->jsonEncode($answers) . "

    OPEN-ENDED ANSWERS (for validation):
    {$openAnswersJson}

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🎯 STEP 1: VALIDATION (CRITICAL!)
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    Check the OPEN-ENDED ANSWERS carefully:

    If they contain:
    - random characters (like 'asdfgh', 'jh klfl')
    - meaningless text
    - repeated letters
    - gibberish
    - very short answers that do not express a clear idea
    - answers that do not contain enough information to understand the student's opinion

    AND if BOTH answers are invalid:

    Return ONLY this and STOP:
    {
    \"status\": \"validation_failed\",
    \"message\": \"بعض إجاباتك المفتوحة غير واضحة. يرجى إعادة صياغتها بشكل أكثر تفصيلاً.\"
    }

    IMPORTANT VALIDATION RULES:
    - If at least one open answer is meaningful → continue normally
    - If both answers are empty → continue normally
    - If one is valid and one is invalid → continue normally (use only the valid one)
    - ONLY reject if BOTH are invalid/meaningless

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🎯 STEP 2: DEEP PERSONAL ANALYSIS
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    Your mission: Make the student feel like you've KNOWN them for years, WITHOUT EXAGGERATION.

    When they read your analysis, they should think:
    💭 \"Wow... this AI truly understands ME!\"
    💭 \"It's like you've been watching my life!\"

    But be honest and grounded. Do not exaggerate or make claims you cannot support.

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🧠 ANALYSIS METHODOLOGY (CRITICAL!)
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    The profile scores are derived from the student's answers.
    They are only SUPPORTING SIGNALS.

    Your analysis MUST primarily rely on the STUDENT'S ANSWERS.
    Use profile scores only to confirm patterns, never as the main source.

    Use ALL available information:
    - Do not rely on only one answer
    - Identify patterns across the entire questionnaire
    - Connect multiple answers to reveal deep insights
    - Base every insight ONLY on the provided answers and profile
    - NEVER invent memories, life events, childhood experiences, or facts not supported by the input

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🧠 PERSONALIZATION RULES
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    1️⃣ USE DIRECT ADDRESS
    ❌ WRONG: \"The student prefers...\"
    ✅ RIGHT: \"أنت تميل إلى...\" / \"You tend to...\"
    ✅ RIGHT: \"شخصيتك تجمع بين...\" / \"Your personality combines...\"

    2️⃣ AVOID REPETITION
    Do NOT start every sentence with \"أنت\" / \"You\"
    Vary your sentence structure to make the text natural

    3️⃣ BE SPECIFIC, NOT GENERIC
    ❌ WRONG: \"You are a good leader\"
    ✅ RIGHT: \"عندما واجهت موقفاً صعباً، اخترت قيادة الفريق بدلاً من الانتظار، وهذا يكشف عن قدرتك الطبيعية على تحمل المسؤولية\"

    4️⃣ SHOW PATTERNS (without question numbers!)
    ✅ RIGHT: \"في عدة إجابات مختلفة، اخترت الحلول الإبداعية، مما يؤكد أن الإبداع جزء أساسي من شخصيتك\"
        WRONG: \"في الأسئلة 3، 8، 15 اخترت...\"
        WRONG: \"From your answer to question 5...\"

    5️⃣ USE PSYCHOLOGICAL INSIGHTS
    ✅ \"أنت لا تحب العمل الجماعي فحسب، بل تحتاج إليه نفسياً لتشعر بالإنجاز\"

    6️⃣ BE WARM BUT HONEST
    - Praise genuinely (not flattery)
    - Point out weaknesses constructively
    - Show you understand their struggles

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🎓 MAJOR SELECTION RULES
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    You MUST choose majors that are:
    ✅ COMMON and WIDELY AVAILABLE in most universities worldwide
    ✅ WELL-ESTABLISHED academic programs
    ✅ RECOGNIZED internationally
    ✅ HAVE clear career paths

    You MUST AVOID:
    ❌ Rare or niche majors
    ❌ Highly specialized programs only in few universities
    ❌ Experimental or new fields without established curriculum
    ❌ Majors that are not offered in most universities

    Examples of GOOD majors:
    - Computer Science, Software Engineering, IT
    - Medicine, Pharmacy, Nursing
    - Business Administration, Marketing, Finance
    - Civil/Mechanical/Electrical Engineering
    - Law, Psychology, Education
    - Architecture, Graphic Design, Media

    Choose majors based on:
    - Personality
    - Interests
    - Cognitive strengths
    - Motivation
    - Behavioral patterns
    - Profile scores (as supporting evidence)

    IMPORTANT:
    - The recommended majors must be DIFFERENT from each other
    - Avoid suggesting highly similar majors
    - Examples: Do NOT suggest \"Computer Science\" + \"Software Engineering\" + \"IT\" (too similar)
    - Each major should represent a distinct field

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    📝 CRITICAL CONTENT RULES
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    1️⃣ MAJOR NAME - ONE LANGUAGE ONLY:
    ❌ WRONG: \"Computer Science (علوم الحاسب)\"
    ❌ WRONG: \"إدارة الأعمال (Business Administration)\"
    ✅ RIGHT: \"علوم الحاسب\" (if Arabic output)
    ✅ RIGHT: \"Computer Science\" (if English output)
    → Apply this to best_major.name AND alternatives names

    2️⃣ WHY_THIS_MAJOR - NO QUESTION NUMBERS:
    ❌ WRONG: \"في السؤال 12 أظهرت...\"
        WRONG: \"From your answer to question 5...\"
    ❌ WRONG: \"إجابتك في السؤال 3 تكشف...\"
    ✅ RIGHT: \"تمتلك قدرة طبيعية على التحليل المنطقي...\"
    → Focus on the TRAIT, not the question number

    3️⃣ PERSONALITY_SUMMARY & STRENGTHS:
    ✅ You can say: \"في عدة إجابات...\" or \"in multiple answers...\"
        NEVER say: \"السؤال 5\" or \"question 12\"

    4️⃣ NO REPETITION ACROSS FIELDS:
    Do not repeat the same idea in different fields
    Each section should provide NEW information
    personality_summary ≠ strengths ≠ reason ≠ encouragement

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🔢 EXACT COUNTS (CRITICAL!)
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    You MUST provide EXACTLY these counts:
    - best_major.why_this_major: Exactly 3 reasons
    - best_major.what_you_will_study: Exactly 3 subjects
    - best_major.required_skills: Exactly 5 skills
    - best_major.career_paths: Exactly 4 paths
    - alternatives: Exactly 2 majors
    - strengths: Exactly 3 strengths
    - development_areas: Exactly 2 areas
    - roadmap.month1: Exactly 3 tasks
    - roadmap.month2: Exactly 3 tasks
    - roadmap.month3: Exactly 3 tasks

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🔢 DATA TYPES (CRITICAL!)
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    - match_percentage: Integer from 0 to 100 (no decimals, no percentages symbol)
    - best_major.match: Integer from 0 to 100
    - alternatives[].match: Integer from 0 to 100

    ❌ WRONG: 84.55, \"84%\", \"approximately 84\"
    ✅ RIGHT: 85

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    🛠️ REQUIRED SKILLS FORMAT
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    Each skill must be a SHORT KEYWORD (1-3 words, MAX 20 characters):

    ✅ CORRECT: \"Python\", \"SQL\", \"Git\", \"Teamwork\", \"Problem Solving\"
    ❌ WRONG: \"Python/Java Programming\", \"Database Management with SQL\"

    IMPORTANT: The skills must MATCH the selected major!
    - For Medicine: \"Anatomy\", \"Patient Care\", \"Diagnosis\" (NOT \"Python\")
    - For Business: \"Leadership\", \"Marketing\", \"Finance\"
    - Examples are just examples, adapt to the major

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    ⚠️ STRICT LENGTH RULES
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    - best_major.reason: MAX 180 characters
    - best_major.why_this_major (each): MAX 100 characters
    - best_major.what_you_will_study (each): MAX 50 characters
    - best_major.required_skills (each): MAX 20 characters
    - best_major.career_paths (each): MAX 50 characters
    - best_major.study_description: MAX 200 characters
    - best_major.work_description: MAX 200 characters
    - personality_summary: MAX 350 characters
    - strengths (each): MAX 100 characters
    - development_areas (each): MAX 100 characters
    - alternatives.reason (each): MAX 120 characters
    - roadmap tasks (each): MAX 60 characters
    - encouragement: MAX 250 characters
    - best_major.market_info.job_opportunities: MAX 150 characters
    - best_major.market_info.average_salary: MAX 100 characters
    - best_major.market_info.global_demand: MAX 100 characters

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    📝 OUTPUT FORMAT
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    Return ONLY valid JSON written in {$outputLanguage} (keys in English):

    {
    \"status\": \"success\",
    \"match_percentage\": 85,
    \"best_major\": {
        \"name\": \"Major Name in ONE language only\",
        \"match\": 85,
        \"overview\": \"Brief description of the major (100-150 chars). Example: Computer Science is a field that studies computation, programming, and algorithms to solve real-world problems.\",
        \"reason\": \"≤180 chars: PERSONAL reason using 'you/أنت'\",
        \"why_this_major\": [
        \"≤100 chars: trait-based reason (NO question numbers!)\",
        \"≤100 chars: trait-based reason (NO question numbers!)\",
        \"≤100 chars: trait-based reason (NO question numbers!)\"
        ],
        \"what_you_will_study\": [\"≤50 chars\", \"≤50 chars\", \"≤50 chars\"],
        \"required_skills\": [\"skill1\", \"skill2\", \"skill3\", \"skill4\", \"skill5\"],
        \"career_paths\": [\"≤50 chars\", \"≤50 chars\", \"≤50 chars\", \"≤50 chars\"],
        \"study_description\": \"≤200 chars\",
        \"work_description\": \"≤200 chars\"
    },
    \"market_info\": {
        \"job_opportunities\": \"≤150 chars: description of job market\",
        \"average_salary\": \"≤100 chars: salary range\",
        \"global_demand\": \"≤100 chars: demand level\"
    },
    \"personality_summary\": \"≤350 chars: DEEP profile (NO question numbers)\",
    \"strengths\": [
        \"≤100 chars: strength + evidence (NO question numbers)\",
        \"≤100 chars: strength + evidence (NO question numbers)\",
        \"≤100 chars: strength + evidence (NO question numbers)\"
    ],
    \"development_areas\": [
        \"≤100 chars: area + how to improve\",
        \"≤100 chars: area + how to improve\"
    ],
    \"alternatives\": [
        {\"name\": \"Major in ONE language\", \"match\": 75, \"reason\": \"≤120 chars\"},
        {\"name\": \"Major in ONE language\", \"match\": 70, \"reason\": \"≤120 chars\"}
    ],
    \"roadmap\": {
        \"month1\": [\"≤60 chars\", \"≤60 chars\", \"≤60 chars\"],
        \"month2\": [\"≤60 chars\", \"≤60 chars\", \"≤60 chars\"],
        \"month3\": [\"≤60 chars\", \"≤60 chars\", \"≤60 chars\"]
    },
    \"encouragement\": \"≤250 chars: VERY PERSONAL message\"
    }

    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
    ⚠️ FINAL RULES (READ CAREFULLY!)
    ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

    1. Every field shown in the JSON schema is REQUIRED
    2. Do not omit any field
    3. Never return null
    4. Always fill every property
    5. Use {$outputLanguage} for all text values
    6. Keep keys in English
    7. Do not use Markdown
    8. Do not wrap the JSON inside code fences
    9. Return raw JSON only
    10. Output must begin with { and end with }
    11. Do NOT explain anything outside the JSON
    12. Do not repeat the same idea in different fields
    13. Make sure all majors are different from each other

    IMPORTANT:
    Return ONLY a valid JSON object.
    Do NOT wrap it inside markdown.
    Do NOT use \`\`\`json.
    Do NOT explain anything.
    Output must begin with { and end with }.";

            $executionTime = (microtime(true) - $start) * 1000;
            $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

            Log::info('Gemini Analysis Performance', [
                'execution_time_ms' => round($executionTime,2),
                'memory_usage_mb' => round($memoryUsage,2),
            ]);

            return $this->callGemini($prompt, 'analysis');
        }

        public function getAlternativeDetails(string $majorName, array $profile, array $answers, string $language = 'ar'): array
        {
            $start = microtime(true);

            $outputLanguage = $language === 'ar' ? 'Arabic' : 'English';

            $prompt = "You are AlMostashar, an expert career advisor.

    STUDENT PROFILE:
    " . $this->jsonEncode($profile) . "

    STUDENT ANSWERS:
    " . $this->jsonEncode($answers) . "

    TARGET MAJOR: {$majorName}

    Provide detailed analysis for {$majorName} in {$outputLanguage}.

    Return ONLY valid JSON:
    {
    \"status\": \"success\",
    \"major_name\": \"{$majorName}\",
    \"match\": 82,
    \"reason\": \"≤180 chars: why this fits the student\",
    \"why_this_major\": [\"reason1\", \"reason2\", \"reason3\"],
    \"what_you_will_study\": [\"subject1\", \"subject2\", \"subject3\"],
    \"required_skills\": [\"skill1\", \"skill2\", \"skill3\", \"skill4\", \"skill5\"],
    \"career_paths\": [\"job1\", \"job2\", \"job3\", \"job4\"],
    \"study_description\": \"≤200 chars\",
    \"work_description\": \"≤200 chars\",
    \"roadmap\": {
        \"month1\": [\"task1\", \"task2\", \"task3\"],
        \"month2\": [\"task1\", \"task2\", \"task3\"],
        \"month3\": [\"task1\", \"task2\", \"task3\"]
    },
    \"market_info\": {
        \"job_opportunities\": \"≤150 chars: description of job market opportunities\",
        \"average_salary\": \"≤100 chars: average salary range (e.g., '\$50,000-\$80,000' or '3000-5000 USD')\",
        \"global_demand\": \"≤100 chars: demand level (e.g., 'High demand globally', 'Moderate demand')\"
    },
    \"encouragement\": \"≤250 chars\"
    }

    Rules:
    - Use {$outputLanguage} for all text
    - Keys in English
    - NO question numbers
    - Be personal (use 'أنت' / 'You')
    - Return ONLY JSON";

        $executionTime = (microtime(true) - $start) * 1000;
        $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

        Log::info('Chatbot Performance', [
            'execution_time_ms' => round($executionTime,2),
            'memory_usage_mb' => round($memoryUsage,2),
        ]);

        return $this->callGemini($prompt, 'alternative');
    }

    private function jsonEncode($data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }
}
