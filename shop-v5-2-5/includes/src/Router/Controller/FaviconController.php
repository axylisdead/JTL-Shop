<?php declare(strict_types=1);

namespace JTL\Router\Controller;

use JTL\Shop;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\Response\RedirectResponse;
use League\Route\RouteGroup;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class FaviconController
 * @package JTL\Router\Controller
 */
class FaviconController extends AbstractController
{
    /**
     * @inheritdoc
     */
    public function register(RouteGroup $route, string $dynName): void
    {
        $route->get('/favicon.ico', [$this, 'getResponse']);
    }

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $favURL       = $this->getFaviconURL(Shop::getURL());
        if (!$this->init()) {
            return $this->notFoundResponse($request, $args, $smarty);
        }

        return new RedirectResponse($favURL, 301);
    }
}
