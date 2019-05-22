<?php
define('MODX_API_MODE', true);
define('IN_MANAGER_MODE', true);

include_once(__DIR__."/../../../index.php");
$modx->db->connect();
if (empty ($modx->config)) {
    $modx->getSettings();
}
if(!isset($_SESSION['mgrValidated'])){
    die();
}
$modx->invokeEvent('OnManagerPageInit',array('invokedBy'=>'TinyMCEUploader'));
include_once(MODX_BASE_PATH . 'assets/plugins/tmuploader/lib/controller.php');
$controller = new TinyMCEUploader\Controller($modx);
$out = json_encode($controller->upload());
echo ($out);
exit;
