<?php declare(strict_types=1);
/**
 * Class QRCode
 *
 * @filesource   QRCode.php
 * @created      26.11.2015
 * @package      chillerlan\QRCode
 * @author       Smiley <smiley@chillerlan.net>
 * @copyright    2015 Smiley
 * @license      MIT
 */

namespace qrcodegenerator\QRCode;

use qrcodegenerator\QRCode\Data\QRDataInterface;
use qrcodegenerator\QRCode\Output\QROutputInterface;

/**
 * Class QRCode
 * @link https://github.com/kazuhikoarase/qrcode-generator/tree/master/php
 * @link http://www.thonky.com/qr-code-tutorial/
 */
class QRCode
{
    /**
     * API constants
     */
    public const OUTPUT_STRING_TEXT = 0;
    public const OUTPUT_STRING_JSON = 1;
    public const OUTPUT_STRING_HTML = 2;

    public const OUTPUT_IMAGE_PNG = 'png';
    public const OUTPUT_IMAGE_JPG = 'jpg';
    public const OUTPUT_IMAGE_GIF = 'gif';

    public const ERROR_CORRECT_LEVEL_L = 1; // 7%.
    public const ERROR_CORRECT_LEVEL_M = 0; // 15%.
    public const ERROR_CORRECT_LEVEL_Q = 3; // 25%.
    public const ERROR_CORRECT_LEVEL_H = 2; // 30%.

    // max bits @ ec level L:07 M:15 Q:25 H:30 %
    public const TYPE_01 = 1; //  152  128  104   72
    public const TYPE_02 = 2; //  272  224  176  128
    public const TYPE_03 = 3; //  440  352  272  208
    public const TYPE_04 = 4; //  640  512  384  288
    public const TYPE_05 = 5; //  864  688  496  368
    public const TYPE_06 = 6; // 1088  864  608  480
    public const TYPE_07 = 7; // 1248  992  704  528
    public const TYPE_08 = 8; // 1552 1232  880  688
    public const TYPE_09 = 9; // 1856 1456 1056  800
    public const TYPE_10 = 10; // 2192 1728 1232  976

    /**
     * @var array
     */
    protected $matrix = [];

    /**
     * @var int
     */
    protected $pixelCount = 0;

    /**
     * @var int
     */
    protected $typeNumber;

    /**
     * @var int
     */
    protected $errorCorrectLevel;

    /**
     * @var int
     */
    protected $lostPoint;

    /**
     * @var int
     */
    protected $darkCount;

    /**
     * @var float
     */
    protected $minLostPoint;

    /**
     * @var int
     */
    protected $maskPattern;

    /**
     * @var BitBuffer
     */
    protected $bitBuffer;

    /**
     * @var QRDataInterface
     */
    protected $qrDataInterface;

    /**
     * @var QROutputInterface
     */
    protected $qrOutputInterface;

    /**
     * QRCode constructor.
     *
     * @param string            $data
     * @param QROutputInterface $output
     * @param QROptions|null    $options
     */
    public function __construct($data, QROutputInterface $output, QROptions $options = null)
    {
        $this->qrOutputInterface = $output;
        $this->bitBuffer         = new BitBuffer();
        $this->setData($data, $options);
    }

