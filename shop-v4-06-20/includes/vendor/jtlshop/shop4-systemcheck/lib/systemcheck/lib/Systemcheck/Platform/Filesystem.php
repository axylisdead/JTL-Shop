<?php
/**
 * @package jtl\Systemcheck\Shop4
 * @copyright JTL-Software-GmbH
 */

/**
 * Systemcheck_Platform_Filesystem
 */
class Systemcheck_Platform_Filesystem
{
    /**
     * root-path of this shop - optional
     *
     * @var string
     */
    protected $szRootPath = '';

    /**
     * array of all shop-folders (no state-info!)
     *
     * @var array|null
     */
    protected $vShopFolders;

    /**
     * array of strings (folder-names) with their states
     *
     * @var null|array
     */
    protected $vShopFoldersChecked;

    /**
     * result of the folder-check
     *
     * @var bool
     */
    protected $bIsPassed = true;

    /**
     * @var array
     */
    protected $vWritableEntities = array(
        // Folders
          'bilder/intern/trustedshops'
        , 'bilder/news'
        , 'bilder/intern/shoplogo'
        , 'mediafiles/Bilder'
        , 'mediafiles/Musik'
        , 'mediafiles/Sonstiges'
        , 'mediafiles/Videos'
        , 'bilder/banner'
        , 'bilder/produkte/mini'
        , 'bilder/produkte/klein'
        , 'bilder/produkte/normal'
        , 'bilder/produkte/gross'
        , 'bilder/kategorien'
        , 'bilder/variationen/mini'
        , 'bilder/variationen/normal'
        , 'bilder/variationen/gross'
        , 'bilder/hersteller/normal'
        , 'bilder/hersteller/klein'
        , 'bilder/merkmale/normal'
        , 'bilder/merkmale/klein'
        , 'bilder/merkmalwerte/normal'
        , 'bilder/merkmalwerte/klein'
        , 'bilder/brandingbilder'
        , 'bilder/suchspecialoverlay/klein'
        , 'bilder/suchspecialoverlay/normal'
        , 'bilder/suchspecialoverlay/gross'
        , 'bilder/konfigurator/klein'
        , 'bilder/links'
        , 'bilder/newsletter'
        , 'jtllogs'
        , 'export'
        , 'export/backup'
        , 'export/yatego'
        , 'templates_c'
        , 'dbeS/tmp'
        , 'dbeS/logs'
        , 'uploads'
        , 'media/image'
        , 'media/image/storage'
        , 'media/image/product'
        , 'admin/templates_c'
        , 'admin/includes/emailpdfs'
        // Files
        , 'includes/config.JTL-Shop.ini.php'
        //, 'rss.xml'
        //, 'shopinfo.xml'
    );

    /**
     * Constructor of the file-check-object
     *
     * @param string $rootPath root-path of this shop-application
     */
    public function __construct($rootPath)
    {
        $this->szRootPath = $rootPath;
        $this->vShopFolders = $this->collectWritableEntities();
    }

    /**
     * HELPER to get the paths we want
     * (this should prevent functionality in a "difines"-config-file)
     *
     * @return array string-array of to-writable paths
     */
    private function collectWritableEntities()
    {
        return array_map(function ($v) {
            if (strpos($v, PFAD_ROOT) === 0) {
                $v = substr($v, strlen(PFAD_ROOT));
            }

            return trim($v, '/\\');
        }, $this->vWritableEntities);
    }

    /**
     * HELPER to get all shop-folders as array, only for display-purposes
     *
     * @return array with folder-names (unchecked)
     */
    public function getFolders()
    {
        return $this->vShopFolders;
    }

    /**
     * Check the folders (one times), given in "includes/defines.php"
     * and store the results in this object for later usage
     * (refactored and moved from "install_inc::gibBeschreibbareVerzeichnisseAssoc()")
     *
     * @return array  hash of shop-writable-folder, where value represents the state
     *                (1=writable, ''=not writable)
     */
    public function getFoldersChecked()
    {
        if (null === $this->vShopFoldersChecked) {
            if (empty($this->szRootPath)) {
                return array();
            }

            $vFsEntities_resulting = array();
            $vFsEntities_current   = $this->vShopFolders;
            if (is_array($vFsEntities_current) && count($vFsEntities_current) > 0) {
                foreach ($vFsEntities_current as $szFsEntity) {
                    $vFsEntities_resulting[$szFsEntity] = false;

                    if (is_writable(PFAD_ROOT . $szFsEntity)) {
                        // if entity (implicitly exists and) is writable
                        $vFsEntities_resulting[$szFsEntity] = true;
                    } elseif (!is_file(PFAD_ROOT . $szFsEntity)) {
                        // if entity is not a file (implicitly not exists) try to write/create, and return the result
                        $bIsWriable                         = (@file_put_contents(PFAD_ROOT . $szFsEntity, ' ') === 1);
                        $vFsEntities_resulting[$szFsEntity] = $bIsWriable;
                        // cleanup, if anything was written
                        if (true === $bIsWriable) {
                            unlink(PFAD_ROOT . $szFsEntity);
                        }
                    }
                }
            }
            $this->vShopFoldersChecked = $vFsEntities_resulting;
            return $vFsEntities_resulting;
        }

        return $this->vShopFoldersChecked;
    }


    /**
     * return a summary-result of this test, depending on the checked-folders-array.
     * if at least one folder is not writable, the test is failed
     *
     * @return bool (member-var) summary-test-result
     *              (true=ok, false=failed)
     */
    public function getIsPassed()
    {
        $vCheckedFolders = (null === $this->vShopFoldersChecked)
            ? $this->getFoldersChecked()
            : $this->vShopFoldersChecked;

        foreach (array_keys($vCheckedFolders) as $key) {
            (!(bool)$vCheckedFolders[$key])
                ? $this->bIsPassed = false
                : null;
        }
        return $this->bIsPassed;
    }


    /**
     * calculates a statistical number about the folders which need to be writable,
     * to show that in the "admin/permissioncheck"
     * (refactored and moved from permissioncheck_inc.php)
     *
     * @return stdClass contains the summery of folders/files and a value of 'invalids'
     */
    public function getFolderStats()
    {
        $oStat                = new stdClass();
        $oStat->nCount        = 0;
        $oStat->nCountInValid = 0;

        if (is_array($this->vShopFoldersChecked) && count($this->vShopFoldersChecked) > 0) {
            foreach ($this->vShopFoldersChecked as $cDir => $isValid) {
                $oStat->nCount++;
                if (!$isValid) {
                    $oStat->nCountInValid++;
                }
            }
        }

        return $oStat;
    }
}
