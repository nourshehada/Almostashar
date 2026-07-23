<?php

namespace App\Services\AI;

interface ChatServiceInterface
{
    /**
     * محادثة مع سياق الصفحة
     */
    public function chatWithContext(
        string $userMessage,
        array $chatHistory,
        string $context,
        array $contextData
    ): array;
}
