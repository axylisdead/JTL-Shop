<?php declare(strict_types=1);
/**
 * Class QRStringOptions
 *
 * @filesource   QRStringOptions.php
 * @created      08.12.2015
 * @package      chillerlan\QRCode\Output
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace qrcodegenerator\QRCode\Output;

use qrcodegenerator\QRCode\QRCode;

/**
 * Class QRStringOptions
 */
class QRStringOptions
{
    public int $type = QRCode::OUTPUT_STRING_HTML;

    public string $textDark = '#';

    public string $textLight = ' ';

    public string $eol = \PHP_EOL;

    public string $htmlRowTag = 'p';

    public bool $htmlOmitEndTag = true;
}
