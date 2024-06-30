<?php declare(strict_types=1);

namespace JTL\Router\Controller\Backend;

use Exception;
use JTL\Backend\Permissions;
use JTL\Helpers\Form;
use JTL\Helpers\Request;
use JTL\Helpers\Text;
use JTL\Media\Image;
use JTL\Media\IMedia;
use JTL\Media\Media;
use JTL\Smarty\JTLSmarty;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;

/**
 * Class BrandingController
 * @package JTL\Router\Controller\Backend
 */
class BrandingController extends AbstractBackendController
{
    /**
     * @inheritdoc
     */
    public function getResponse(ServerRequestInterface $request, array $args, JTLSmarty $smarty): ResponseInterface
    {
        $this->smarty = $smarty;
        $this->checkPermissions(Permissions::DISPLAY_BRANDING_VIEW);
        $this->getText->loadAdminLocale('pages/branding');

        $currentID   = (int)($args['id'] ?? 1);
        $this->route = \str_replace('[/{id}]', '', $this->route);
        $step        = 'branding_uebersicht';
        if (Request::verifyGPDataString('action') === 'delete' && Form::validateToken()) {
            $id = Request::postInt('id');
            $this->deleteImage($id);
            die(\json_encode((object)['id' => $id, 'status' => 'OK'], \JSON_THROW_ON_ERROR));
        }
        if (Request::verifyGPCDataInt('branding') === 1) {
            $step = 'branding_detail';
            if (Request::postInt('speicher_einstellung') === 1 && Form::validateToken()) {
                if ($this->saveConfig(Request::verifyGPCDataInt('kBranding'), $_POST, $_FILES)) {
                    $this->alertService->addSuccess(\__('successConfigSave'), 'successConfigSave');
                } else {
                    $this->alertService->addError(\__('errorFillRequired'), 'errorFillRequired');
                }
            }
            if (Request::verifyGPCDataInt('kBranding') > 0) {
                $currentID = Request::verifyGPCDataInt('kBranding');
            }
        }

        return $smarty->assign('cRnd', \time())
            ->assign('branding', $this->getBranding($currentID))
            ->assign('brandings', $this->getBrandings())
            ->assign('step', $step)
            ->assign('route', $this->route)
            ->getResponse('branding.tpl');
    }

    /**
     * @return stdClass[]
     * @former gibBrandings()
     */
    private function getBrandings(): array
    {
        return $this->db->getCollection('SELECT * FROM tbranding ORDER BY cBildKategorie')
            ->map(static function (stdClass $item): stdClass {
                $item->kBranding = (int)$item->kBranding;

                return $item;
            })
            ->toArray();
    }

    /**
     * @param int $brandingID
     * @return stdClass|null
     * @former gibBranding()
     */
    private function getBranding(int $brandingID): ?stdClass
    {
        $item = $this->db->getSingleObject(
            'SELECT tbranding.*, tbranding.kBranding AS kBrandingTMP, tbrandingeinstellung.*
                FROM tbranding
                LEFT JOIN tbrandingeinstellung 
                    ON tbrandingeinstellung.kBranding = tbranding.kBranding
                WHERE tbranding.kBranding = :bid
                GROUP BY tbranding.kBranding',
            ['bid' => $brandingID]
        );
        if ($item === null) {
            return null;
        }

        $item->kBrandingEinstellung = (int)$item->kBrandingEinstellung;
        $item->kBranding            = (int)$item->kBranding;
        $item->kBrandingTMP         = (int)$item->kBrandingTMP;
        $item->nAktiv               = (int)$item->nAktiv;
        $item->dTransparenz         = (int)$item->dTransparenz;
        $item->dGroesse             = (int)$item->dGroesse;
        $item->dRandabstand         = (int)$item->dRandabstand;

        return $item;
    }

    /**
     * @param int   $brandingID
     * @param array $post
     * @param array $files
     * @return bool
     * @former speicherEinstellung()
     */
    private function saveConfig(int $brandingID, array $post, array $files): bool
    {
        $hasNewImage = \mb_strlen($files['cBrandingBild']['name'] ?? '') > 0;
        if ($hasNewImage && !Image::isImageUpload($files['cBrandingBild'])) {
            return false;
        }
        $conf                = $this->db->select('tbrandingeinstellung', 'kBranding', $brandingID) ?? new stdClass();
        $conf->dRandabstand  = 0;
        $conf->kBranding     = $brandingID;
        $conf->cPosition     = $post['cPosition'];
        $conf->nAktiv        = $post['nAktiv'];
        $conf->dTransparenz  = $post['dTransparenz'];
        $conf->dGroesse      = $post['dGroesse'];
        $conf->imagesizes    = Text::createSSK($post['imagesizes'] ?? []);
        $conf->cBrandingBild = $conf->cBrandingBild ?? '';
        if ($hasNewImage) {
            $conf->cBrandingBild = 'kBranding_' . $brandingID . $this->mapFileType($files['cBrandingBild']['type']);
        }
        if ($conf->kBranding > 0 && \mb_strlen($conf->cPosition) > 0 && \mb_strlen($conf->cBrandingBild) > 0) {
            if ($hasNewImage) {
                $this->deleteImage($conf->kBranding);
                try {
                    $conf->cBrandingBild = $this->saveImage($files, $conf->kBranding);
                } catch (Exception $e) {
                    $this->alertService->addWarning($e->getMessage(), 'brandingImageError');

                    return false;
                }
            }
            $this->db->upsert('tbrandingeinstellung', $conf);
            $data = $this->db->select('tbranding', 'kBranding', $conf->kBranding);
            $type = Media::getClass($data->cBildKategorie ?? '');
            /** @var IMedia $type */
            $type::clearCache();
            $this->cache->flushTags([\CACHING_GROUP_OPTION]);

            return true;
        }

        return false;
    }

    /**
     * @param array $files
     * @param int   $brandingID
     * @return string
     * @former speicherBrandingBild()
     * @throws Exception
     */
    private function saveImage(array $files, int $brandingID): string
    {
        $upload = $files['cBrandingBild'];
        if (!Image::isImageUpload($upload)) {
            throw new Exception(\__('Not a valid image file.'));
        }
        $fileName = \uniqid('kBranding_' . $brandingID . '_', false)
            . $this->mapFileType($upload['type']);
        $newFile  = \PFAD_ROOT . \PFAD_BRANDINGBILDER . $fileName;
        if (\move_uploaded_file($upload['tmp_name'], $newFile) === false) {
            throw new Exception(\sprintf(\__('Could not move uploaded file to %s.'), $newFile));
        }

        return $fileName;
    }

    /**
     * @param int $brandingID
     * @former loescheBrandingBild()
     */
    private function deleteImage(int $brandingID): void
    {
        $filename = $this->db->select('tbrandingeinstellung', 'kBranding', $brandingID)->cBrandingBild ?? null;
        if ($filename === null) {
            return;
        }
        $path = \PFAD_ROOT . \PFAD_BRANDINGBILDER . $filename;
        if (\file_exists($path)) {
            @\unlink($path);
        }
    }

    /**
     * @param string $ype
     * @return string
     * @former mappeFileTyp()
     */
    private function mapFileType(string $ype): string
    {
        return match ($ype) {
            'image/gif' => '.gif',
            'image/png' => '.png',
            'image/bmp' => '.bmp',
            default     => '.jpg',
        };
    }
}
