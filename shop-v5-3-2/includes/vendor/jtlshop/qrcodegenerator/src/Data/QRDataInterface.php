<?php declare(strict_types=1);
/**
 * Interface QRDataInterface
 *
 * @filesource   QRDataInterface.php
 * @created      01.12.2015
 * @package      chillerlan\QRCode\Data
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace qrcodegenerator\QRCode\Data;

use qrcodegenerator\QRCode\BitBuffer;

/**
 * Interface QRDataInterface
 *
 * @property string data
 * @property int    dataLength
 * @property int    mode
 */
interface QRDataInterface
{
    /**
     * @param BitBuffer $buffer
     * @return void
     */
    public function write(BitBuffer $buffer);

    /**
     * @param $type
     * @return int
     * @throws QRCodeDataException
     */
    public function getLengthInBits($type);
}
