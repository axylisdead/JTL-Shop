<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 */

/**
 * Class PPCBannerConfig
 */
class PPCBannerConfig
{
    /** @var NiceDB */
    private $db;

    /** @var Plugin */
    private $plugin;

    /** @var PPCBannerConfigItems */
    private static $config;

    const PREFIX = 'jtl_paypal_ppc_banner_';

    const POSITION_PAGE_CART    = 'wk';
    const POSITION_PAGE_PRODUCT = 'product';
    const POSITION_PAGE_PAYMENT = 'payment';
    const POSITION_POPUP_CART   = 'miniwk';

    /**
     * PPCBannerConfig constructor.
     * @param Plugin $plugin
     * @param NiceDB     $db
     */
    private function __construct(Plugin $plugin, NiceDB $db)
    {
        $this->plugin = $plugin;
        $this->db     = $db;
    }

    /**
     * @param Plugin $plugin
     * @param NiceDB     $db
     * @return static
     */
    public static function instance(Plugin $plugin, NiceDB $db)
    {
        static $instance;

        return isset($instance) ? $instance : new static($plugin, $db);
    }

    /**
     * @param bool $forceLoad
     * @return PPCBannerConfigItems
     */
    private function loadConfig($forceLoad = false)
    {
        if (static::$config === null || $forceLoad) {
            static::$config = new PPCBannerConfigItems(gibPluginEinstellungen($this->plugin->kPlugin));
        }

        return static::$config;
    }

    /**
     * @param PPCBannerConfigItems $config
     * @return PPCBannerConfigItems
     */
    private function loadDefaults(PPCBannerConfigItems $config)
    {
        $prefix      = 'kPlugin_' . $this->plugin->kPlugin;
        $apiMode     = $config->get($prefix . '_paypalplus_api_live_sandbox', 'sandbox');
        $clientID    = $config->get($prefix . '_paypalplus_api_' . $apiMode . '_client_id', '');
        $tplDefaults = $this->getTplDefaults();

        return (new PPCBannerConfigItems([
            self::PREFIX . 'active'         => 'Y',
            self::PREFIX . 'use_consent'    => 'N',
            self::PREFIX . 'api_client_id'  => $clientID,

            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_minprice'       => '100',
            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_active'         => 'Y',
            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_layout'         => 'flex',
            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_logo_type'      => 'primary',
            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_text_size'      => '12',
            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_text_color'     => 'black',
            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_style_color'    => 'white',
            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_style_ratio'    => '8x1',
            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_query_selector' =>
                $tplDefaults['selector'][self::POSITION_PAGE_PRODUCT],
            self::PREFIX . self::POSITION_PAGE_PRODUCT . '_query_method'   =>
                $tplDefaults['method'][self::POSITION_PAGE_PRODUCT],

            self::PREFIX . self::POSITION_PAGE_CART . '_minprice'       => '100',
            self::PREFIX . self::POSITION_PAGE_CART . '_active'         => 'Y',
            self::PREFIX . self::POSITION_PAGE_CART . '_layout'         => 'flex',
            self::PREFIX . self::POSITION_PAGE_CART . '_logo_type'      => 'primary',
            self::PREFIX . self::POSITION_PAGE_CART . '_text_size'      => '12',
            self::PREFIX . self::POSITION_PAGE_CART . '_text_color'     => 'black',
            self::PREFIX . self::POSITION_PAGE_CART . '_style_color'    => 'white',
            self::PREFIX . self::POSITION_PAGE_CART . '_style_ratio'    => '8x1',
            self::PREFIX . self::POSITION_PAGE_CART . '_query_selector' =>
                $tplDefaults['selector'][self::POSITION_PAGE_CART],
            self::PREFIX . self::POSITION_PAGE_CART . '_query_method'   =>
                $tplDefaults['method'][self::POSITION_PAGE_CART],

            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_minprice'       => '100',
            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_active'         => 'Y',
            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_layout'         => 'flex',
            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_logo_type'      => 'primary',
            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_text_size'      => '12',
            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_text_color'     => 'black',
            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_style_color'    => 'white',
            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_style_ratio'    => '20x1',
            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_query_selector' =>
                $tplDefaults['selector'][self::POSITION_PAGE_PAYMENT],
            self::PREFIX . self::POSITION_PAGE_PAYMENT . '_query_method'   =>
                $tplDefaults['method'][self::POSITION_PAGE_PAYMENT],

            self::PREFIX . self::POSITION_POPUP_CART . '_minprice'       => '100',
            self::PREFIX . self::POSITION_POPUP_CART . '_active'         => 'Y',
            self::PREFIX . self::POSITION_POPUP_CART . '_layout'         => 'text',
            self::PREFIX . self::POSITION_POPUP_CART . '_logo_type'      => 'primary',
            self::PREFIX . self::POSITION_POPUP_CART . '_text_size'      => '12',
            self::PREFIX . self::POSITION_POPUP_CART . '_text_color'     => 'black',
            self::PREFIX . self::POSITION_POPUP_CART . '_style_color'    => 'white',
            self::PREFIX . self::POSITION_POPUP_CART . '_style_ratio'    => '1x1',
            self::PREFIX . self::POSITION_POPUP_CART . '_query_selector' =>
                $tplDefaults['selector'][self::POSITION_POPUP_CART],
            self::PREFIX . self::POSITION_POPUP_CART . '_query_method'   =>
                $tplDefaults['method'][self::POSITION_POPUP_CART],
        ]))->merge($config);
    }

