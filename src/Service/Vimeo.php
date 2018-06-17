<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @category   YoutubeDownload
 * @author     Michael Lämmlein <laemmi@spacerabbit.de>
 * @copyright  ©2017 laemmi
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.0
 * @since      05.05.17
 */

namespace Laemmi\YoutubeDownload\Service;

use Laemmi\YoutubeDownload\Data;
use Laemmi\YoutubeDownload\ServiceInterface;
use Laemmi\YoutubeDownload\Http\Client\ClientInterface;
use Laemmi\YoutubeDownload\ServiceOptionsInterface;


class Vimeo implements ServiceInterface
{
    const URL_PLAYER = 'https://player.vimeo.com/video/%s';
    const URL_VIDEO  = 'https://vimeo.com/%s';
    const URL_LOGIN  = 'https://vimeo.com/log_in';

    /**
     * @var ClientInterface|null
     */
    private $HttpClient = null;

    /**
     * @var string
     */
    private $id = '';

    /**
     * @var array
     */
    private $httpoptions = [];

    private $ServiceOptions = null;

    /**
     * Vimeo constructor.
     *
     * @param ClientInterface $HttpClient
     * @param ServiceOptionsInterface $options
     */
    public function __construct(ClientInterface $HttpClient, ServiceOptionsInterface $options)
    {
        $this->HttpClient     = $HttpClient;
        $this->ServiceOptions = $options;
    }

    /**
     * Set id
     *
     * @param $value
     * @throws VimeoException
     */
    public function setId($value)
    {
        $urldata = parse_url($value);

        if(! isset($urldata['path'])) {
            throw new VimeoException('no id found', VimeoException::NO_ID_FOUND);
        }

        $info = pathinfo($urldata['path']);
        $this->id = $info['filename'];
    }

    /**
     * Get data
     *
     * @return Data
     * @throws VimeoException
     */
    public function getData() : Data
    {
        if ($this->ServiceOptions->doAuthenticate()) {
            $authenticatecredentials = $this->ServiceOptions->getAuthenticateCredentials();
            $this->authenticate($authenticatecredentials['email'], $authenticatecredentials['password']);
        }

        // Find video.clip_page_config to get config_url
        $content = $this->HttpClient->getContent(sprintf(self::URL_VIDEO, $this->id), $this->httpoptions);
        if (preg_match('~vimeo\.clip_page_config = (\{.*?\});~m', $content, $match)) {
            $info = json_decode($match[1], true);
            $content = $this->HttpClient->getContent($info['player']['config_url']);
            $info = json_decode($content, true);
            return $this->addDateFromConfigUrl($info);
        }

        // Alternative find config data on player page
        $content = $this->HttpClient->getContent(sprintf(self::URL_PLAYER, $this->id), $this->httpoptions);
        if (preg_match('~var t=(\{.*?\});~m', $content, $match)) {
            $info = json_decode($match[1], true);
            return $this->addDateFromConfigUrl($info);
        }

        // Alternative load config if user allows download video
        $content = $this->HttpClient->getContent(sprintf(self::URL_VIDEO . '?action=load_download_config', $this->id), [
            CURLOPT_HTTPHEADER => [
                'X-Requested-With: XMLHttpRequest'
            ]
        ] + $this->httpoptions);
        $info = json_decode($content, true);
        if (isset($info['files'])) {
            $data = new Data();
            $data->setTitle('');
            $data->setPreviewUrl('');

            foreach($info['files'] as $key => $val) {
                $size = $this->HttpClient->getHeaderContentLength($val['download_url']);
                $stream = new Data\Stream();
                $stream->setUrl($val['download_url']);
                $stream->setContentType($val['extension']);
                $stream->setSize($size);
                $stream->setFileExtension($val['extension']);
                $stream->setFilename($val['download_name']);
                $stream->setFormat($val['public_name']);
                $stream->setQuality($val['public_name']);

                $data->append($stream);
            }
            return $data;
        }

        throw new VimeoException('no content found', VimeoException::NO_CONTENT_FOUND);
    }

    /**
     * Get videotype
     *
     * @param $value
     * @return array
     */
    private function getVideotype($value) : array
    {
        $type = array(
            'video/webm' => array(
                'extension' => '.webm',
                'name' => 'WebM'
            ),
            'video/3gpp' => array(
                'extension' => '.3gp',
                'name' => '3GPP'
            ),
            'video/x-flv' => array(
                'extension' => '.flv',
                'name' => 'FLV'
            ),
            'video/mp4' => array(
                'extension' => '.mp4',
                'name' => 'MPEG4'
            )
        );

        return isset($type[$value]) ? $type[$value] : [];
    }

    /**
     * Append data from give array
     *
     * @param array $info
     * @return Data
     */
    private function addDateFromConfigUrl(array $info) : Data
    {
        $data = new Data();
        $data->setTitle($info['video']['title']);
        $data->setPreviewUrl($info['video']['thumbs']['640']);
        foreach($info['request']['files']['progressive'] as $key => $val) {
            $size = $this->HttpClient->getHeaderContentLength($val['url']);
            $videotype = $this->getVideotype($val['mime']);

            $stream = new Data\Stream();
            $stream->setUrl($val['url']);
            $stream->setContentType($val['mime']);
            $stream->setSize($size);
            $stream->setFileExtension($videotype['extension']);
            $stream->setFilename($info['video']['title'].$videotype['extension']);
            $stream->setFormat($videotype['name']);
            $stream->setQuality($val['quality']);

            $data->append($stream);
        }
        return $data;
    }

    /**
     * Authenticate to vimeo
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    private function authenticate(string $email, string $password)
    {
        $response = $this->HttpClient->getContent(self::URL_LOGIN);
        if (! preg_match("/.xsrft.:\"(.*)\",/i", $response, $match)) {
            return false;
        }

        $token = $match[1];

        if (! preg_match("/[\"\']vuid[\"\']\:[\"\'](.*?)[\"\']/i", $response, $match)) {
            return false;
        }

        $vuid = $match[1];

        $cookie     = 'vuid=' . $vuid;
        $cookiefile = '/tmp/LaemmiYouTubeDownloadVimeoCookie.txt';
        @unlink($cookiefile);

        $headers = array(
            "Accept: application/json",
            "Accept-Language: en-US,en;q=0.5",
            "X-Requested-With: XMLHttpRequest",
            "X-Request: JSON",
            "Content-Type: application/x-www-form-urlencoded; charset=utf-8",
        );

        $postfields = [
            'email'    => $email,
            'password' => $password,
            'token'    => $token,
            'action'   => 'login',
            'service'  => 'vimeo',
        ];

        $this->httpoptions = [
            CURLOPT_COOKIESESSION => true,
            CURLOPT_COOKIEJAR     => $cookiefile,
            CURLOPT_COOKIEFILE    => $cookiefile,
            CURLOPT_COOKIE        => $cookie,
        ];

        $response = $this->HttpClient->getContent(self::URL_LOGIN, [
            CURLOPT_HTTPHEADER    => $headers,
            CURLOPT_TIMEOUT       => 3600,
            CURLOPT_POST          => true,
            CURLOPT_POSTFIELDS    => http_build_query($postfields),
        ] + $this->httpoptions);
    }
}