<?php

namespace App\Rules;

use App\Services\ContentService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NoSpamContent implements ValidationRule
{
    protected ContentService $contentService;
    protected array $spamKeywords;
    protected array $suspiciousPatterns;

    /**
     * Create a new rule instance.
     */
    public function __construct()
    {
        $this->contentService = app(ContentService::class);
        $this->loadSpamDetectionData();
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail('The :attribute must be a string.');
            return;
        }

        // Quick length check
        if (strlen($value) < 10) {
            $fail('The :attribute is too short and appears to be spam.');
            return;
        }

        // Check for spam keywords
        if ($this->containsSpamKeywords($value)) {
            $fail('The :attribute contains prohibited content.');
            return;
        }

        // Check for suspicious patterns
        if ($this->hasSuspiciousPatterns($value)) {
            $fail('The :attribute contains suspicious patterns that may indicate spam.');
            return;
        }

        // Check for excessive repetition
        if ($this->hasExcessiveRepetition($value)) {
            $fail('The :attribute contains too much repetitive content.');
            return;
        }

        // Check for link spam
        if ($this->isLinkSpam($value)) {
            $fail('The :attribute contains too many links or suspicious URLs.');
            return;
        }

        // Check for AI/bot-generated content patterns
        if ($this->appearsBotGenerated($value)) {
            $fail('The :attribute appears to be automatically generated content.');
            return;
        }

        // Advanced spam detection using external service (if configured)
        if ($this->failsExternalSpamCheck($value)) {
            $fail('The :attribute has been flagged as potential spam by our content filter.');
            return;
        }
    }

    /**
     * Load spam detection keywords and patterns
     */
    protected function loadSpamDetectionData(): void
    {
        $this->spamKeywords = [
            // Commercial spam
            'buy now', 'limited time', 'act fast', 'don\'t miss out', 'exclusive offer',
            'make money fast', 'work from home', 'easy money', 'get rich quick',
            'free money', 'guaranteed income', 'no experience needed',
            
            // Pharmaceutical spam
            'viagra', 'cialis', 'pharmacy', 'prescription', 'no prescription needed',
            
            // Gambling spam
            'casino', 'poker', 'betting', 'lottery', 'jackpot', 'win big',
            
            // Generic spam indicators
            'click here', 'visit now', 'urgent', 'congratulations you won',
            'you have been selected', 'claim your prize', 'limited offer',
            
            // Crypto/investment spam
            'cryptocurrency', 'bitcoin investment', 'trading bot', 'guaranteed returns',
            'forex trading', 'binary options',
        ];

        $this->suspiciousPatterns = [
            // Excessive punctuation
            '/!{3,}/',
            '/\?{3,}/',
            '/\.{4,}/',
            
            // ALL CAPS sections
            '/\b[A-Z]{10,}\b/',
            
            // Excessive emojis (more than 10% of content)
            '/[\x{1F600}-\x{1F64F}\x{1F300}-\x{1F5FF}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}]{5,}/u',
            
            // Phone number patterns
            '/\b\d{3}[-.]?\d{3}[-.]?\d{4}\b/',
            '/\b1[-.]?800[-.]?\d{3}[-.]?\d{4}\b/',
            
            // Excessive spacing
            '/\s{5,}/',
            
            // Common spam phrases patterns
            '/\b(call|text)\s+(now|today)\s*!{0,3}\s*\b/i',
            '/\b(limited|special)\s+(time|offer)\b/i',
        ];
    }

    /**
     * Check if content contains spam keywords
     */
    protected function containsSpamKeywords(string $content): bool
    {
        $lowerContent = strtolower($content);
        $spamScore = 0;

        foreach ($this->spamKeywords as $keyword) {
            if (str_contains($lowerContent, strtolower($keyword))) {
                $spamScore++;
                
                // Immediate fail for high-risk keywords
                $highRiskKeywords = ['viagra', 'cialis', 'make money fast', 'get rich quick'];
                if (in_array($keyword, $highRiskKeywords)) {
                    return true;
                }
            }
        }

        // Fail if too many spam keywords are present
        return $spamScore >= 3;
    }

    /**
     * Check for suspicious patterns
     */
    protected function hasSuspiciousPatterns(string $content): bool
    {
        $patternMatches = 0;

        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $patternMatches++;
            }
        }

        return $patternMatches >= 2;
    }

    /**
     * Check for excessive repetition
     */
    protected function hasExcessiveRepetition(string $content): bool
    {
        // Check for repeated words
        $words = preg_split('/\s+/', strtolower($content));
        $wordCounts = array_count_values($words);
        
        foreach ($wordCounts as $word => $count) {
            // Skip common words
            if (in_array($word, ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by'])) {
                continue;
            }
            
            if (strlen($word) > 3 && $count > 5) {
                return true;
            }
        }

        // Check for repeated phrases
        $sentences = preg_split('/[.!?]+/', $content);
        if (count($sentences) > count(array_unique($sentences))) {
            return true;
        }

        return false;
    }

    /**
     * Check if content is link spam
     */
    protected function isLinkSpam(string $content): bool
    {
        // Count URLs
        $urlCount = preg_match_all('/https?:\/\/[^\s]+/', $content);
        $contentLength = strlen($content);

        // Too many URLs relative to content length
        if ($urlCount > 3 && $urlCount > ($contentLength / 100)) {
            return true;
        }

        // Check for suspicious domains
        $suspiciousDomains = ['.tk', '.ml', '.ga', '.cf', 'bit.ly', 'tinyurl.com'];
        foreach ($suspiciousDomains as $domain) {
            if (str_contains($content, $domain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if content appears to be bot-generated
     */
    protected function appearsBotGenerated(string $content): bool
    {
        // Check for unnatural patterns common in bot-generated content
        $botPatterns = [
            // Unnatural repetitive sentence structures
            '/^(I am|I will|I can|We are|We will|We can)\s+.+\.\s*\1/im',
            
            // Generic AI-like phrases
            '/\b(as an ai|i am an ai|according to my knowledge|based on my training)\b/i',
            
            // Overly formal or robotic language patterns
            '/\b(furthermore|moreover|additionally|consequently)\b.*\b(furthermore|moreover|additionally|consequently)\b/i',
        ];

        foreach ($botPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        // Check sentence length variance (bots often have consistent lengths)
        $sentences = preg_split('/[.!?]+/', $content);
        if (count($sentences) > 3) {
            $lengths = array_map('strlen', array_filter($sentences));
            $avgLength = array_sum($lengths) / count($lengths);
            $variance = array_sum(array_map(function($len) use ($avgLength) { 
                return pow($len - $avgLength, 2); 
            }, $lengths)) / count($lengths);
            
            // Very low variance might indicate bot content
            if ($variance < 50 && $avgLength > 50) {
                return true;
            }
        }

        return false;
    }

    /**
     * Use external spam detection service if available
     */
    protected function failsExternalSpamCheck(string $content): bool
    {
        $spamCheckApi = config('services.spam_check.api_url');
        $spamCheckKey = config('services.spam_check.api_key');

        if (!$spamCheckApi || !$spamCheckKey) {
            return false; // Skip if not configured
        }

        $cacheKey = 'spam_check_' . md5($content);
        
        return Cache::remember($cacheKey, 3600, function () use ($content, $spamCheckApi, $spamCheckKey) {
            try {
                $response = Http::timeout(5)
                    ->withHeaders(['Authorization' => 'Bearer ' . $spamCheckKey])
                    ->post($spamCheckApi, [
                        'content' => $content,
                        'check_types' => ['spam', 'profanity', 'toxicity']
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    return ($result['spam_score'] ?? 0) > 0.7;
                }
            } catch (\Exception $e) {
                // Log the error but don't fail validation due to API issues
                \Log::warning('Spam check API failed', [
                    'error' => $e->getMessage(),
                    'content_length' => strlen($content)
                ]);
            }

            return false;
        });
    }
}
