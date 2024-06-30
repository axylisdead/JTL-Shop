<?php declare(strict_types=1);

namespace JTL\dbeS\Push;

use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\dbeS\Mapper;
use JTL\Helpers\Text;
use JTL\XML;
use Psr\Log\LoggerInterface;
use ZipArchive;

/**
 * Class AbstractPush
 * @package JTL\dbeS\Push
 */
abstract class AbstractPush
{
    protected const XML_FILE = 'data.xml';

    protected const TEMP_DIR = \PFAD_ROOT . \PFAD_DBES . \PFAD_SYNC_TMP;

    /**
     * @var Mapper
     */
    protected Mapper $mapper;

    /**
     * Products constructor.
     * @param DbInterface       $db
     * @param JTLCacheInterface $cache
     * @param LoggerInterface   $logger
     */
    public function __construct(
        protected DbInterface $db,
        protected JTLCacheInterface $cache,
        protected LoggerInterface $logger
    ) {
        $this->mapper = new Mapper();
    }

    /**
     * @return array|string
     */
    abstract public function getData();

    /**
     * @param array|mixed $arr
     * @param array       $excludes
     * @return array
     */
    protected function buildAttributes(&$arr, array $excludes = []): array
    {
        $attributes = [];
        if (!\is_array($arr)) {
            return $attributes;
        }
        foreach (\array_keys($arr) as $key) {
            if (!\in_array($key, $excludes, true) && $key[0] === 'k') {
                $attributes[$key] = $arr[$key];
                unset($arr[$key]);
            }
        }

        return $attributes;
    }

    /**
     * @param string $zip
     * @param object|array $xml
     * @param string $wawiVersion
     */
    public function zipRedirect(string $zip, $xml, string $wawiVersion): void
    {
        $xmlfile       = \fopen(self::TEMP_DIR . self::XML_FILE, 'wb');
        $serializedXML = $wawiVersion === 'unknown'
            ? \strtr(Text::convertISO(XML::serialize($xml)), "\0", ' ')
            : XML::serialize($xml);
        \fwrite($xmlfile, $serializedXML);
        \fclose($xmlfile);
        if (\file_exists(self::TEMP_DIR . self::XML_FILE)) {
            $archive = new ZipArchive();
            if ($archive->open(self::TEMP_DIR . $zip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== false
                && $archive->addFile(self::TEMP_DIR . self::XML_FILE, self::XML_FILE)
            ) {
                $archive->close();
                \readfile(self::TEMP_DIR . $zip);
                exit;
            }
            $archive->close();
            \syncException($archive->getStatusString());
        }
    }
}
