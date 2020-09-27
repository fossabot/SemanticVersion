<?php
/*
 * This file is part of the SemanticVersion package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\SemanticVersion;

use function array_values;
use function explode;
use function implode;
use function is_numeric;
use function preg_match;
use const PREG_UNMATCHED_AS_NULL;
use SoureCode\SemanticVersion\Exception\InvalidArgumentException;

/**
 * Immutable Version.
 *
 * @author Jason Schilling <jason@sourecode.dev>
 */
final class Version
{
    /**
     * @see https://semver.org/
     */
    private static string $expression = "/^(?P<major>0|[1-9]\d*)\.(?P<minor>0|[1-9]\d*)\.(?P<patch>0|[1-9]\d*)(?:-(?P<prerelease>(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*)(?:\.(?:0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*))*))?(?:\+(?P<buildmetadata>[0-9a-zA-Z-]+(?:\.[0-9a-zA-Z-]+)*))?$/";

    private int $major;

    private int $minor;

    private int $patch;

    /**
     * @var string[]
     */
    private array $preRelease;

    /**
     * @var string[]
     */
    private array $buildMetadata;

    public function __construct(int $major, int $minor, int $patch, array $preRelease = [], array $buildMetadata = [])
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = array_values($preRelease);
        $this->buildMetadata = array_values($buildMetadata);
    }

    public static function fromString(string $version): self
    {
        $matched = preg_match(self::$expression, $version, $matches, PREG_UNMATCHED_AS_NULL);

        if (0 === $matched || null === $matches['major'] || null === $matches['minor'] || null === $matches['patch']) {
            throw new InvalidArgumentException(sprintf('The "%s" version is invalid.', $version));
        }

        return new self(
            (int) ($matches['major']),
            (int) ($matches['minor']),
            (int) ($matches['patch']),
            (null !== $matches['prerelease']) ? explode('.', $matches['prerelease']) : [],
            (null !== $matches['buildmetadata']) ? explode('.', $matches['buildmetadata']) : [],
        );
    }

    public function compare(self $version): int
    {
        return static::comp($this, $version);
    }

    public static function comp(self $versionA, self $versionB): int
    {
        if ($versionA === $versionB) {
            return 0;
        }

        if (0 !== $result = static::compareMain($versionA, $versionB)) {
            return $result;
        }

        if (0 !== $result = static::comparePreRelease($versionA, $versionB)) {
            return $result;
        }

        return 0;
    }

    public static function compareMain(self $versionA, self $versionB): int
    {
        if (0 !== $result = static::compareIdentifiers($versionA->major, $versionB->major)) {
            return $result;
        }

        if (0 !== $result = static::compareIdentifiers($versionA->minor, $versionB->minor)) {
            return $result;
        }

        if (0 !== $result = static::compareIdentifiers($versionA->patch, $versionB->patch)) {
            return $result;
        }

        return 0;
    }

    /**
     * @param int|string $a
     * @param int|string $b
     */
    private static function compareIdentifiers($a, $b): int
    {
        $aIsNumber = is_numeric($a);
        $bIsNumber = is_numeric($b);

        if ($a === $b) {
            return 0;
        }

        if ($aIsNumber && !$bIsNumber) {
            return -1;
        }

        if ($bIsNumber && !$aIsNumber) {
            return 1;
        }

        if ($a < $b) {
            return -1;
        }

        return 1;
    }

    private static function comparePreRelease(self $versionA, self $versionB): int
    {
        $aLength = \count($versionA->preRelease);
        $bLength = \count($versionB->preRelease);

        if ($aLength && !$bLength) {
            return -1;
        } elseif (!$aLength && $bLength) {
            return 1;
        } elseif (!$aLength && !$bLength) {
            return 0;
        }

        $length = max($aLength, $bLength);

        for ($index = 0; $index <= $length; ++$index) {
            $aHasKey = \array_key_exists($index, $versionA->preRelease);
            $bHasKey = \array_key_exists($index, $versionB->preRelease);

            if (!$bHasKey) {
                return 1;
            } elseif (!$aHasKey) {
                return -1;
            } elseif ($versionA->preRelease[$index] === $versionB->preRelease[$index]) {
                continue;
            } else {
                return static::compareIdentifiers($versionA->preRelease[$index], $versionB->preRelease[$index]);
            }
        }

        return 0;
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function setMajor(int $major): self
    {
        return new self(
            $major,
            $this->minor,
            $this->patch,
            $this->preRelease,
            $this->buildMetadata
        );
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function setMinor(int $minor): self
    {
        return new self(
            $this->major,
            $minor,
            $this->patch,
            $this->preRelease,
            $this->buildMetadata
        );
    }

    public function getPatch(): int
    {
        return $this->patch;
    }

    public function setPatch(int $patch): self
    {
        return new self(
            $this->major,
            $this->minor,
            $patch,
            $this->preRelease,
            $this->buildMetadata
        );
    }

    /**
     * @return string[]
     */
    public function getPreRelease(): array
    {
        return $this->preRelease;
    }

    /**
     * @param string[] $preRelease
     */
    public function setPreRelease(array $preRelease): self
    {
        return new self(
            $this->major,
            $this->minor,
            $this->patch,
            $preRelease,
            $this->buildMetadata
        );
    }

    /**
     * @return string[]
     */
    public function getBuildMetadata(): array
    {
        return $this->buildMetadata;
    }

    /**
     * @param string[] $buildMetadata
     */
    public function setBuildMetadata(array $buildMetadata): self
    {
        return new self(
            $this->major,
            $this->minor,
            $this->patch,
            $this->preRelease,
            $buildMetadata
        );
    }

    public function __toString(): string
    {
        $version = sprintf('%d.%d.%d', $this->major, $this->minor, $this->patch);

        if (\count($this->preRelease) > 0) {
            $version .= '-'.implode('.', $this->preRelease);
        }

        if (\count($this->buildMetadata) > 0) {
            $version .= '+'.implode('.', $this->buildMetadata);
        }

        return $version;
    }
}
