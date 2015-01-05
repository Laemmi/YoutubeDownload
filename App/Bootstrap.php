<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) . '/../lib'),
    get_include_path(),
)));

require 'Laemmi/YoutubeDownload/Session.php';
require 'Laemmi/YoutubeDownload/Http/Client.php';
require 'Laemmi/YoutubeDownload/YoutubeDownload.php';