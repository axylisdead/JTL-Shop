<?php declare(strict_types=1);
/**
 * Class Kanji
 *
 * @filesource   Kanji.php
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
 * Class Kanji
 */
class Kanji extends QRDataAbstract
{
    /**
     * @var int
     */
    public $mode = QRConst::MODE_KANJI;

    /**
     * @var array
     */
    protected array $lengthBits = [8, 10, 12];

    /**
     * @param BitBuffer $buffer
     * @throws QRCodeDataException
     */
    public function write(BitBuffer $buffer)
    {
        $i = 0;
        while ($i + 1 < $this->dataLength) {
            $c = ((0xff & \ord($this->data[$i])) << 8) | (0xff & \ord($this->data[$i + 1]));

            if (0x8140 <= $c && $c <= 0x9FFC) {
                $c -= 0x8140;
            } elseif (0xE040 <= $c && $c <= 0xEBBF) {
                $c -= 0xC140;
            } else {
                throw new QRCodeDataException('illegal char at ' . ($i + 1) . ' (' . $c . ')');
            }

            $buffer->put((($c >> 8) & 0xff) * 0xC0 + ($c & 0xff), 13);
            $i += 2;
        }

        if ($i < $this->dataLength) {
            throw new QRCodeDataException('illegal char at ' . ($i + 1));
        }
    }
}
