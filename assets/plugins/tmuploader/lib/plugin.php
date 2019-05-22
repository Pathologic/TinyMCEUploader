<?php namespace TinyMCEUploader;
include_once (MODX_BASE_PATH . 'assets/lib/Helpers/Assets.php');
include_once (MODX_BASE_PATH . 'assets/plugins/tmuploader/lib/model.php');
class Plugin {
    protected $modx = null;
    protected $data = null;
    protected $jsList = 'assets/plugins/tmuploader/js/scripts.json';
    public function __construct (\DocumentParser $modx)
    {
        $this->modx = $modx;
        $this->data = new Model($modx);
    }

    public function OnDocFormRender() {
        $out = '';
        $this->data->createTable();
        $lifetime = !empty($this->modx->event->params['lifetime']) ?  $this->modx->event->params['lifetime'] : 24;
        $this->data->deleteLost($lifetime, true);
        $assets = \AssetsHelper::getInstance($this->modx);
        $fs = \Helpers\FS::getInstance();
        if ($fs->checkFile($this->jsList)) {
            $scripts = @file_get_contents(MODX_BASE_PATH . $this->jsList);
            $scripts = json_decode($scripts, true);
            $out = $assets->registerScriptsList($scripts['scripts']);
        }
        $this->modx->event->addOutput($out);
    }

    public function OnDocFormSave() {
        if (empty($this->modx->event->params['id']) || !$this->modx->event->params['id']) return;
        $id = $this->modx->event->params['id'];
        $table = $this->modx->getFullTableName($this->data->tableName());
        $this->modx->db->query("UPDATE {$table} SET `rid`=0 WHERE `rid`={$id}");
        $q = $this->modx->db->query("SELECT `content` FROM {$this->modx->getFullTableName('site_content')} WHERE `id`={$id}");
        $content = $this->modx->db->getValue($q);
        preg_match_all('/\< *[img][^\>]*[data-tmu] *= *[\"\']{0,1}([^\"\']*)/i', $content, $matches);
        $ids = array_unique(array_pop($matches));
        if ($ids) {
            foreach ($ids as &$_id) {
                $_id = (int)$_id;
            }
            unset($_id);
            $ids = implode(',', $ids);
            $this->modx->db->query("UPDATE {$table} SET `rid`={$id} WHERE `id` IN ({$ids})");
        }
    }

    public function OnEmptyTrash() {
        if (empty($ids)) return;
        $this->data->deleteByRid($ids, true);
    }
}

