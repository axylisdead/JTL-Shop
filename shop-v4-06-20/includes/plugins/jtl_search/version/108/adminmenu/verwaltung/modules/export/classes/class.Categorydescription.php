<?php

/**
 * Class Categorydescription
 */
class Categorydescription extends Document
{
    /**
     * @access protected
     * @var int
     */
    protected $kCategory;

    /**
     * @access protected
     * @var string
     */
    protected $cLanguageIso;

    /**
     * @access protected
     * @var string
     */
    protected $cDescription;

    /**
     * @param $kCategory
     * @return $this
     */
    public function setCategory($kCategory)
    {
        $this->kCategory = intval($kCategory);

        return $this;
    }

    /**
     * @param $cLanguageIso
     * @return $this
     */
    public function setLanguageIso($cLanguageIso)
    {
        $this->cLanguageIso = $cLanguageIso;

        return $this;
    }

    /**
     * @param $cDescription
     * @return $this
     */
    public function setDescription($cDescription)
    {
        $this->cDescription = $this->prepareString($cDescription);

        return $this;
    }

    /**
     * Gets the kCategory
     *
     * @access public
     * @return int
     */
    public function getCategory()
    {
        return $this->kCategory;
    }

    /**
     * Gets the cLanguageIso
     *
     * @access public
     * @return string
     */
    public function getLanguageIso()
    {
        return $this->cLanguageIso;
    }

    /**
     * Gets the cDescription
     *
     * @access public
     * @return string
     */
    public function getDescription()
    {
        return $this->cDescription;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return get_class();
    }
}
