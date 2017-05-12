[![Build Status](https://travis-ci.org/Laemmi/youtube-download.svg?branch=master)](https://travis-ci.org/Laemmi/youtube-download)
[![Latest Stable Version](https://poser.pugx.org/laemmi/youtube-download/v/stable)](https://packagist.org/packages/laemmi/youtube-download)
[![Total Downloads](https://poser.pugx.org/laemmi/youtube-download/downloads)](https://packagist.org/packages/laemmi/youtube-download)
[![Latest Unstable Version](https://poser.pugx.org/laemmi/youtube-download/v/unstable)](https://packagist.org/packages/laemmi/youtube-download)
[![License](https://poser.pugx.org/laemmi/youtube-download/license)](https://packagist.org/packages/laemmi/youtube-download)

# YoutubeDownload
This is a app with full frontend and backand to load videos from youtube.com or vimeo.com to local drive.

You can use it also als library for your own projects.

# Requirements
php 5.5

# Installation
via composer

    composer require laemmi/youtube-download

or use repository

    git clone https://github.com/Laemmi/youtube-download.git
    
# Usage

    $service = Laemmi\YoutubeDownload\Service::factory(https://www.youtube.com/watch?v=Rd7YsqomnGI);
    $data = $service->getData();
    
You get different URL for mediatype and quality.