    /**
     * @param string         $data
     * @param QROptions|null $options
     * @return $this
     * @throws QRCodeException
     */
    public function setData($data, QROptions $options = null)
    {
        $data = \trim($data);

        if (empty($data)) {
            throw new QRCodeException('No data given.');
        }

        if (!$options instanceof QROptions) {
            $options = new QROptions();
        }

        if (!\in_array($options->errorCorrectLevel, QRConst::$RSBLOCK, true)) {
            throw new QRCodeException('Invalid error correct level: ' . $options->errorCorrectLevel);
        }

        $this->errorCorrectLevel = $options->errorCorrectLevel;

        $mode = match (true) {
            Util::isAlphaNum($data) => Util::isNumber($data) ? QRConst::MODE_NUMBER : QRConst::MODE_ALPHANUM,
            Util::isKanji($data) => QRConst::MODE_KANJI,
            default => QRConst::MODE_BYTE,
        };

        $vTemp           = [
            QRConst::MODE_ALPHANUM => "\qrcodegenerator\QRCode\Data\AlphaNum",
            QRConst::MODE_BYTE     => "\qrcodegenerator\QRCode\Data\Byte",
            QRConst::MODE_KANJI    => "\qrcodegenerator\QRCode\Data\Kanji",
            QRConst::MODE_NUMBER   => "\qrcodegenerator\QRCode\Data\Number",
        ];
        $qrDataInterface = $vTemp[$mode];

        $this->qrDataInterface = new $qrDataInterface($data);
        $this->typeNumber      = (int)($options->typeNumber);

        if ($this->typeNumber < 1 || $this->typeNumber > 10) {
            $this->typeNumber = $this->getTypeNumber($mode);
        }

        return $this;
    }

    /**
     * @param $mode
     * @return int
     * @throws QRCodeException
     */
    protected function getTypeNumber($mode)
    {
        $length = $this->qrDataInterface->dataLength;

        if ($this->qrDataInterface->mode === QRConst::MODE_KANJI) {
            $length = \floor($length / 2);
        }

        foreach (\range(1, 10) as $type) {
            if ($length <= Util::getMaxLength($type, $mode, $this->errorCorrectLevel)) {
                return $type;
            }
        }

        throw new QRCodeException('Unable to determine type number.'); // @codeCoverageIgnore
    }

    /**
     * @return mixed
     */
    public function output()
    {
        $this->qrOutputInterface->setMatrix($this->getRawData());

        return $this->qrOutputInterface->dump();
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        $this->minLostPoint = 0;
        $this->maskPattern  = 0;

        foreach (\range(0, 7) as $pattern) {
            $this->testPattern($pattern);
        }

        $this->getMatrix(false, $this->maskPattern);

        return $this->matrix;
    }

    /**
     * @param array $range
     */
    protected function testLevel1(array $range): void
    {
        foreach ($range as $row) {
            foreach ($range as $col) {
                $sameCount = 0;

                foreach ([-1, 0, 1] as $rr) {
                    if ($row + $rr < 0 || $this->pixelCount <= $row + $rr) {
                        continue;
                    }

                    foreach ([-1, 0, 1] as $cr) {

                        if (($rr === 0 && $cr === 0) || ($col + $cr < 0 || $this->pixelCount <= $col + $cr)) {
                            continue;
                        }

                        if ($this->matrix[$row + $rr][$col + $cr] === $this->matrix[$row][$col]) {
                            $sameCount++;
                        }
                    }
                }

                if ($sameCount > 5) {
                    $this->lostPoint += (3 + $sameCount - 5);
                }

            }
        }
    }

    /**
     * @param array $range
     */
    protected function testLevel2(array $range): void
    {
        foreach ($range as $row) {
            foreach ($range as $col) {
                $count = 0;

                if (
                    $this->matrix[$row][$col]
                    || $this->matrix[$row][$col + 1]
                    || $this->matrix[$row + 1][$col]
                    || $this->matrix[$row + 1][$col + 1]
                ) {
                    $count++;
                }
                if ($count === 0 || $count === 4) {
                    $this->lostPoint += 3;
                }
            }
        }
    }

    /**
     * @param array $range1
     * @param array $range2
     */
    protected function testLevel3(array $range1, array $range2): void
    {
        foreach ($range1 as $row) {
            foreach ($range2 as $col) {
                if (
                    $this->matrix[$row][$col]
                    && !$this->matrix[$row][$col + 1]
                    && $this->matrix[$row][$col + 2]
                    && $this->matrix[$row][$col + 3]
                    && $this->matrix[$row][$col + 4]
                    && !$this->matrix[$row][$col + 5]
                    && $this->matrix[$row][$col + 6]
                ) {
                    $this->lostPoint += 40;
                }

            }
        }

        foreach ($range1 as $col) {
            foreach ($range2 as $row) {
                if (
                    $this->matrix[$row][$col]
                    && !$this->matrix[$row + 1][$col]
                    && $this->matrix[$row + 2][$col]
                    && $this->matrix[$row + 3][$col]
                    && $this->matrix[$row + 4][$col]
                    && !$this->matrix[$row + 5][$col]
                    && $this->matrix[$row + 6][$col]
                ) {
                    $this->lostPoint += 40;
                }

            }
        }
    }

