<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Shop4_OperatingSystem
 */
class Systemcheck_Tests_Shop4_OperatingSystem extends Systemcheck_Tests_ProgramTest
{
    protected $name          = 'Betriebssystem';
    protected $requiredState = 'Linux';
    protected $description   = 'JTL-Software empfiehlt den Betrieb mit Linux-Webservern. Der Betrieb unter Solaris, FreeBSD oder Windows wird weder empfohlen noch unterstützt.';
    protected $isOptional    = true;
    protected $isRecommended = true;

    private $unameMap = array(
        'CYGWIN_NT-5.1' => 'Windows',
        'Darwin'        => 'Mac OS X',
        'IRIX64'        => 'IRIX',
        'SunOS'         => 'Solaris/OpenSolaris',
        'WIN32'         => 'Windows',
        'WINNT'         => 'Windows'
    );

    private $archMap = array(
        'i386'   => 'Intel x86',
        'i486'   => 'Intel x86',
        'i586'   => 'Intel x86',
        'i686'   => 'Intel x86',
        'x86_64' => 'Intel x86_64',
        'sparc'  => 'SPARC'
    );

    public function execute()
    {
        // Operating system
        $os = php_uname('s');
        if (array_key_exists($os, $this->unameMap)) {
            $os = $this->unameMap[$os];
        }

        // Processor architecture
        $arch = php_uname('m');
        if (array_key_exists($arch, $this->archMap)) {
            $arch = $this->archMap[$arch];
        }

        $this->currentState = sprintf('%s (%s)', $os, $arch);
        switch ($os) {
            case 'Linux':
                $this->result = Systemcheck_Tests_Test::RESULT_OK;
                break;
            default:
                $this->result = Systemcheck_Tests_Test::RESULT_FAILED;
                break;
        }
    }
}
