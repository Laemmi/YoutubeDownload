<?php
require 'App/Bootstrap.php';

$session = new Laemmi_YoutubeDownload_Session();

$key = isset($_GET['k'])?(int)$_GET['k']:null;

if(!isset($session->data['stream'][$key])) {
    exit('Wrong key');
}

$data = $session->data['stream'][$key];

header("Pragma: public", true);
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT", true);
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Content-Type: ".$data['content_type']);
header("Content-Disposition: attachment; filename=\"".$data['filename']."\";" );
header("Content-Length: ".$data['size']);

readfile($data['url']);