    /**
     * @param array $range
     */
    protected function testLevel4(array $range): void
    {
        foreach ($range as $col) {
            foreach ($range as $row) {
                if ($this->matrix[$row][$col]) {
                    $this->darkCount++;
                }
            }
        }
    }

    /**
     * @param int $pattern
     */
    protected function testPattern($pattern): void
    {
        $this->getMatrix(true, $pattern);
        $this->lostPoint = 0;
        $this->darkCount = 0;

        $range = \range(0, $this->pixelCount - 1);

        $this->testLevel1($range);
        $this->testLevel2(\range(0, $this->pixelCount - 2));
        $this->testLevel3($range, \range(0, $this->pixelCount - 7));
        $this->testLevel4($range);

        $this->lostPoint += (\abs(100 * $this->darkCount / $this->pixelCount / $this->pixelCount - 50) / 5) * 10;
        if ($pattern === 0 || $this->minLostPoint > $this->lostPoint) {
            $this->minLostPoint = $this->lostPoint;
            $this->maskPattern  = $pattern;
        }
    }

    /**
     * @param bool $test
     */
    protected function setTypeNumber($test): void
    {
        $bits = Util::getBCHTypeNumber($this->typeNumber);
        $i    = 0;
        while ($i < 18) {
            $a = (int)\floor($i / 3);
            $b = $i % 3 + $this->pixelCount - 8 - 3;

            $this->matrix[$b][$a] = !$test && (($bits >> $i) & 1) === 1;
            $this->matrix[$a][$b] = $this->matrix[$b][$a];
            $i++;
        }
    }

    /**
     * @param bool $test
     * @param int  $pattern
     */
    protected function setTypeInfo($test, $pattern): void
    {
        $this->setPattern();
        $bits = Util::getBCHTypeInfo(($this->errorCorrectLevel << 3) | $pattern);
        $i    = 0;
        while ($i < 15) {
            $mod = !$test && (($bits >> $i) & 1) === 1;

            switch (true) {
                case $i < 6:
                    $this->matrix[$i][8] = $mod;
                    break;
                case $i < 8:
                    $this->matrix[$i + 1][8] = $mod;
                    break;
                default:
                    $this->matrix[$this->pixelCount - 15 + $i][8] = $mod;
            }

            switch (true) {
                case $i < 8:
                    $this->matrix[8][$this->pixelCount - $i - 1] = $mod;
                    break;
                case $i < 9:
                    $this->matrix[8][15 + 1 - $i - 1] = $mod;
                    break;
                default:
                    $this->matrix[8][15 - $i - 1] = $mod;
            }
            $i++;
        }

        $this->matrix[$this->pixelCount - 8][8] = !$test;
    }

    /**
     * @throws QRCodeException
     */
    protected function createData(): void
    {
        $this->bitBuffer->clear();
        $this->bitBuffer->put($this->qrDataInterface->mode, 4);
        $this->bitBuffer->put(
            $this->qrDataInterface->mode === QRConst::MODE_KANJI
                ? \floor($this->qrDataInterface->dataLength / 2)
                : $this->qrDataInterface->dataLength,
            $this->qrDataInterface->getLengthInBits($this->typeNumber)
        );

        $this->qrDataInterface->write($this->bitBuffer);

        $MAX_BITS = QRConst::$MAX_BITS[$this->typeNumber][$this->errorCorrectLevel];

        if ($this->bitBuffer->length > $MAX_BITS) {
            throw new QRCodeException('code length overflow (' . $this->bitBuffer->length . ' > ' . $MAX_BITS . 'bit)');
        }
        // end code.
        if ($this->bitBuffer->length + 4 <= $MAX_BITS) {
            $this->bitBuffer->put(0, 4);
        }
        // padding
        while ($this->bitBuffer->length % 8 !== 0) {
            $this->bitBuffer->putBit(false);
        }
        // padding
        while (true) {
            if ($this->bitBuffer->length >= $MAX_BITS) {
                break;
            }

            $this->bitBuffer->put(QRConst::PAD0, 8);

            if ($this->bitBuffer->length >= $MAX_BITS) {
                break;
            }

            $this->bitBuffer->put(QRConst::PAD1, 8);
        }
    }

