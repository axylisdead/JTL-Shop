<?php declare(strict_types=1);
/**
 * Class QRDataAbstract
 *
 * @filesource   QRDataAbstract.php
 * @created      25.11.2015
 * @package      chillerlan\QRCode\Data
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace qrcodegenerator\QRCode\Data;

/**
 * Class QRDataAbstract
 */
abstract class QRDataAbstract implements QRDataInterface
{
    /**
     * @var string
     */
    public $data;

    /**
     * @var int
     */
    public $dataLength;

    /**
     * @var array
     */
    protected $lengthBits = [0, 0, 0];

    /**
     * QRDataAbstract constructor.
     *
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data       = $data;
        $this->dataLength = \strlen($data);
    }

    /**
     * @param int $type
     *
     * @return int
     * @throws QRCodeDataException
     * @see QRCode::createData()
     * @codeCoverageIgnore
     */
    public function getLengthInBits($type)
    {
        return match (true) {
            $type >= 1 && $type <= 9 => $this->lengthBits[0],
            $type <= 26 => $this->lengthBits[1],
            $type <= 40 => $this->lengthBits[2],
            default => throw new QRCodeDataException('$type: ' . $type),
        };
    }
}
