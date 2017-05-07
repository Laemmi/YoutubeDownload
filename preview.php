<?php
require_once __DIR__ . '/vendor/autoload.php';

$session = new Laemmi\YoutubeDownload\Session();

if(! $session->data->getPreviewUrl()) {
    exit('off');
}

header('Content-Type: image/jpeg');
readfile($session->data->getPreviewUrl());