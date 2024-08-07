<?php declare(strict_types=1);
/**
 * Class Byte
 *
 * @filesource   Byte.php
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
 * Class Byte
 */
class Byte extends QRDataAbstract
{
    /**
     * @var int
     */
    public $mode = QRConst::MODE_BYTE;

    /**
     * @var array
     */
    protected $lengthBits = [8, 16, 16];

    /**
     * @inheritdoc
     */
    public function write(BitBuffer $buffer)
    {
        $i = 0;
        while ($i < $this->dataLength) {
            $buffer->put(\ord($this->data[$i]), 8);
            $i++;
        }
    }
}
