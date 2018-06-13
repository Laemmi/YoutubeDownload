<?php
/**
 * Copyright (c) 2014 Michael Lämmlein <ml@spacerabbit.de>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category    Laemmi
 * @package     Laemmi\YoutubeDownload
 * @subpackage  Http_Client_Adapter
 * @author      Michael Lämmlein <ml@spacerabbit.de>
 * @copyright   2014 Michael Lämmlein <ml@spacerabbit.de>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version     0.0.1
 * @link        https://github.com/laemmi/youtube-download
 * @since       20.11.2014
 */

namespace Laemmi\YoutubeDownload\Http\Client\Adapter;

use Laemmi\YoutubeDownload\Http\Client\ClientInterface;
use Laemmi\YoutubeDownload\Http\Client\Options;

/**
 * Class Curl
 *
 * @category    Laemmi
 * @package     Laemmi\YoutubeDownload
 * @author      Michael Lämmlein <ml@spacerabbit.de>
 * @copyright   2014 Michael Lämmlein <ml@spacerabbit.de>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version     0.0.1
 * @link        https://github.com/laemmi/youtube-download
 * @since       20.11.2014
 */
class Curl implements ClientInterface
{
    private $options = [];

    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    public function saveFile($url, $local)
    {
        $fp = fopen($local, 'w');
        $response = $this->request($url, [
            CURLOPT_FILE => $fp
        ]);
        fclose($fp);
    }

    public function getContent($url)
    {
        return $this->request($url);
    }

    public function getHeaderContentLength($url)
    {
        $response = $this->request($url, [
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_FOLLOWLOCATION => true,
        ]);

        if (preg_match_all("/Content\-Length\:(.*)?\n/", $response, $match)) {
            return trim(array_pop($match[1]));
        }

        return '';
    }

    private function request($url, array $options = [])
    {
        $defaults = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko Firefox/11.0',
            CURLOPT_REFERER        => $this->options->referer
        ];

        $options = $options + $defaults;

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
