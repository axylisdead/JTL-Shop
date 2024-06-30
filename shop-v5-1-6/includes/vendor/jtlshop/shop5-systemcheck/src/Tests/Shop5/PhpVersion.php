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
    protected $requiredState = '7.3.0 - 8.0.x';
    protected $description   = '';
    protected $isOptional    = false;
    protected $isRecommended = false;

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $version            = \PHP_VERSION;
        $this->currentState = $version;

        return \version_compare($version, '7.3.0', '>=') && \version_compare($version, '8.1.0', '<');
    }
}
