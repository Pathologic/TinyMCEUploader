<?php namespace TinyMCEUploader;

include_once(MODX_BASE_PATH . 'assets/plugins/tmuploader/lib/model.php');
include_once(MODX_BASE_PATH . 'assets/lib/Helpers/FS.php');

class Controller
{
    protected $modx = null;
    protected $data = null;
    public $uploadDir = 'assets/images/content/';

    public function __construct (\DocumentParser $modx)
    {
        $this->modx = $modx;
        $this->data = new Model($this->modx);
        $this->fs = \Helpers\FS::getInstance();
    }

    public function upload ()
    {
        $out = array('success' => false, 'message' => 'Не удалось загрузить файл.');
        $dir = $this->uploadDir;
        $allowedFiles = array('png', 'gif', 'jpg', 'jpeg');
        if (!empty($_FILES['file']) && !$_FILES['file']['error'] && is_uploaded_file($_FILES['file']['tmp_name'])) {
            $name = $_FILES['file']['name'];
            $ext = $this->fs->takeFileExt($name);
            if (!in_array(strtolower($ext), $allowedFiles)) {
                $out['message'] = 'Запрещено загружать такие файлы.';
            } else {
                $dir = $dir . date('Y-m') . '/' . date('d') . '/';
                if ($this->fs->makeDir($dir)) {
                    $name = $this->data->stripName($name);
                    $name = $this->fs->getInexistantFilename($dir . $name);
                    if (@move_uploaded_file($_FILES['file']['tmp_name'],
                            MODX_BASE_PATH . $dir . $name) && $this->data->upload($dir . $name)) {
                        $out = array(
                            'success' => true,
                            'item'    => array(
                                'id'   => $this->data->getID(),
                                'file' => $dir . $name
                            )
                        );
                    }
                }
            }
        }

        return $out;
    }
}
