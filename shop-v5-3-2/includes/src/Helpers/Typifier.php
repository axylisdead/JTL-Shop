<?php declare(strict_types=1);

namespace JTL\Helpers;

use JTL\Shop;

/**
 * Class Typifier
 * @since 5.3.0
 * @package JTL\Helpers
 */
class Typifier
{
    /**
     * @var array
     */
    private const POSSIBLEBOOLVALUES = [
        'true'  => true,
        'y'     => true,
        'yes'   => true,
        'ja'    => true,
        '1'     => true,
        'false' => false,
        'n'     => false,
        'no'    => false,
        'nein'  => false,
        '0'     => false,
    ];

    private const TYPEMAPPING = [
        'string'  => 'stringify',
        'integer' => 'intify',
        'double'  => 'floatify',
        'boolean' => 'boolify',
        'array'   => 'arrify',
        'object'  => 'objectify',
    ];

    /**
     * @param string $msg
     */
    private static function logError(string $msg): void
    {
        try {
            Shop::Container()->getLogService()->error(__CLASS__ . $msg);
        } catch (\Exception) {
            //do nothing
        }
        if (\APPLICATION_BUILD_SHA === '#DEV#') {
            throw new \InvalidArgumentException($msg, 501);
        }
    }

    /**
     * @param mixed  $value
     * @param string $to
     * @return mixed
     */
    public static function typeify(mixed $value, string $to): mixed
    {
        $method = self::TYPEMAPPING[$to] ?? '';
        if ($method === '') {
            return $value;
        }

        return self::$method($value);
    }

    /**
     * @param mixed       $value
     * @param string|null $default
     * @return string|null
     */
    public static function stringify(mixed $value, ?string $default = ''): ?string
    {
        if ($default !== null && (\is_array($value) === true || \is_object($value) === true)) {
            self::logError('Value could not be typified into a string. ' . \serialize($value ?? 'null'));
        }

        return $value === null ? $default : (string)$value;
    }

    /**
     * @param mixed $value
     * @param int   $default
     * @return int
     */
    public static function intify(mixed $value, int $default = 0): int
    {
        if (\is_numeric($value)) {
            return (int)$value;
        }

        return $default;
    }

    /**
     * @param mixed $value
     * @return int|null
     */
    public static function intifyOrNull(mixed $value): ?int
    {
        return \is_numeric($value) ? (int)$value : null;
    }

    /**
     * @param mixed $value
     * @param float $default
     * @return float
     */
    public static function floatify(mixed $value, float $default = 0.00): float
    {
        if (\is_numeric($value)) {
            return (float)$value;
        }

        return $default;
    }

    /**
     * @param mixed     $value
     * @param bool|null $default
     * @return bool|null
     */
    public static function boolify(mixed $value, ?bool $default = false): ?bool
    {
        if (array_key_exists(\strtolower((string)$value), self::POSSIBLEBOOLVALUES)) {
            return (\is_bool($value) ? $value : self::POSSIBLEBOOLVALUES[\strtolower((string)$value)]);
        }

        return $default ?? false;
    }

    /**
     * @param mixed $value
     * @param array $default
     * @return array
     */
    public static function arrify(mixed $value, array $default = []): array
    {
        if (null !== $value) {
            return (array)$value;
        }

        return $default;
    }

    /**
     * @param mixed  $value
     * @param object $default
     * @return object
     */
    public static function objectify(mixed $value, object $default = new \stdClass()): object
    {
        if (\is_object($value) || \is_array($value)) {
            return (object)$value;
        }

        return $default;
    }
}
