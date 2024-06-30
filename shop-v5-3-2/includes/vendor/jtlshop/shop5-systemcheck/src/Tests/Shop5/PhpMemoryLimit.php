<?php declare(strict_types=1);

namespace Systemcheck\Tests\Shop5;

use Systemcheck\Tests\PhpConfigTest;

/**
 * Class PhpMemoryLimit
 * @package Systemcheck\Tests\Shop5
 */
class PhpMemoryLimit extends PhpConfigTest
{
    protected const MEMORY_LIMIT_MB = '128';

    protected $name          = 'memory_limit';
    protected $requiredState = '>= ' . self::MEMORY_LIMIT_MB . 'MB';
    protected $description   = '';

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $memoryLimit        = \ini_get('memory_limit');
        $this->currentState = $memoryLimit;

        return ($memoryLimit == -1
            || $this->shortHandToInt($memoryLimit) >= $this->shortHandToInt(self::MEMORY_LIMIT_MB . 'M'));
    }
}
