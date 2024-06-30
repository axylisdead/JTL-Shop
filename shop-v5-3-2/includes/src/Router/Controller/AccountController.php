<?php declare(strict_types=1);

namespace JTL\Router\Controller;

use JTL\Customer\AccountController as CustomerAccountController;
use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AccountController
 * @package JTL\Router\Controller
 */
class AccountController extends AbstractController
{
    /**
     * @inheritdoc
     */
    public function init(): bool
    {
        parent::init();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        Shop::setPageType($this->state->pageType);
        require_once \PFAD_ROOT . \PFAD_INCLUDES . 'bestellvorgang_inc.php';

        $linkService           = Shop::Container()->getLinkService();
        $this->currentLink     = $linkService->getSpecialPage(\LINKTYP_LOGIN);
        $controller            = new CustomerAccountController(
            $this->db,
            $this->alertService,
            $linkService,
            $this->smarty
        );
        $customer              = $controller->handleRequest();
        $this->canonicalURL    = $this->currentLink->getURL($this->languageID);
        $this->customerGroupID = $customer->getGroupID();
        $this->preRender();
        \executeHook(\HOOK_JTL_PAGE);

        return $this->smarty->getResponse('account/index.tpl');
    }
}
