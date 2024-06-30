<?php declare(strict_types=1);
/**
 * Class AlphaNum
 *
 * @filesource   AlphaNum.php
 * @created      25.11.2015
 * @package      chillerlan\QRCode\Data
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace qrcodegenerator\QRCode\Data;

use qrcodegenerator\QRCode\BitBuffer;
use qrcodegenerator\QRCode\QRConst;

/**
 * Class AlphaNum
 */
class AlphaNum extends QRDataAbstract
{
    /**
     * @var array
     */
    public static array $CHAR_MAP = [
        36 => ' ',
        37 => '$',
        38 => '%',
        39 => '*',
        40 => '+',
        41 => '-',
        42 => '.',
        43 => '/',
        44 => ':',
    ];

    /**
     * @var int
     */
    public $mode = QRConst::MODE_ALPHANUM;

    /**
     * @var array
     */
    protected $lengthBits = [9, 11, 13];

    /**
     * @param BitBuffer $buffer
     */
    public function write(BitBuffer $buffer)
    {
        $i = 0;
        while ($i + 1 < $this->dataLength) {
            $buffer->put(self::getCharCode($this->data[$i]) * 45 + self::getCharCode($this->data[$i + 1]), 11);
            $i += 2;
        }
        if ($i < $this->dataLength) {
            $buffer->put(self::getCharCode($this->data[$i]), 6);
        }
    }

    /**
     * @param string $string
     * @return int
     * @throws QRCodeDataException
     */
    private static function getCharCode($string)
    {
        $c = \ord($string);

        switch (true) {
            case \ord('0') <= $c && $c <= \ord('9'):
                return $c - \ord('0');
            case \ord('A') <= $c && $c <= \ord('Z'):
                return $c - \ord('A') + 10;
            default:
                foreach (self::$CHAR_MAP as $i => $char) {
                    if (\ord($char) === $c) {
                        return $i;
                    }
                }
        }

        throw new QRCodeDataException('illegal char: ' . $c);
    }
}
