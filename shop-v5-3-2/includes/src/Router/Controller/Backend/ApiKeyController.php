<?php declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use JTL\Backend\Permissions;
use JTL\REST\Models\ApiKeyModel;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ApiKeyController
 * @package JTL\Router\Controller\Backend
 */
class ApiKeyController extends GenericModelController
{
    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::API_KEYS_VIEW);
        $this->getText->loadAdminLocale('pages/apikey');
        $this->smarty->assign('route', $this->route);

        $this->modelClass    = ApiKeyModel::class;
        $this->adminBaseFile = \ltrim($this->route, '/');

        return $this->handle(\SHOW_REST_API === false ? '404.tpl' : 'apikey.tpl');
    }
}
