<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Class ValidHtmlContentRule
 *
 * A custom Laravel validation rule to validate HTML content.
 * It checks for allowed HTML tags, disallows event handler attributes,
 * prevents use of the "javascript:" protocol in href/src attributes,
 * ensures balanced <script> tags, and detects potentially unsafe JavaScript operations.
 *
 * @package App\Rules
 */
class ValidScriptContentRule implements ValidationRule
{
    /**
     * @var array List of allowed HTML tags.
     */
    protected array $allowedTags;

    /**
     * Create a new rule instance.
     *
     * @param  array  $allowedTags  List of allowed HTML tags.
     * @return void
     */
    public function __construct(array $allowedTags)
    {
        $this->allowedTags = $allowedTags;
    }

    /**
     * Validate the given attribute.
     *
     * @param  string   $attribute  The attribute name being validated.
     * @param  mixed    $value      The attribute value.
     * @param  \Closure $fail       The fail callback to invoke if validation fails.
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strpos($value, '<') === false) {
            $fail("The {$attribute} field must contain at least one HTML tag.");
            return;
        }

        // Strip comments (HTML, Blade, JS) before analysis
        $value = preg_replace('/<!--.*?-->/s', '', $value);           // HTML comments
        $value = preg_replace('/{{--.*?--}}/s', '', $value);          // Blade comments
        $value = preg_replace('/\/\/.*(?=[\n\r])/s', '', $value);     // JS single-line
        $value = preg_replace('/\/\*.*?\*\//s', '', $value);          // JS multi-line

        // Check HTML tags
        preg_match_all('/<\s*\/?\s*([a-z0-9]+)[^>]*>/i', $value, $matches);
        $foundTags = array_map('strtolower', $matches[1]);

        $invalidTags = array_diff($foundTags, $this->allowedTags);

        if (!empty($invalidTags)) {
            $fail("Invalid tags found in {$attribute}: " . implode(', ', array_unique($invalidTags)) .
                '. Allowed tags: ' . implode(', ', $this->allowedTags) . '.');
            return;
        }

        // Disallow event handlers
        if (preg_match('/\s+on\w+\s*=/i', $value)) {
            $fail("Event handler attributes (like onclick, onerror) are not allowed in {$attribute}.");
            return;
        }

        // Disallow javascript: in href/src
        if (preg_match('/(href|src)\s*=\s*["\']\s*javascript:/i', $value)) {
            $fail("The \"javascript:\" protocol is not allowed in {$attribute}.");
            return;
        }

        // Validate script tag balance
        $openTagsCount  = substr_count(strtolower($value), '<script');
        $closeTagsCount = substr_count(strtolower($value), '</script>');

        if ($openTagsCount !== $closeTagsCount) {
            $fail("The JavaScript code must contain valid <script> tags. Unbalanced <script> tags found in {$attribute}.");
            return;
        }

        // Check inside <script> for dangerous content
        if ($openTagsCount > 0) {
            preg_match_all('/<script\b[^>]*>(.*?)<\/script>/is', $value, $scriptMatches);
            $allScriptContent = implode(' ', $scriptMatches[1]);

            $dangerousPatterns = [
                '/document\.write\s*\(/i',
                '/eval\s*\(/i',
                '/innerHTML\s*=/i',
                '/outerHTML\s*=/i',
                '/localStorage\s*\./i',
                '/sessionStorage\s*\./i',
            ];

            foreach ($dangerousPatterns as $pattern) {
                if (preg_match($pattern, $allScriptContent)) {
                    $fail("Potentially unsafe JavaScript operation found in {$attribute}.");
                    return;
                }
            }
        }
    }
}
