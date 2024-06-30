<?php declare(strict_types=1);

namespace JTL\Plugin\Admin\Installation\Items;

use JTL\Plugin\InstallCode;

/**
 * Class PortletInputTypes
 * @package JTL\Plugin\Admin\Installation\Items
 */
class PortletInputTypes extends AbstractItem
{
    /**
     * @inheritdoc
     */
    public function getNode(): array
    {
        return isset($this->baseNode['Install'][0]['PortletInputTypes'][0]['PortletInputType'])
        && \is_array($this->baseNode['Install'][0]['PortletInputTypes'][0]['PortletInputType'])
            ? $this->baseNode['Install'][0]['PortletInputTypes'][0]['PortletInputType']
            : [];
    }

    /**
     * @inheritdoc
     */
    public function install(): int
    {
        foreach ($this->getNode() as $i => $data) {
            $i = (string)$i;
            \preg_match('/\d+/', $i, $hits2);
            if (\mb_strlen($hits2[0]) !== \mb_strlen($i)) {
                continue;
            }
            $inputTypeObj = (object)[
                'name'      => $this->plugin->cPluginID . '.' . $data['Name'],
                'plugin_id' => $this->plugin->kPlugin,
            ];
            if (!$this->db->upsert('portlet_input_type', $inputTypeObj)) {
                return InstallCode::SQL_CANNOT_SAVE_INPUT_TYPE;
            }
        }

        return InstallCode::OK;
    }
}
