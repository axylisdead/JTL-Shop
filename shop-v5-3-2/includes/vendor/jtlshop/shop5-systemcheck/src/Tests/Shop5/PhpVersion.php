<?php declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\ProgramTest;
use Systemcheck\Tests\AbstractTest;

/**
 * Class PhpVersion
 * @package Systemcheck\Tests\Shop5
 */
class PhpVersion extends ProgramTest
{
    protected $name          = 'PHP-Version';
    protected $requiredState = '>8.1.0';
    protected $description   = '';
    protected $isOptional    = false;
    protected $isRecommended = false;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $this->currentState = \PHP_VERSION;

        return \version_compare($this->currentState, '8.1.0', '>=');
    }
}
