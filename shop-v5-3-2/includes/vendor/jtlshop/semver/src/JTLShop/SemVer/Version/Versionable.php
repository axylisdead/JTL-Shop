<?php declare(strict_types=1);

namespace JTLShop\SemVer\Version;

/**
 * Class Versionable
 * @package JTLShop\SemVer\Version
 */
abstract class Versionable
{
    /**
     * Prefix of version
     *
     * @var string|null
     */
    private ?string $prefix = null;

    /**
     * Major version
     *
     * @var int
     */
    private int $major = 0;

    /**
     * Minor version
     *
     * @var int
     */
    private int $minor = 0;

    /**
     * Patch version
     *
     * @var int
     */
    private int $patch = 0;

    /**
     * @var bool
     */
    private bool $selectLastPatch = false;

    /**
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @param string|null $prefix
     * @return $this
     */
    public function setPrefix(?string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return int
     */
    public function getMajor(): int
    {
        return $this->major;
    }

    /**
     * @param int $major
     * @return Versionable
     */
    public function setMajor(int $major): self
    {
        $this->major = $major;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinor(): int
    {
        return $this->minor;
    }

    /**
     * @param int $minor
     * @return Versionable
     */
    public function setMinor(int $minor): self
    {
        $this->minor = $minor;

        return $this;
    }

    /**
     * @return int
     */
    public function getPatch(): int
    {
        return $this->patch;
    }

    /**
     * @param int $patch
     * @return Versionable
     */
    public function setPatch(int $patch): self
    {
        $this->patch = $patch;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSelectLastPatch(): bool
    {
        return $this->selectLastPatch;
    }

    /**
     * @param bool $selectLastPatch
     * @return Versionable
     */
    public function setSelectLastPatch(bool $selectLastPatch): self
    {
        $this->selectLastPatch = $selectLastPatch;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return \sprintf(
            '%s%d.%d.%d',
            $this->getPrefix(),
            $this->getMajor(),
            $this->getMinor(),
            $this->getPatch()
        );
    }
}
