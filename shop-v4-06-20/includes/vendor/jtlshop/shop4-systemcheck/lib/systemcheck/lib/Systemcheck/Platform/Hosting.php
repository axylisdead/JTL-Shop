<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Platform_Hosting
 */
class Systemcheck_Platform_Hosting
{

    /**
     * PROVIDER_1UND1
     */
    const PROVIDER_1UND1 = '1und1';

    /**
     * PROVIDER_STRATO
     */
    const PROVIDER_STRATO = 'strato';

    /**
     * PROVIDER_HOSTEUROPE
     */
    const PROVIDER_HOSTEUROPE = 'hosteurope';

    /**
     * PROVIDER_ALFAHOSTING
     */
    const PROVIDER_ALFAHOSTING = 'alfahosting';

    /**
     * PROVIDER_JTL
     */
    const PROVIDER_JTL = 'jtl';

    /**
     * PROVIDER_HETZNER
     */
    const PROVIDER_HETZNER = 'hetzner';

    /**
     * hostname
     * @var string
     */
    protected $hostname;

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @var string
     */
    protected $documentRoot;

    /**
     * @return string
     */
    public function getDocumentRoot()
    {
        return $this->documentRoot;
    }

    /**
     * @var string
     */
    protected $provider = null;

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return string
     */
    public function getPhpVersion()
    {
        return phpversion();
    }

    /**
     * __construct
     */
    public function __construct()
    {
        $this->documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $this->detect();
    }

    /**
     * detect
     * @return void
     */
    private function detect()
    {
        $hostname = gethostbyaddr($_SERVER['SERVER_ADDR']);

        if (preg_match('/jtl-software\.de$/', $hostname)) {
            $this->provider = Systemcheck_Platform_Hosting::PROVIDER_JTL;
        } elseif (preg_match('/hosteurope\.de$/', $hostname)) {
            $this->provider = Systemcheck_Platform_Hosting::PROVIDER_HOSTEUROPE;
        } elseif (preg_match('/your-server\.de$/', $hostname)) {
            $this->provider = Systemcheck_Platform_Hosting::PROVIDER_HETZNER;
        } elseif (preg_match('/kundenserver\.de$/', $hostname)) {
            $this->provider = Systemcheck_Platform_Hosting::PROVIDER_1UND1;
        } elseif (preg_match('/stratoserver\.net$/', $hostname)) {
            $this->provider = Systemcheck_Platform_Hosting::PROVIDER_STRATO;
        } elseif (preg_match('/alfahosting-server\.de$/', $hostname)) {
            $this->provider = Systemcheck_Platform_Hosting::PROVIDER_ALFAHOSTING;
        }

        $this->hostname = $hostname;
    }

}
