<?php
require 'App/Bootstrap.php';

$session = new Laemmi_YoutubeDownload_Session();

if(!isset($session->data['meta']['img_preview'])) {
    exit('off');
}

header('Content-Type: image/jpeg');
readfile($session->data['meta']['img_preview']);