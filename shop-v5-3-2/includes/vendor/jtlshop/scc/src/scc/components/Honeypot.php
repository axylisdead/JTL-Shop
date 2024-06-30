<?php declare(strict_types=1);

namespace scc\components;

/**
 * Class Honeypot
 * @package scc\components
 */
class Honeypot extends AbstractFunctionComponent
{
    /**
     * Honeypot constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('honeypot.tpl');
        $this->setName('honeypot');
    }
}