    /**
     * @return array
     */
    public function getTplDefaults()
    {
        return [
            'selector' => [
                self::POSITION_PAGE_PRODUCT => '#add-to-cart > .form-inline:first',
                self::POSITION_PAGE_CART    => '.proceed',
                self::POSITION_PAGE_PAYMENT => '#fieldset-payment',
                self::POSITION_POPUP_CART   => '.cart-dropdown li:first .btn-group:last',
            ],
            'method' => [
                self::POSITION_PAGE_PRODUCT => 'after',
                self::POSITION_PAGE_CART    => 'prepend',
                self::POSITION_PAGE_PAYMENT => 'append',
                self::POSITION_POPUP_CART   => 'after',
            ],
        ];
    }

    /**
     * @param string       $name
     * @param mixed|null   $default
     * @return string
     */
    public function getConfigItem($name, $default = null)
    {
        return $this->loadDefaults($this->loadConfig())->get($name, $default);
    }

    /**
     * @param string $prefix
     * @return PPCBannerConfigItems
     * @noinspection PhpUnusedParameterInspection
     */
    public function getConfigItemsByPrefix($prefix)
    {
        return $this->loadDefaults($this->loadConfig())->filter(
            static function ($value, $key) use ($prefix) {
                return strpos($key, $prefix) === 0;
            }
        )->mapWithKeys(static function ($value, $key) use ($prefix) {
            return [str_replace($prefix, '', $key) => $value];
        });
    }

    /**
     * @param array  $items
     * @param string $prefix
     */
    public function saveConfigItems($items, $prefix = '')
    {
        self::$config = null;

        foreach ($items as $key => $value) {
            $this->db->delete('tplugineinstellungen', ['kPlugin', 'cName'], [$this->plugin->kPlugin, $prefix . $key]);
            $this->db->insert('tplugineinstellungen', (object)[
                'kPlugin' => $this->plugin->kPlugin,
                'cName'   => $prefix . $key,
                'cWert'   => $value,
            ]);
        }
    }

    /**
     * @param string $clientID
     * @param string $currency
     * @return string
     */
    public function getAPIUrl($clientID, $currency = 'EUR')
    {
        return 'https://www.paypal.com/sdk/js?client-id='. $clientID
            . '&currency=' . $currency
            . '&components=messages';
    }

    /**
     * @param string|null $position
     * @return bool
     */
    public function isPPCBannerActiv($position = null)
    {
        $settings = $this->getConfigItemsByPrefix(self::PREFIX);
        if ($settings->get('active', 'N') !== 'Y') {
            return false;
        }
        if ($position !== null && $settings->get($position . '_active', 'N') !== 'Y') {
            return false;
        }
        $selector = $settings->get($position . '_query_selector', '');

        return !($position !== null && empty($selector));
    }
}
