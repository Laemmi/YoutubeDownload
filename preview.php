<?php
require_once __DIR__ . '/vendor/autoload.php';

$session = new Laemmi\YoutubeDownload\Session();

if(!isset($session->data['meta']['img_preview'])) {
    exit('off');
}

header('Content-Type: image/jpeg');
readfile($session->data['meta']['img_preview']);