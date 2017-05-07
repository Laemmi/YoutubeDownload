<?php
require_once __DIR__ . '/vendor/autoload.php';

$session = new Laemmi\YoutubeDownload\Session();

$key = isset($_GET['k'])?(int)$_GET['k']:null;

if(! $session->data->offsetExists($key)) {
    exit('Wrong key');
}

$stream = $session->data->offsetGet($key);

header("Pragma: public", true);
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT", true);
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Content-Type: " . $stream->getContentType());
header("Content-Disposition: attachment; filename=\"" . $stream->getFilename() . "\";" );
header("Content-Length: " . $stream->getSize());

readfile($stream->getUrl());