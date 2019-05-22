<?php
if (IN_MANAGER_MODE != 'true') {
    die();
}
$event = $modx->event->name;
include_once (MODX_BASE_PATH . 'assets/plugins/tmuploader/lib/plugin.php');
$plugin = new \TinyMCEUploader\Plugin($modx);
if (method_exists($plugin, $event)) {
    call_user_func(array($plugin, $event));
}
