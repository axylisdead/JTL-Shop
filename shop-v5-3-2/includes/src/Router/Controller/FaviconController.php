<?php declare(strict_types=1);

namespace JTL\Router\Controller;

use JTL\Router\Middleware\FaviconFileCheckMiddleware;
use JTL\Smarty\JTLSmarty;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Stream;
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
        $faviconFileCheckMiddleware = new FaviconFileCheckMiddleware();
        $supportedFiles             = '{file:favicon.ico|favicon.svg|apple-touch-icon.png|android-chrome-192x192.png|'
            . 'android-chrome-512x512.png|site.webmanifest|browserconfig.xml|safari-pinned-tab.svg|mstile-70x70.png|'
            . 'mstile-144x144.png|mstile-150x150.png|mstile-310x150.png|mstile-310x310.png}';
        $route->get(\sprintf('%s', $supportedFiles), [$this, 'getResponse'])
            ->middleware($faviconFileCheckMiddleware);
    }

    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $file         = $args['file'];
        $this->smarty = $smarty;
        $favPath      = $this->getFaviconPath($file);
        $mimeType     = \mime_content_type($favPath);
        if (!$this->init()) {
            return $this->notFoundResponse($request, $args, $smarty);
        }

        return new Response(new Stream($favPath, 'rb'), 200, [
            'Content-Type'   => $mimeType,
            'Content-Length' => \filesize($favPath),
            'Cache-Control'  => 'max-age=604800',
            'Last-Modified'  => \gmdate('D, d M Y H:i:s', \filemtime($favPath))
        ]);
    }

    /**
     * @param string $file
     * @return string
     */
    protected function getFaviconPath(string $file = 'favicon.ico'): string
    {
        $templateDir      = $this->smarty->getTemplateDir($this->smarty->context);
        $shopTemplatePath = $this->smarty->getTemplateUrlPath();
        if (\file_exists($templateDir . 'favicon/' . $file)) {
            $faviconPath = $shopTemplatePath . 'favicon/' . $file;
        } elseif (\file_exists($templateDir . $file)) {
            $faviconPath = $shopTemplatePath . $file;
        } elseif (\file_exists(\PFAD_ROOT . $file)) {
            $faviconPath = $file;
        } elseif ($file === 'favicon.svg' && \file_exists($shopTemplatePath . 'favicon/favicon.ico')) {
            $faviconPath = $shopTemplatePath . 'favicon/favicon.ico';
        } elseif (($file === 'favicon.svg' || $file === 'favicon.ico')
            && \file_exists($templateDir . 'themes/base/images/favicon.ico')
        ) {
            $faviconPath = $shopTemplatePath . 'themes/base/images/favicon.ico';
        } else {
            $faviconPath = $shopTemplatePath . 'favicon/favicon-default.ico';
        }

        return $faviconPath;
    }
}
