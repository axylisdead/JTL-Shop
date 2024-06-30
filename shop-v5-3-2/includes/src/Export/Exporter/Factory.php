<?php declare(strict_types=1);

namespace JTL\Export\Exporter;

use Exception;
use InvalidArgumentException;
use JTL\Cache\JTLCacheInterface;
use JTL\DB\DbInterface;
use JTL\Export\ExportWriterInterface;
use JTL\Export\Model;
use Psr\Log\LoggerInterface;

/**
 * Class ExporterFactory
 * @package JTL\Export\Exporter
 */
class Factory
{
    /**
     * @param DbInterface                $db
     * @param LoggerInterface            $logger
     * @param JTLCacheInterface          $cache
     * @param ExportWriterInterface|null $writer
     */
    public function __construct(
        private readonly DbInterface            $db,
        private readonly LoggerInterface        $logger,
        private readonly JTLCacheInterface      $cache,
        private readonly ?ExportWriterInterface $writer = null
    ) {
    }

    public function getExporter(
        int $exportID,
        bool $isAsync = false,
        bool $isCron = false
    ): ExporterInterface {
        try {
            $model = Model::load(['id' => $exportID], $this->db, Model::ON_NOTEXISTS_FAIL);
        } catch (Exception) {
            throw new InvalidArgumentException('Cannot find export with id ' . $exportID);
        }
        if ($model->getPluginID() > 0) {
            $exporter = new PluginExporter($this->db, $this->logger, $this->cache, $this->writer);
        } elseif ($isAsync) {
            $exporter = new AsyncExporter($this->db, $this->logger, $this->cache, $this->writer);
        } elseif ($isCron) {
            $exporter = new CronExporter($this->db, $this->logger, $this->cache, $this->writer);
        } else {
            $exporter = new SyncExporter($this->db, $this->logger, $this->cache, $this->writer);
        }
        $exporter->initialize($exportID, $model, $isAsync, $isCron);
        \executeHook(\HOOK_EXPORT_FACTORY_GET_EXPORTER, [
            'exportID' => $exportID,
            'exporter' => &$exporter,
            'model'    => $model
        ]);

        return $exporter;
    }
}
