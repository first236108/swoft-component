<?php declare(strict_types=1);

namespace Swoft\Stdlib\Helper;

use Swoft\Stdlib\Concern\RandomStringTrait;
use function explode;
use function is_string;
use function lcfirst;
use function mb_convert_case;
use function mb_strlen;
use function mb_strtoupper;
use function preg_match;
use function preg_replace;
use function str_pad;
use function str_replace;
use function strlen;
use function strpos;
use function strrpos;
use function substr;
use function substr_replace;
use function trim;
use const STR_PAD_LEFT;
use const STR_PAD_RIGHT;

/**
 * String helper
 *
 * @since 2.0
 */
class StringHelper
{
    use RandomStringTrait;

    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static $studlyCache = [];

    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param string $value
     *
     * @return string
     */
    public static function ascii($value): string
    {
        foreach (StringChars::getChars() as $key => $val) {
            $value = str_replace($val, $key, $value);
        }

        return preg_replace('/[^\x20-\x7E]/u', '', $value);
    }

    /**
     * Convert a value to camel case.
     *
     * @param string $value
     * @param bool   $lcfirst
     *
     * @return string
     */
    public static function camel($value, bool $lcfirst = true): string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = ($lcfirst ? lcfirst(static::studly($value)) : static::studly($value));
    }

    /**
     * @param string $string
     * @param string $delimiter
     * @param int    $limit
     *
     * @return array
     */
    public static function toArray(string $string, string $delimiter = ',', int $limit = 0): array
    {
        $string = trim($string, "$delimiter ");
        if ($string === '') {
            return [];
        }

        $values  = [];
        $rawList = $limit < 1 ? explode($delimiter, $string) : explode($delimiter, $string, $limit);

        foreach ($rawList as $val) {
            if (($val = trim($val)) !== '') {
                $values[] = $val;
            }
        }

        return $values;
    }

    /**
     * @param string $str
     * @param string $separator
     * @param int    $limit
     *
     * @return array
     */
    public static function explode(string $str, string $separator = ',', int $limit = 0): array
    {
        return static::toArray($str, $separator, $limit);
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    public static function contains(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    public static function endsWith(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $str
     *
     * @return string
     */
    public static function firstLine(string $str): string
    {
        if (!$str = trim($str)) {
            return '';
        }

        if (strpos($str, "\n") > 0) {
            return explode("\n", $str)[0];
        }

        return $str;
    }

    /**
     * Cap a string with a single instance of a given value.
     *
     * @param string $value
     * @param string $cap
     *
     * @return string
     */
    public static function finish($value, $cap): string
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:' . $quoted . ')+$/', '', $value) . $cap;
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string $pattern
     * @param string $value
     *
     * @return bool
     */
    public static function is(string $pattern, string $value): bool
    {
        if ($pattern === $value) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);

        return (bool)preg_match('#^' . $pattern . '\z#', $value);
    }

    /**
     * Return the length of the given string.
     *
     * @param string|int  $value
     * @param string|null $encode
     *
     * @return int
     */
    public static function length($value, string $encode = null): int
    {
        return mb_strlen((string)$value, $encode);
    }

    /**
     * Return the length of the given string.
     *
     * @param string|int $value
     * @param string|null $encode
     *
     * @return int
     */
    public static function len($value, string $encode = null): int
    {
        return mb_strlen((string)$value, $encode);
    }

    /**
     * @param string|int $string
     * @param int|float $padLen
     * @param string $padStr
     * @param int    $padType
     *
     * @return string
     */
    public static function pad($string, $padLen, string $padStr = ' ', int $padType = STR_PAD_RIGHT): string
    {
        $string = (string)$string;

        return $padLen > 0 ? str_pad($string, (int)$padLen, $padStr, $padType) : $string;
    }

    /**
     * @param string|int $string
     * @param int    $padLen
     * @param string $padStr
     *
     * @return string
     */
    public static function padLeft($string, int $padLen, string $padStr = ' '): string
    {
        return $padLen > 0 ? str_pad($string, $padLen, $padStr, STR_PAD_LEFT) : $string;
    }

    /**
     * @param string|int $string
     * @param int    $padLen
     * @param string $padStr
     *
     * @return string
     */
    public static function padRight($string, int $padLen, string $padStr = ' '): string
    {
        return $padLen > 0 ? str_pad($string, $padLen, $padStr) : $string;
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int    $limit
     * @param string $end
     *
     * @return string
     */
    public static function limit($value, $limit = 100, $end = '...'): string
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Limit the number of words in a string.
     *
     * @param string $value
     * @param int    $words
     * @param string $end
     *
     * @return string
     */
    public static function words($value, $words = 100, $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);

        if (!isset($matches[0]) || strlen($value) === strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * Parse a `Class@method` style callback into class and method.
     *
     * @param string $callback
     * @param string $default
     *
     * @return array
     */
    public static function parseCallback(string $callback, string $default): array
    {
        return static::contains($callback, '@') ? explode('@', $callback, 2) : [$callback, $default];
    }

    /**
     * Compares two strings using a constant-time algorithm.
     * Note: This method will leak length information.
     * Note: Adapted from Symfony\Component\Security\Core\Util\StringUtils.
     *
     * @param string $knownString
     * @param string $userInput
     *
     * @return bool
     * @deprecated since version 5.2. Use hash_equals instead.
     */
    public static function equals(string $knownString, string $userInput): bool
    {
        return hash_equals($knownString, $userInput);
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string
     */
    public static function replaceFirst($search, $replace, $subject): string
    {
        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string
     */
    public static function replaceLast(string $search, $replace, string $subject): string
    {
        $position = strrpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convert the given string to title case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function title($value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $title
     * @param string $separator
     *
     * @return string
     */
    public static function slug(string $title, string $separator = '-'): string
    {
        $title = static::ascii($title);

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     *
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value . $delimiter;

        if (isset(static::$snakeCache[$key])) {
            return static::$snakeCache[$key];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/', '', $value);

            $value = strtolower(preg_replace('/(.)(?=[A-Z])/', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key] = $value;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    public static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function studly($value): string
    {
        $key = $value;

        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * Returns the portion of string specified by the start and length parameters.
     *
     * @param string   $string
     * @param int      $start
     * @param int|null $length
     *
     * @return string
     */
    public static function substr(string $string, $start, $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * Make a string's first character uppercase.
     *
     * @param string $string
     *
     * @return string
     */
    public static function ucfirst(string $string): string
    {
        return static::upper(static::substr($string, 0, 1)) . static::substr($string, 1);
    }

    public static function trim($str, $prefix = '', $suffix = ''): void
    {
    }

    public static function strSplit($str, $splitLength = 1)
    {
        $splitLength = (int)$splitLength;

        if (self::isAscii($str)) {
            return str_split($str, $splitLength);
        }

        if ($splitLength < 1) {
            return false;
        }

        if (mb_strlen($str) <= $splitLength) {
            return [$str];
        }
        preg_match_all('/.{' . $splitLength . '}|[^\x00]{1,' . $splitLength . '}$/us', $str, $matches);
        return $matches[0];
    }

    /**
     * @param string $str
     *
     * @return bool
     */
    public static function isAscii($str): bool
    {
        return is_string($str) && !preg_match('/[^\x00-\x7F]/S', $str);
    }

    /**
     * Get class name without suffix. eg: HomeController -> home
     *
     * @param string $class  full class name, with namespace.
     * @param string $suffix class suffix
     *
     * @return string
     */
    public static function getClassName(string $class, string $suffix): string
    {
        if (empty($suffix)) {
            return $class;
        }

        // \\(\w+)Helper$
        if (strpos($class, $suffix) > 0) {
            $regex = '/\\\(\w+)' . $suffix . '$/';
            $ok    = preg_match($regex, $class, $match);
        } else {
            $ok    = true;
            $match = [1 => $class];
        }

        return $ok ? lcfirst($match[1]) : '';
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function formatPath(string $path): string
    {
        $path = '/' . trim($path, '/ ');

        return $path ?: '/';
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function rmPharPrefix(string $path): string
    {
        if (0 === strpos($path, 'phar://')) {
            return preg_replace('/[\w-]+\.phar\//', '', substr($path, 7));
        }

        return $path;
    }

    /**
     * @param array|string $queryData
     *
     * @return string
     */
    public static function buildQuery($queryData): string
    {
        // is string
        if (is_string($queryData)) {
            return $queryData;
        }

        // array: k-v map
        return preg_replace('/%5B(?:\d|[1-9]\d+)%5D=/', '=', http_build_query($queryData));
    }
}
