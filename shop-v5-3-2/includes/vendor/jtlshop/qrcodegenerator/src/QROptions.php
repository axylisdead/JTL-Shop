<?php declare(strict_types=1);
/**
 * Class QROptions
 *
 * @filesource   QROptions.php
 * @created      08.12.2015
 * @package      chillerlan\QRCode
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace qrcodegenerator\QRCode;

/**
 * Class QROptions
 */
class QROptions
{
    /**
     * @var int
     */
    public int $errorCorrectLevel = QRCode::ERROR_CORRECT_LEVEL_M;

    /**
     * @var int|null
     */
    public $typeNumber = null;
}
