<?php declare(strict_types=1);
/**
 * Polyfills for deprecated PHP 4-era APIs so the legacy codebase can run on
 * PHP 8.3 without fatal errors. These helpers mimic the behaviour of removed
 * functions while delegating to modern extensions under the hood.
 */

declare(strict_types=1);

if (!function_exists('legacy_regex_pattern')) {
    function legacy_regex_pattern(string $pattern, bool $caseInsensitive = false): string
    {
        $delimiter = '~';
        $escaped = str_replace($delimiter, '\\'.$delimiter, $pattern);

        return $delimiter.$escaped.$delimiter.($caseInsensitive ? 'i' : '');
    }
}

if (!function_exists('each')) {
    /**
     * Replacement for the removed each() function using the internal array pointer.
     *
     * @param array<int|string, mixed> $array
     *
     * @return array<int|string, mixed>|false
     */
    function each(array &$array)
    {
        $key = key($array);
        if ($key === null) {
            return false;
        }

        $value = current($array);
        next($array);

        return [
            1 => $value,
            'value' => $value,
            0 => $key,
            'key' => $key,
        ];
    }
}

if (!function_exists('eregi')) {
    function eregi(string $pattern, string $string, ?array &$regs = null)
    {
        $result = preg_match(legacy_regex_pattern($pattern, true), $string, $matches);
        if ($regs !== null) {
            $regs = $matches;
        }

        return $result === 1;
    }
}

if (!function_exists('ereg')) {
    function ereg(string $pattern, string $string, ?array &$regs = null)
    {
        $result = preg_match(legacy_regex_pattern($pattern, false), $string, $matches);
        if ($regs !== null) {
            $regs = $matches;
        }

        return $result === 1;
    }
}

if (!function_exists('eregi_replace')) {
    function eregi_replace(string $pattern, string $replacement, string $string)
    {
        return preg_replace(legacy_regex_pattern($pattern, true), $replacement, $string);
    }
}

if (!function_exists('ereg_replace')) {
    function ereg_replace(string $pattern, string $replacement, string $string)
    {
        return preg_replace(legacy_regex_pattern($pattern, false), $replacement, $string);
    }
}
