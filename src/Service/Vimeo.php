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

class Vimeo implements ServiceInterface
{
    const URL_INFO = 'https://player.vimeo.com/video/%s';

    private $HttpClient = null;

    private $id = '';

    public function __construct(ClientInterface $HttpClient)
    {
        $this->HttpClient = $HttpClient;
    }

    public function setId($value)
    {
        $urldata = parse_url($value);

        if(! isset($urldata['path'])) {
            throw new VimeoException('no id found', VimeoException::NO_ID_FOUND);
        }

        $info = pathinfo($urldata['path']);
        $this->id = $info['filename'];
    }

    public function getData()
    {
        $content = $this->HttpClient->getContent(sprintf(self::URL_INFO, $this->id));

        if (! preg_match('~var a=(\{.*?\});~m', $content, $match)) {
            throw new VimeoException('no content found', VimeoException::NO_CONTENT_FOUND);
        }

        $info = json_decode($match[1], true);

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

    private function getVideotype($value)
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
}