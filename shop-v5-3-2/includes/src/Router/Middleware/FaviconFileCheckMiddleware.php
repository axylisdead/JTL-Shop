<?php declare(strict_types=1);

namespace JTL\Router\Middleware;

use Exception;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;
use JTL\Media\Image;
use JTL\Shop;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class PhpFileCheckMiddleware
 * @package JTL\Router\Middleware
 */
class FaviconFileCheckMiddleware implements MiddlewareInterface
{
    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $file        = $request->getAttribute('file');
        $smarty      = Shop::Smarty();
        $templateDir = $smarty->getTemplateDir($smarty->context);
        $faviconPath = $templateDir . 'favicon/';

        // generate all supported favicons from image upload in template settings
        switch ($file) {
            case 'android-chrome-512x512.png':
                $this->generatePNG($file, 512, 512, $faviconPath);
                break;
            case 'android-chrome-192x192.png':
                $this->generatePNG($file, 192, 192, $faviconPath);
                break;
            case 'apple-touch-icon.png':
                $this->generatePNG($file, 180, 180, $faviconPath);
                break;
            case 'mstile-70x70.png':
                $this->generatePNG($file, 70, 70, $faviconPath);
                break;
            case 'mstile-144x144.png':
                $this->generatePNG($file, 144, 144, $faviconPath);
                break;
            case 'mstile-150x150.png':
                $this->generatePNG($file, 150, 150, $faviconPath);
                break;
            case 'mstile-310x150.png':
                $this->generatePNG($file, 310, 150, $faviconPath);
                break;
            case 'mstile-310x310.png':
                $this->generatePNG($file, 310, 310, $faviconPath);
                break;
            case 'site.webmanifest':
                $this->generateWebmanifest($file, $faviconPath);
                break;
            case 'browserconfig.xml':
                $this->generateBrowserconfig($file, $faviconPath);
                break;
            default:
                break;
        }

        return $handler->handle($request);
    }

    /**
     * @param string $fileName
     * @param int $width
     * @param int $height
     * @param string $targetPath
     * @return void
     * @throws Exception
     */
    private function generatePNG(string $fileName, int $width, int $height, string $targetPath):void
    {
        if (\is_file($targetPath . $fileName)) {
            return;
        }

        $sourcePath = $targetPath . 'favicon.ico';
        if (!\is_file($sourcePath)) {
            throw new Exception(\sprintf('Image "%s" does not exist', $sourcePath));
        }

        $thumbnail = $targetPath . $fileName;
        $manager   = new ImageManager(['driver' => Image::getImageDriver()]);
        $img       = $manager->make($sourcePath);
        $ext       = 'png';
        $canvas    = $manager->canvas($width, $height, 'rgba(255, 255, 255, 0)');
        if ($img->getWidth() > $width || $img->getHeight() > $height) {
            $img->resize($width, $height, static function (Constraint $constraint) {
                $constraint->aspectRatio();
            });
        }
        $canvas->insert($img, 'center');
        $img = $canvas;

        $img->save($thumbnail, 100, $ext);
    }

    /**
     * @param string $fileName
     * @param string $targetPath
     * @return void
     */
    private function generateWebmanifest(string $fileName, string $targetPath):void
    {
        if (\is_file($targetPath . $fileName)) {
            return;
        }

        $jsonData = '{
            "name": "",
            "short_name": "",
            "icons": [
                {
                    "src": "/android-chrome-192x192.png",
                    "sizes": "192x192",
                    "type": "image/png"
                },
                {
                    "src": "/android-chrome-512x512.png",
                    "sizes": "512x512",
                    "type": "image/png"
                }
            ],
            "theme_color": "#ffffff",
            "background_color": "#ffffff",
            "display": "standalone"
        }';

        $arrayData               = json_decode($jsonData, true);
        $conf                    = Shop::getSettings([\CONF_GLOBAL]);
        $arrayData['name']       = $conf['global']['global_shopname'];
        $arrayData['short_name'] = '';
        // theme color hinzufügen?

        $modifiedJsonData = json_encode($arrayData, JSON_PRETTY_PRINT);

        file_put_contents($targetPath . $fileName, $modifiedJsonData);
    }

    /**
     * @param string $fileName
     * @param string $targetPath
     * @return void
     */
    private function generateBrowserconfig(string $fileName, string $targetPath):void
    {
        if (\is_file($targetPath . $fileName)) {
            return;
        }

        $xmlData = '<?xml version="1.0" encoding="utf-8"?>
            <browserconfig>
                <msapplication>
                    <tile>
                        <square70x70logo src="/mstile-70x70.png"/>
                        <square150x150logo src="/mstile-150x150.png"/>
                        <square310x310logo src="/mstile-310x310.png"/>
                        <wide310x150logo src="/mstile-310x150.png"/>
                        <TileColor>#f8bf00</TileColor>
                    </tile>
                </msapplication>
            </browserconfig>';

        file_put_contents($targetPath . $fileName, $xmlData);
    }
}
