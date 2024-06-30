<?php
/**
 * @copyright JTL-Software-GmbH
 * @package jtl\Systemcheck\Shop4
 */

/**
 * Systemcheck_Tests_Test
 */
abstract class Systemcheck_Tests_Test implements JsonSerializable
{
    /**
     * RESULT_OK
     */
    const RESULT_OK = '0';

    /**
     * RESULT_FAILED
     */
    const RESULT_FAILED = '1';

    /**
     * RESULT_UNKNOWN
     */
    const RESULT_UNKNOWN = '2';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $currentState;

    /**
     * @var int
     */
    protected $result;

    /**
     * @var string
     */
    protected $requiredState;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $isRecommended = false;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRequiredState()
    {
        return $this->requiredState;
    }

    /**
     * @return string
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @var bool
     */
    protected $isOptional = false;

    /**
     * @return bool
     */
    public function getIsOptional()
    {
        return $this->isOptional;
    }

    /**
     * getIsRecommended
     * @return bool
     */
    public function getIsRecommended()
    {
        return $this->isRecommended;
    }

    /**
     * @return bool|string
     */
    public function getIsReplaceableBy()
    {
        return property_exists($this, 'isReplaceableBy')
            ? $this->isReplaceableBy
            : false;
    }

    /**
     * @return bool
     */
    public function getRunStandAlone()
    {
        return property_exists($this, 'runStandAlone')
            ? $this->runStandAlone
            : null; // do not change to 'false'! we need three states here!
    }

    /**
     * @return int
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Systemcheck_Tests_Test constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return void
     */
    abstract public function execute();

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
