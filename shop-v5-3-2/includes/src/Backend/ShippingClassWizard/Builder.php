<?php declare(strict_types=1);

namespace JTL\Backend\ShippingClassWizard;

use Illuminate\Support\Collection;

/**
 * Class Builder
 * @package JTL\Backend\ShippingClassWizard
 */
final class Builder
{
    /** @var array */
    private array $shippingClassIds;

    /**
     * Builder constructor
     * @param array $shippingClassIds
     */
    public function __construct(array $shippingClassIds)
    {
        $this->shippingClassIds = $shippingClassIds;
    }

    /**
     * @param Collection $result
     * @return Collection
     */
    private function makeResult(Collection $result): Collection
    {
        return $result
            ->map(static function (array $item) {
                \sort($item);

                return \implode('-', $item);
            })->uniqueStrict()->sortBy(static function (string $value) {
                return $value;
            });
    }

    /**
     * @param array $exclude
     * @return array
     */
    private function getShippingClassIds(array $exclude = []): array
    {
        return \array_diff($this->shippingClassIds, $exclude);
    }

    /**
     * @param array $variants
     * @return array
     */
    private function buildCombinations(array $variants): array
    {
        $variants = \array_unique($variants, \SORT_NUMERIC);
        \sort($variants);

        $res = \array_map(static function ($item) {
            return [$item];
        }, $variants);

        $st = \count($variants) - 1;
        $of = 0;
        while ($st > 0) {
            $c = \count($res);
            for ($i = 0; $i < $c - $of - 1; $i++) {
                foreach ($variants as $variant) {
                    if (!\in_array($variant, $res[$i + $of], true)) {
                        $new = \array_merge($res[$i + $of], [$variant]);
                        \sort($new);
                        if (!\in_array($new, $res, true)) {
                            $res[] = $new;
                        }
                    }
                }
            }
            $st--;
            $of = $c;
        }

        return $res;
    }

    /**
     * @param array $merge
     * @param array $combinations
     * @param bool  $exclusive
     * @return array
     */
    private function mergeCombinations(array $merge, array $combinations, bool $exclusive = false): array
    {
        foreach (\array_keys($combinations) as $key) {
            $merged = \array_merge($combinations[$key], $merge);
            \sort($merged);
            $combinations[$key] = $merged;
        }

        if (!$exclusive) {
            \array_unshift($combinations, $merge);
        }

        return $combinations;
    }

    /**
     * @param array $set
     * @return array
     */
    private function buildAndCombinations(array $set): array
    {
        $res = [];
        $idx = [];
        $len = [];
        $st  = \count($set) - 1;

        $updateIdx = static function ($key, &$idx, $len) use (&$updateIdx) {
            if (++$idx[$key] >= $len[$key]) {
                $idx[$key] = 0;
                if ($key > 0) {
                    return $updateIdx($key - 1, $idx, $len);
                }

                return false;
            }

            return true;
        };

        foreach (\array_keys($set) as $key) {
            \sort($set[$key]);
            $idx[$key] = 0;
            $len[$key] = \count($set[$key]);
        }

        do {
            $ins = [];
            foreach ($set as $key => $item) {
                $ins[] = \is_array($item[$idx[$key]]) ? $item[$idx[$key]] : [$item[$idx[$key]]];
            }
            $res[] = \array_merge(...$ins);
        } while ($updateIdx($st, $idx, $len));

        return $res;
    }

    /**
     * @param array $variationKeys
     * @param array $variations
     * @return Collection
     */
    private function buildAndCombinationsFromKey(array $variationKeys, array $variations): Collection
    {
        $result    = new Collection();
        $andCombis = $this->buildAndCombinations($variationKeys);

        foreach ($andCombis as $variationKey) {
            $combinations = [];
            foreach ($variationKey as $variation => $key) {
                $combinations[] = $variations[$variation][$key];
            }
            $combinations = \array_merge(...$combinations);
            sort($combinations);
            $result = $result->merge([$combinations]);
        }

        return $result;
    }

    /**
     * @return Collection
     */
    public function combineAll(): Collection
    {
        $result = new Collection(
            $this->buildCombinations(
                $this->getShippingClassIds()
            )
        );

        return $this->makeResult($result);
    }

    /**
     * @param array $definitions
     * @return Collection
     */
    public function combineAllOr(array $definitions): Collection
    {
        $result = new Collection([]);

        foreach ($definitions as $definition) {
            $result = $result->merge(
                $this->mergeCombinations(
                    $definition,
                    $this->buildCombinations(
                        $this->getShippingClassIds($definition)
                    )
                )
            );
        }

        return $this->makeResult($result);
    }

    /**
     * @param array $definitions
     * @return Collection
     */
    public function combineAllAnd(array $definitions): Collection
    {
        $result    = new Collection([]);
        $andCombis = $this->buildAndCombinations($definitions);

        foreach ($andCombis as $combi) {
            $result = $result->merge(
                $this->mergeCombinations(
                    $combi,
                    $this->buildCombinations(
                        $this->getShippingClassIds($combi)
                    )
                )
            );
        }

        return $this->makeResult($result);
    }

    /**
     * @param array $definitions
     * @return Collection
     */
    public function combineSingleOr(array $definitions): Collection
    {
        $result     = new Collection([]);
        $keys       = \array_keys($definitions);
        $variations = $this->buildCombinations($keys);

        foreach ($variations as $variation) {
            $combinations = [];
            foreach ($variation as $key) {
                $combinations[] = $definitions[$key];
            }
            $combinations = \array_merge(...$combinations);
            \sort($combinations);
            $result = $result->merge([$combinations]);
        }

        return $this->makeResult($result);
    }

    /**
     * @param array $definitions
     * @return Collection
     */
    public function combineSingleAnd(array $definitions): Collection
    {
        $variations    = [];
        $variationKeys = [];

        foreach ($definitions as $definition) {
            $combination     = $this->buildCombinations(
                $definition
            );
            $variationKeys[] = \array_keys($combination);
            $variations[]    = $combination;
        }

        return $this->makeResult($this->buildAndCombinationsFromKey($variationKeys, $variations));
    }

    /**
     * @param array $definitions
     * @return Collection
     */
    public function exclusiveOr(array $definitions): Collection
    {
        return $this->makeResult(new Collection($definitions));
    }

    /**
     * @param array $definitions
     * @return Collection
     */
    public function exclusiveAnd(array $definitions): Collection
    {
        $variations    = [];
        $variationKeys = [];

        foreach ($definitions as $definition) {
            $combination     = \array_map(static function (int $item) {
                return [$item];
            }, $definition);
            $variationKeys[] = \array_keys($combination);
            $variations[]    = $combination;
        }

        return $this->makeResult($this->buildAndCombinationsFromKey($variationKeys, $variations));
    }

    /**
     * @param Collection $toInvert
     * @return Collection
     */
    public function invert(Collection $toInvert): Collection
    {
        return $this->combineAll()->diff($toInvert);
    }
}
