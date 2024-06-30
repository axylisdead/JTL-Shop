<?php declare(strict_types=1);
/**
 * Class QROutputAbstract
 *
 * @filesource   QROutputAbstract.php
 * @created      09.12.2015
 * @package      chillerlan\QRCode\Output
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace qrcodegenerator\QRCode\Output;

/**
 * Class QROutputAbstract
 */
abstract class QROutputAbstract implements QROutputInterface
{
    /**
     * @var array
     */
    protected array $matrix;

    /**
     * @var int
     */
    protected int $pixelCount;

    /**
     * @var object
     */
    protected $options;

    /**
     * @param array $matrix
     * @return $this
     * @throws QRCodeOutputException
     */
    public function setMatrix(array $matrix)
    {
        $this->pixelCount = \count($matrix);

        // specify valid range?
        if ($this->pixelCount < 2
            || !isset($matrix[$this->pixelCount - 1])
            || $this->pixelCount !== \count($matrix[$this->pixelCount - 1])
        ) {
            throw new QRCodeOutputException('Invalid matrix!');
        }

        $this->matrix = $matrix;

        return $this;
    }
}
