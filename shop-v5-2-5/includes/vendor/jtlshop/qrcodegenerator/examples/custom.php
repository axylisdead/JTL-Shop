<?php declare(strict_types=1);

use qrcodegenerator\QRCode\Output\QROutputAbstract;
use qrcodegenerator\QRCode\QRCode;

require_once '../vendor/autoload.php';

/**
 * Class MyCustomOutput
 */
class MyCustomOutput extends QROutputAbstract
{
    /**
     * @return mixed
     */
    public function dump()
    {
        $output = '';
        for ($row = 0; $row < $this->pixelCount; $row++) {
            for ($col = 0; $col < $this->pixelCount; $col++) {
                $output .= (string)(int)$this->matrix[$row][$col];
            }
        }

        return $output;
    }
}

$starttime = microtime(true);

echo (new QRCode('otpauth://totp/test?secret=B3JX4VCVJDVNXNZ5&issuer=chillerlan.net', new MyCustomOutput()))->output();

echo PHP_EOL . 'QRCode: ' . round((microtime(true) - $starttime), 5);
