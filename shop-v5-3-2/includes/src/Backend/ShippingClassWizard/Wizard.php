<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace JTL\Backend\ShippingClassWizard;

use Exception;
use JsonException;
use JTL\IO\IOError;
use JTL\Router\Controller\Backend\ShippingMethodsController;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;

/**
 * Class Wizard
 * @package JTL\Backend
 */
class Wizard
{
    public const MAX_CLASS_COUNT  = 13;
    public const WARN_CLASS_COUNT = 10;

    /** @var JTLSmarty */
    private JTLSmarty $smarty;

    /** @var Helper */
    private Helper $helper;

    /**
     * ShippingClassWizard constructor
     * @param JTLSmarty                 $smarty
     * @param ShippingMethodsController $controller
     */
    public function __construct(JTLSmarty $smarty, ShippingMethodsController $controller)
    {
        $this->smarty = $smarty;
        $this->helper = Helper::instance(Shop::Container()->getDB(), $controller);
    }

    /**
     * @param JTLSmarty                 $smarty
     * @param ShippingMethodsController $controller
     * @return self
     */
    public static function instance(JTLSmarty $smarty, ShippingMethodsController $controller): self
    {
        return new self($smarty, $controller);
    }

    /**
     * @param int             $shippingMethodId
     * @param string          $shippingClassIds
     * @param Definition|null $definition
     * @param bool            $suppressWarning
     * @return void
     */
    private function preRender(
        int $shippingMethodId,
        string $shippingClassIds,
        ?Definition $definition = null,
        bool $suppressWarning = false
    ): void {
        $wizardDefinition  = $definition ?? $this->helper->loadDefinition($shippingMethodId, $shippingClassIds);
        $wizardDescription = [];
        $shippingClasses   = $this->helper->getNamedShippingClasses();
        $shippingMethod    = $this->helper->getShippingMethod($shippingMethodId);

        $classCount = $this->helper->getShippingClassCount();
        if ($classCount > self::WARN_CLASS_COUNT) {
            $wizardDescription[] = \__('warningManyCombinations', $classCount, 2 ** $classCount - 2);
        }
        if (!$suppressWarning && !$wizardDefinition->isEqualHash(
            $this->helper->createResultHash($shippingClassIds)
        )) {
            $wizardDescription[] = \__('warningNotSavedYet');
        }
        if (\count($wizardDescription) > 0) {
            $this->smarty->assign('wizardDescription', $wizardDescription);
        }

        $this->smarty
            ->assign('shippingClasses', $shippingClasses)
            ->assign('shippingMethod', $shippingMethod)
            ->assign('wizard', $wizardDefinition);
    }

    /**
     * @return string
     * @throws \SmartyException
     */
    public function render(): string
    {
        return $this->smarty->fetch('tpl_inc/shippingclass_wizard_assign.tpl');
    }

    /**
     * @param int    $shippingMethodID
     * @param string $formData
     * @param string $resultHash
     * @return void
     * @throws JsonException
     */
    public function save(int $shippingMethodID, string $formData, string $resultHash): void
    {
        if ($shippingMethodID > 0 && !empty($formData)) {
            $this->helper->saveDefinition(
                $shippingMethodID,
                Definition::createFromForm(\unserialize($formData, ['allowed_classes' => false]), $resultHash)
            );
        }
    }

    /**
     * @param int    $id
     * @param string $shippingClassIds
     * @param string $definition
     * @param bool   $suppressWarning
     * @return string|IOError
     */
    public function ioRender(
        int    $id,
        string $shippingClassIds,
        string $definition,
        bool   $suppressWarning = false
    ): string|IOError {
        if ($this->helper->getShippingClassCount() > self::MAX_CLASS_COUNT) {
            return new IOError(__(
                'errorMaxClassCountExceeded',
                self::MAX_CLASS_COUNT
            ));
        }

        try {
            $this->preRender(
                $id,
                $shippingClassIds,
                empty($definition) ? null : Definition::createFromForm(
                    \unserialize($definition, ['allowed_classes' => false])
                ),
                $suppressWarning
            );

            return $this->render();
        } catch (Exception $e) {
            return new IOError($e->getMessage());
        }
    }

    /**
     * @param array $formData
     * @return object
     */
    public function ioCalculateMethods(array $formData): object
    {
        $shippingMethods = $this->helper->buildShippingClasses(Definition::createFromForm($formData));
        try {
            $wizardMethods = \json_encode(
                $this->helper->getActiveShippingClassesOverview($shippingMethods),
                \JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            $wizardMethods = '';
        }

        return (object)[
            'shippingMethods' => $shippingMethods,
            'wizardJsonSM'    => $wizardMethods,
            'resultHash'      => $this->helper->createResultHash($shippingMethods),
            'definition'      => \serialize($formData),
        ];
    }
}
