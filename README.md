# YoutubeDownload
This is a app with full frontend and backand to load videos from youtube.com to local drive.

You can use it also als library for your own projects.

# Requirements
php 5.6

# Installation
via composer

    composer require laemmi/youtube-download

or use repository

    git clone https://github.com/Laemmi/youtube-download.git
    
# Usage

    $service = Laemmi\YoutubeDownload\Service::factory(https://www.youtube.com/watch?v=Rd7YsqomnGI);
    $data = $service->getData();
    
You get different URL for mediatype and quality.