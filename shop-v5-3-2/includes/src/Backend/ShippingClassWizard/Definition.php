<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace JTL\Backend\ShippingClassWizard;

use InvalidArgumentException;
use JsonException;
use JsonSerializable;

/**
 * Class Definition
 * @package JTL\Backend\ShippingClassWizard
 */
final class Definition implements JsonSerializable
{
    /** @var string */
    private string $combinationType;

    /** @var DefinitionPart[] */
    private array $definitionParts = [];

    /** @var string */
    private string $logic;

    /** @var bool */
    private bool $inverted;

    /** @var string  */
    private string $resultHash = '';

    /**
     * Definition constructor
     * @param string $combinationType
     * @param string $logic
     * @param bool   $inverted
     */
    private function __construct(
        string $combinationType = CombineTypes::ALL,
        string $logic = CombineTypes::LOGIC_OR,
        bool $inverted = false
    ) {
        $this->setCombinationType($combinationType);
        $this->setLogic($logic);
        $this->setInverted($inverted);
    }

    /**
     * @param string $jsonStr
     * @param string $resultHash
     * @return self
     * @throws JsonException
     */
    public static function jsonDecode(string $jsonStr, string $resultHash): self
    {
        $data     = \json_decode($jsonStr, false, 64, \JSON_THROW_ON_ERROR);
        $instance = new self(
            $data->combinationType ?? CombineTypes::ALL,
            $data->logic ?? CombineTypes::LOGIC_OR,
            $data->inverted ?? false
        );
        foreach (($data->definitionParts ?? []) as $part) {
            $instance->addDefinitionPart(
                DefinitionPart::jsonDecode(\json_encode($part, \JSON_THROW_ON_ERROR))
            );
        }

        return $instance->setResultHash($resultHash);
    }

    /**
     * @param array  $data
     * @param string $resultHash
     * @return self
     */
    public static function createFromForm(array $data, string $resultHash = ''): self
    {
        $instance = new self();

        $show = $data['show'] ?? 'ever';
        $cond = $data['condition'] ?? 'or';
        $excl = $data['exclusive'] ?? 'inclusive';

        if ($show === 'ever') {
            $instance->setCombinationType(CombineTypes::ALL);
            $data['combi'] = [];
        } else {
            $instance->setInverted($show === 'not')
                     ->setLogic($cond === 'and'
                         ? CombineTypes::LOGIC_AND
                         : CombineTypes::LOGIC_OR)
                     ->setCombinationType($excl === 'inclusive'
                         ? CombineTypes::COMBINE_ALL
                         : CombineTypes::COMBINE_SINGLE);
            if ($cond === 'xor') {
                $instance->setCombinationType(CombineTypes::EXCLUSIVE);
            }
        }

        foreach (($data['combi'] ?? []) as $combi) {
            $combi['logic'] = $cond === 'and' ? CombineTypes::LOGIC_OR : CombineTypes::LOGIC_AND;
            $instance->addDefinitionPart(DefinitionPart::createFromForm($combi));
        }

        return $instance->setResultHash($resultHash);
    }

    /**
     * @param string $classIds
     * @return self
     */
    public static function createEmpty(string $classIds = ''): self
    {
        $instance = new self(CombineTypes::ALL, CombineTypes::LOGIC_OR);
        if ($classIds !== '' && !\str_starts_with($classIds, '-')) {
            $instance->setCombinationType(CombineTypes::EXCLUSIVE);
            $parts = \explode(' ', $classIds);
            foreach ($parts as $Ids) {
                $instance->addDefinitionPart(DefinitionPart::createFromClassIds($Ids));
            }
        }

        return $instance->setResultHash('');
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): object
    {
        return (object)[
            'combinationType' => $this->getCombinationType(),
            'logic'           => $this->getLogic(),
            'inverted'        => $this->isInverted(),
            'definitionParts' => $this->getDefinitionParts(),
        ];
    }

    /**
     * @return string
     */
    public function mapShowing(): string
    {
        if ($this->getCombinationType() === CombineTypes::ALL) {
            return 'ever';
        }

        return $this->isInverted() ? 'not' : 'show';
    }

    /**
     * @return string
     */
    public function mapCondition(): string
    {
        if ($this->getLogic() === CombineTypes::LOGIC_AND) {
            return 'and';
        }

        return $this->getCombinationType() === CombineTypes::EXCLUSIVE ? 'xor' : 'or';
    }

    /**
     * @return string
     */
    public function mapExclusive(): string
    {
        return $this->getCombinationType() === CombineTypes::COMBINE_ALL ? 'inclusive' : 'exclusive';
    }

    /**
     * @return string
     */
    public function getResultHash(): string
    {
        return $this->resultHash;
    }

    /**
     * @param string $resultHash
     * @return self
     */
    public function setResultHash(string $resultHash): self
    {
        $this->resultHash = $resultHash;

        return $this;
    }

    /**
     * @param string $resultHash
     * @return bool
     */
    public function isEqualHash(string $resultHash): bool
    {
        return $resultHash === $this->getResultHash();
    }

    /**
     * @return string
     */
    public function getCombinationType(): string
    {
        return $this->combinationType;
    }

    /**
     * @param string $combinationType
     * @return self
     */
    public function setCombinationType(string $combinationType): self
    {
        if (!\in_array($combinationType, CombineTypes::ALL_TYPES, true)) {
            throw new InvalidArgumentException('no valid combination type');
        }

        $this->combinationType = $combinationType;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogic(): string
    {
        return $this->logic;
    }

    /**
     * @param string $logic
     * @return self
     */
    public function setLogic(string $logic): self
    {
        if (!\in_array($logic, [CombineTypes::LOGIC_OR, CombineTypes::LOGIC_AND], true)) {
            throw new InvalidArgumentException(
                'logic must be "' . CombineTypes::LOGIC_OR . '" or "' . CombineTypes::LOGIC_AND . '"'
            );
        }
        $this->logic = $logic;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInverted(): bool
    {
        return $this->inverted;
    }

    /**
     * @param bool $inverted
     * @return self
     */
    public function setInverted(bool $inverted): self
    {
        $this->inverted = $inverted;

        return $this;
    }

    /**
     * @return DefinitionPart[]
     */
    public function getDefinitionParts(): array
    {
        return $this->definitionParts;
    }

    /**
     * @param DefinitionPart $definitionPart
     * @return self
     */
    public function addDefinitionPart(DefinitionPart $definitionPart): self
    {
        $this->definitionParts[] = $definitionPart;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDefinitionParts(): bool
    {
        return \count($this->getDefinitionParts()) > 0;
    }

    /**
     * @return bool
     */
    public function isLogicOr(): bool
    {
        return $this->getLogic() === CombineTypes::LOGIC_OR;
    }

    /**
     * @return bool
     */
    public function isLogicAnd(): bool
    {
        return $this->getLogic() === CombineTypes::LOGIC_AND;
    }

    /**
     * @return array[]
     */
    public function getAllClassDefinitions(): array
    {
        return \array_values(\array_map(static function (DefinitionPart $item) {
            return \array_values($item->getShippingClasses());
        }, $this->getDefinitionParts()));
    }
}