    /**
     * @return array
     * @throws QRCodeException
     */
    protected function createBytes()
    {
        $index          = 0;
        $offset         = 0;
        $maxEcCount     = 0;
        $maxDcCount     = 0;
        $totalCodeCount = 0;
        $rsBlocks       = Util::getRSBlocks($this->typeNumber, $this->errorCorrectLevel);
        $rsBlockCount   = count($rsBlocks);
        $ecdata         = \array_fill(0, $rsBlockCount, null);
        $dcdata         = $ecdata;
        foreach ($rsBlocks as $key => $value) {
            $rsBlockTotal     = $value[0];
            $rsBlockDataCount = $value[1];

            $maxDcCount = \max($maxDcCount, $rsBlockDataCount);
            $maxEcCount = \max($maxEcCount, $rsBlockTotal - $rsBlockDataCount);

            $dcdata[$key] = \array_fill(0, $rsBlockDataCount, null);

            foreach ($dcdata[$key] as $i => &$_dcdata) {
                $bdata   = $this->bitBuffer->buffer;
                $_dcdata = 0xff & $bdata[$i + $offset];
            }
            unset($_dcdata);

            $offset += $rsBlockDataCount;

            $rsPoly  = new Polynomial();
            $modPoly = new Polynomial();

            $i = 0;
            while ($i < $rsBlockTotal - $rsBlockDataCount) {
                $modPoly->setNum([1, $modPoly->gexp($i)]);
                $rsPoly->multiply($modPoly->num);
                $i++;
            }

            $rsPolyCount = count($rsPoly->num);
            $modPoly->setNum($dcdata[$key], $rsPolyCount - 1)->mod($rsPoly->num);
            $ecdata[$key] = \array_fill(0, $rsPolyCount - 1, null);
            $add          = count($modPoly->num) - count($ecdata[$key]);

            foreach ($ecdata[$key] as $i => &$_ecdata) {
                $modIndex = $i + $add;
                $_ecdata  = $modIndex >= 0 ? $modPoly->num[$modIndex] : 0;
            }
            unset($_ecdata);
            $totalCodeCount += $rsBlockTotal;
        }

        $data    = \array_fill(0, $totalCodeCount, null);
        $rsrange = \range(0, $rsBlockCount - 1);

        $i = 0;
        while ($i < $maxDcCount) {
            foreach ($rsrange as $key) {
                if ($i < \count($dcdata[$key])) {
                    $data[$index++] = $dcdata[$key][$i];
                }
            }
            $i++;
        }

        $i = 0;
        while ($i < $maxEcCount) {
            foreach ($rsrange as $key) {
                if ($i < \count($ecdata[$key])) {
                    $data[$index++] = $ecdata[$key][$i];
                }
            }
            $i++;
        }

        return $data;
    }

