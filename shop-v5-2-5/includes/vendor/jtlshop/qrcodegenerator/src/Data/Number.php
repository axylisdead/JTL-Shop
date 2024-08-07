<?php declare(strict_types=1);
/**
 * Class Number
 *
 * @filesource   Number.php
 * @created      26.11.2015
 * @package      QRCode
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace qrcodegenerator\QRCode\Data;

use qrcodegenerator\QRCode\BitBuffer;
use qrcodegenerator\QRCode\QRConst;

/**
 * Class Number
 */
class Number extends QRDataAbstract
{
    /**
     * @var int
     */
    public $mode = QRConst::MODE_NUMBER;

    /**
     * @var array
     */
    protected $lengthBits = [10, 12, 14];

    /**
     * @param BitBuffer $buffer
     */
    public function write(BitBuffer $buffer)
    {
        $i = 0;
        while ($i + 2 < $this->dataLength) {
            $buffer->put(self::parseInt(\substr($this->data, $i, 3)), 10);
            $i += 3;
        }
        if ($i < $this->dataLength) {
            if ($this->dataLength - $i === 1) {
                $buffer->put(self::parseInt(\substr($this->data, $i, $i + 1)), 4);
            } elseif ($this->dataLength - $i === 2) {
                $buffer->put(self::parseInt(\substr($this->data, $i, $i + 2)), 7);
            }
        }
    }

    /**
     * @param string $string
     * @return int
     * @throws QRCodeDataException
     */
    private static function parseInt($string)
    {
        $num = 0;
        $len = \strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $c    = \ord($string[$i]);
            $ord0 = \ord('0');
            if ($ord0 <= $c && $c <= \ord('9')) {
                $c -= $ord0;
            } else {
                throw new QRCodeDataException('illegal char: ' . $c);
            }

            $num = $num * 10 + $c;
        }

        return $num;
    }
}
