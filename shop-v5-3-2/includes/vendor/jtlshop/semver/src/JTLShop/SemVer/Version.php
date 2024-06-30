<?php declare(strict_types=1);

namespace JTLShop\SemVer;

use InvalidArgumentException;
use JTLShop\SemVer\Version\Build;
use JTLShop\SemVer\Version\PreRelease;
use JTLShop\SemVer\Version\Versionable;

/**
 * Class Version
 * @package JTLShop\SemVer
 */
class Version extends Versionable
{
    /**
     * Pre release version
     *
     * @var PreRelease|null
     */
    private ?PreRelease $preRelease = null;

    /**
     * Build version
     *
     * @var Build|null
     */
    private ?Build $build = null;

    /**
     * Original version string
     *
     * @var string
     */
    private string $originalVersionString = '';

    /**
     * Set the original version string for later usage
     *
     * @param string $version
     * @return $this
     */
    public function setOriginalVersion(string $version): self
    {
        $this->originalVersionString = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalVersion(): string
    {
        return $this->originalVersionString;
    }

    /**
     * @return PreRelease
     */
    public function getPreRelease(): PreRelease
    {
        return $this->preRelease;
    }

    /**
     * @param PreRelease $preRelease
     * @return $this
     */
    public function setPreRelease(PreRelease $preRelease): self
    {
        $this->preRelease = $preRelease;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasPreRelease(): bool
    {
        return ($this->preRelease instanceof PreRelease);
    }

    /**
     * @return Build|null
     */
    public function getBuild(): ?Build
    {
        return $this->build;
    }

    /**
     * @param Build $build
     * @return $this
     */
    public function setBuild(Build $build): self
    {
        $this->build = $build;

        return $this;
    }

    /**
     * Does this Version have a build?
     *
     * @return bool
     */
    public function hasBuild(): bool
    {
        return $this->build instanceof Build;
    }

    /**
     * Get the next logical version relative to the provided base version. If
     * no base is supplied, base will be the same as the current version.
     *
     * @param mixed|null $base
     * @return Version
     * @throws InvalidArgumentException
     */
    public function next(mixed $base = null): self
    {
        //  Ensure that $base is a Version. Parse it if we must, use ourself if
        //  it is empty.
        if (empty($base)) {
            $base = $this;
        } elseif (\is_string($base)) {
            $base = Parser::parse($base);
        } elseif (!$base instanceof self) {
            throw new InvalidArgumentException('$base must be of type Version');
        }

        // If the base is ahead of this Version then the next version will be
        // the base.
        if (Compare::greaterThan($base, $this)) {
            return $base->cleanCopy();
        }

        $next = new self();

        $next->setPrefix($this->getPrefix());
        $next->setMajor($this->getMajor());
        $next->setMinor($this->getMinor());
        $next->setPatch($this->getPatch());

        if ($base->hasPreRelease()) {
            if ($this->hasPreRelease()) {
                // We already know that $base is less than or equal to $this
                // and we won't be jumping to the next greek value. So it is
                // safe use $this prerelease and just increment the release
                // number.
                $preRelease = $this->getPreRelease();
                $pre        = new PreRelease();
                if (!empty($preRelease->getGreek())) {
                    $pre->setGreek($preRelease->getGreek());
                    $pre->setReleaseNumber($preRelease->getReleaseNumber() + 1);
                } else {
                    $pre->setPatch($preRelease->getPatch() + 1);
                }

                $next->setPreRelease($pre);
            } else {
                throw new InvalidArgumentException('This version has left prerelease without updating the base.
                 Base should not be prerelease.');
            }
        } elseif (!$this->hasPreRelease()) {
            $next->setPatch($this->getPatch() + 1);
        }
        // The case of $this having a pre-release when $base does not means
        // that we are essentially just leaving pre-release. Nothing needs to
        // be done.

        return $next;
    }

    /**
     * Create a new Version that discards the entity information of build and
     * originalVersionString
     *
     * @return Version
     */
    public function cleanCopy(): self
    {
        $version = new self();

        $version->setPrefix($this->getPrefix());
        $version->setMajor($this->getMajor());
        $version->setMinor($this->getMinor());
        $version->setPatch($this->getPatch());

        if ($this->hasPreRelease()) {
            $version->preRelease = clone($this->getPreRelease());
        }

        return $version;
    }

    /**
     * String representation of this Version
     *
     * @return string
     */
    public function __toString()
    {
        $string = parent::__toString();

        // Add pre-release
        if ($this->hasPreRelease()) {
            $string .= '-' . $this->getPreRelease();
        }

        // Add build
        if ($this->hasBuild()) {
            $string .= '+' . $this->getBuild();
        }

        return $string;
    }

    /**
     * Parse a new version or return an existing version
     *
     * @param int|string|Version $version
     * @return Version
     */
    public static function parse(int|string|Version $version): self
    {
        if ($version instanceof self) {
            return $version;
        }

        return Parser::parse($version);
    }

    /**
     * Check if the version is greater than another version
     *
     * @param int|string|Version $v2
     * @return bool
     */
    public function greaterThan(int|string|Version $v2): bool
    {
        return Compare::greaterThan($this, self::parse($v2));
    }

    /**
     * Check if the version is smaller than another version
     *
     * @param int|string|Version $v2
     * @return bool
     */
    public function smallerThan(int|string|Version $v2): bool
    {
        return Compare::smallerThan($this, self::parse($v2));
    }

    /**
     * Check if the version is equals to another version
     *
     * @param int|string|Version $v2
     * @return bool
     */
    public function equals(int|string|Version $v2): bool
    {
        return Compare::equals($this, self::parse($v2));
    }
}
