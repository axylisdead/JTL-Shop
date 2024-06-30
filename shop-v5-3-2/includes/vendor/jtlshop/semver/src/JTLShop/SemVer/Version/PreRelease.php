<?php declare(strict_types=1);

namespace JTLShop\SemVer\Version;

/**
 * Class PreRelease
 * @package JTLShop\SemVer\Version
 */
class PreRelease extends Versionable
{
    /**
     * "Greek" name
     *
     * @var string|null
     */
    private ?string $greek = null;

    /**
     * Release number
     *
     * @var int
     */
    private int $releaseNumber = 0;

    /**
     * Set the "greek" name of the pre-release status
     *
     * @param string $greek
     * @return $this
     */
    public function setGreek(string $greek): self
    {
        $this->greek = $greek;

        return $this;
    }

    /**
     * Set the release number
     *
     * @param int $releaseNumber
     * @return $this
     */
    public function setReleaseNumber(int $releaseNumber): self
    {
        $this->releaseNumber = $releaseNumber;

        return $this;
    }

    /**
     * Get the "greek" name of the pre-release status
     *
     * @return string|null
     */
    public function getGreek(): ?string
    {
        return $this->greek;
    }

    /**
     * Get the release number
     *
     * @return int
     */
    public function getReleaseNumber(): int
    {
        return $this->releaseNumber;
    }

    /**
     * Get string representation
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->getMajor() > 0 || $this->getMinor() > 0 || $this->getPatch() > 0) {
            return parent::__toString();
        }
        $preReleaseStr = $this->getGreek();
        if ($this->getReleaseNumber() > 0) {
            $preReleaseStr .= '.' . $this->getReleaseNumber();
        }

        return $preReleaseStr ?? '';
    }
}
