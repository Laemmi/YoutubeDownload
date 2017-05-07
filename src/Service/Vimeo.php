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

use Laemmi\YoutubeDownload\ServiceInterface;
use Laemmi\YoutubeDownload\Exception;
use Laemmi\YoutubeDownload\Http\Client\ClientInterface;

class Vimeo implements ServiceInterface
{

    private $HttpClient = null;

    private $id = '';

    public function __construct(ClientInterface $HttpClient)
    {
        $this->HttpClient = $HttpClient;
    }

    public function setId($value)
    {
        $urldata = parse_url($value);


        $id = isset($urldata['path']) ? $urldata['path'] : '';

        echo "<pre>";
        print_r($urldata);
        echo "</pre>";

        $id = '39995350';

        $base = 'https://player.vimeo.com/play_redirect';

        $content = $this->HttpClient->getContent(sprintf('https://player.vimeo.com/video/%s', $id));

//        preg_match('/g:(\{.*?\}),a/s', $content, $match);
        preg_match('~var t=(\{.*?\};)~m', $content, $match);

        $pattern = '/\{(?:[^{}]|(?R))*\}/x';
        preg_match_all($pattern, $content, $match);

        echo "<pre>";
        print_r($match);
        echo "</pre>";

//        $json = json_decode($match[1]);
//
//        echo "<pre>";
//        print_r($json);
//        echo "</pre>";
    }

    public function getData()
    {

    }

    /**
     * Format bytes
     *
     * @param $bytes
     * @param int $precision
     * @return string
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}