<?php declare(strict_types=1);

namespace JTLShop\SemVer\Version;

use InvalidArgumentException;

/**
 * Class Build
 * @package JTLShop\SemVer\Version
 */
class Build
{
    /**
     * Build number
     *
     * @var int
     */
    private int $number = 0;

    /**
     * Parts
     *
     * @var array
     */
    private array $parts = [];

    /**
     * Get the build number
     *
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * Set the build number
     *
     * @param int $number
     * @return $this
     */
    public function setNumber($number): self
    {
        if ($number < 0) {
            throw new InvalidArgumentException(
                'Build number "' . $number . '" is invalid'
            );
        }
        $this->number = $number;

        return $this;
    }

    /**
     * Get the build parts
     *
     * @return array[int]string
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * Set the build parts
     *
     * @param array $parts
     * @return $this
     */
    public function setParts(array $parts): self
    {
        $this->parts = [];
        foreach ($parts as $part) {
            $this->addPart($part);
        }

        return $this;
    }

    /**
     * Add a part to the build parts stack
     *
     * @param string $part
     * @return $this
     */
    public function addPart(string $part): self
    {
        // Sanity check
        if (!\ctype_alnum($part)) {
            throw new InvalidArgumentException(
                'Build part "' . $part . '" is not alpha numerical'
            );
        }
        $this->parts[] = $part;

        return $this;
    }

    /**
     * Get string representation
     *
     * @return string
     */
    public function __toString()
    {
        // If there are other parts
        if (\count($this->getParts()) > 0) {
            $parts = ['build'];
            // Add number if we have one
            if ($this->getNumber() !== null) {
                $parts[] = $this->getNumber();
            }
            $parts[] = \implode('.', $this->getParts());

            return \implode('.', $parts);
        }
        // No number, no parts, no output.
        if ($this->getNumber() === null) {
            return '';
        }

        return 'build.' . $this->getNumber();
    }
}