    /**
     * @param int $pattern
     * @throws QRCodeException
     */
    protected function mapData($pattern): void
    {
        $this->createData();
        $data      = $this->createBytes();
        $inc       = -1;
        $row       = $this->pixelCount - 1;
        $bitIndex  = 7;
        $byteIndex = 0;
        $dataCount = \count($data);

        for ($col = $this->pixelCount - 1; $col > 0; $col -= 2) {
            if ($col === 6) {
                $col--;
            }

            while (true) {
                $c = 0;
                while ($c < 2) {
                    $_col = $col - $c;

                    if ($this->matrix[$row][$_col] === null) {
                        $dark = false;

                        if ($byteIndex < $dataCount) {
                            $dark = (($data[$byteIndex] >> $bitIndex) & 1) === 1;
                        }

                        $a = $row + $_col;
                        $m = $row * $_col;

                        $vTemp        = [
                            QRConst::MASK_PATTERN000 => $a % 2,
                            QRConst::MASK_PATTERN001 => $row % 2,
                            QRConst::MASK_PATTERN010 => $_col % 3,
                            QRConst::MASK_PATTERN011 => $a % 3,
                            QRConst::MASK_PATTERN100 => (\floor($row / 2) + \floor($_col / 3)) % 2,
                            QRConst::MASK_PATTERN101 => $m % 2 + $m % 3,
                            QRConst::MASK_PATTERN110 => ($m % 2 + $m % 3) % 2,
                            QRConst::MASK_PATTERN111 => ($m % 3 + $a % 2) % 2,
                        ];
                        $MASK_PATTERN = $vTemp[$pattern];

                        if ($MASK_PATTERN === 0) {
                            $dark = !$dark;
                        }

                        $this->matrix[$row][$_col] = $dark;

                        $bitIndex--;
                        if ($bitIndex === -1) {
                            $byteIndex++;
                            $bitIndex = 7;
                        }
                    }
                    $c++;
                }

                $row += $inc;
                if ($row < 0 || $this->pixelCount <= $row) {
                    $row -= $inc;
                    $inc = -$inc;
                    break;
                }
            }
        }
    }

    protected function setupPositionProbePattern(): void
    {
        $range = \range(-1, 7);

        foreach ([[0, 0], [$this->pixelCount - 7, 0], [0, $this->pixelCount - 7]] as $grid) {
            $row = $grid[0];
            $col = $grid[1];

            $r = -1;
            while ($r < 8) {
                foreach ($range as $c) {
                    if ($row + $r <= -1
                        || $this->pixelCount <= $row + $r
                        || $col + $c <= -1
                        || $this->pixelCount <= $col + $c
                    ) {
                        continue;
                    }

                    $this->matrix[$row + $r][$col + $c] =
                        (0 <= $r && $r <= 6 && ($c === 0 || $c === 6))
                        || (0 <= $c && $c <= 6 && ($r === 0 || $r === 6))
                        || (2 <= $c && $c <= 4 && 2 <= $r && $r <= 4);
                }
                $r++;
            }
        }
    }

    protected function setupPositionAdjustPattern(): void
    {
        $range = QRConst::$PATTERN_POSITION[$this->typeNumber - 1];

        foreach ($range as $i => $posI) {
            foreach ($range as $j => $posJ) {
                if ($this->matrix[$posI][$posJ] !== null) {
                    continue;
                }
                for ($row = -2; $row <= 2; $row++) {
                    for ($col = -2; $col <= 2; $col++) {
                        $this->matrix[$posI + $row][$posJ + $col] =
                            $row === -2 || $row === 2
                            || $col === -2 || $col === 2
                            || ($row === 0 && $col === 0);
                    }
                }
            }
        }
    }

    protected function setPattern(): void
    {
        $this->setupPositionProbePattern();
        $this->setupPositionAdjustPattern();

        // setupTimingPattern
        for ($i = 8; $i < $this->pixelCount - 8; $i++) {
            if ($this->matrix[$i][6] !== null) {
                continue; // @codeCoverageIgnore
            }
            $this->matrix[6][$i] = $i % 2 === 0;
            $this->matrix[$i][6] = $this->matrix[6][$i];
        }
    }

    /**
     * @param bool $test
     * @param int  $maskPattern
     * @throws QRCodeException
     */
    protected function getMatrix($test, $maskPattern): void
    {
        $this->pixelCount = $this->typeNumber * 4 + 17;
        $this->matrix     = \array_fill(0, $this->pixelCount, \array_fill(0, $this->pixelCount, null));
        $this->setTypeInfo($test, $maskPattern);
        if ($this->typeNumber >= 7) {
            $this->setTypeNumber($test);
        }
        $this->mapData($maskPattern);
    }
}
