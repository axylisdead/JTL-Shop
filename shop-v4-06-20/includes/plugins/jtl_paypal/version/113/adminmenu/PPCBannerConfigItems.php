<?php
/**
 * @copyright (c) JTL-Software-GmbH
 * @license http://jtl-url.de/jtlshoplicense
 */

/**
 * Class PPCBannerConfigItem
 */
class PPCBannerConfigItems
{
    /** @var array */
    private $items;

    /**
     * PPCBannerConfigItems constructor.
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param string $name
     * @param null   $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return isset($this->items[$name]) ? $this->items[$name] : $default;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->items;
    }

    /**
     * @param self $items
     * @return self
     */
    public function merge(PPCBannerConfigItems $items)
    {
        return new self(array_merge($this->items, $items->asArray()));
    }

    /**
     * @param callable $callback
     * @return self
     */
    public function filter($callback)
    {
        return new self(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @param $callback
     * @return PPCBannerConfigItems
     */
    public function mapWithKeys($callback)
    {
        $result = [];

        foreach ($this->items as $key => $value) {
            $assoc = $callback($value, $key);

            foreach ($assoc as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return new static($result);
    }
}
