<?php declare(strict_types=1);
/**
 * Interface QROutputInterface,
 *
 * @filesource   QROutputInterface.php
 * @created      02.12.2015
 * @package      chillerlan\QRCode\Output
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace qrcodegenerator\QRCode\Output;

/**
 * Interface QROutputInterface
 */
interface QROutputInterface
{
    /**
     * @return mixed
     */
    public function dump();

    /**
     * @param array $matrix
     * @return $this
     * @throws QRCodeOutputException
     */
    public function setMatrix(array $matrix);
}
