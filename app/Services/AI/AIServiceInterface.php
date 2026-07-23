<?php

namespace App\Services\AI;

interface AIServiceInterface
{
    /**
     * التحليل الكامل للإجابات
     */
    public function analyze(
        array $answers,
        array $profile,
        string $language = 'ar'
    ): array;

    /**
     * جلب تفاصيل تخصص بديل محدد
     */
    public function getAlternativeDetails(
        string $majorName,
        array $profile,
        array $answers,
        string $language = 'ar'
    ): array;
}
