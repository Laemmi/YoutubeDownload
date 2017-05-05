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
 * @package     Laemmi_YoutubeDownload
 * @subpackage  Http_Client_Adapter
 * @author      Michael Lämmlein <ml@spacerabbit.de>
 * @copyright   2014 Michael Lämmlein <ml@spacerabbit.de>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version     0.0.1
 * @link        https://github.com/Laemmi/YoutubeDownload
 * @since       20.11.2014
 */

/**
 * Class Laemmi_YoutubeDownload_Http_Client_Adapter_Curl
 *
 * @category    Laemmi
 * @package     Laemmi_YoutubeDownload
 * @subpackage  Http_Client_Adapter
 * @author      Michael Lämmlein <ml@spacerabbit.de>
 * @copyright   2014 Michael Lämmlein <ml@spacerabbit.de>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version     0.0.1
 * @link        https://github.com/Laemmi/YoutubeDownload
 * @since       20.11.2014
 */
class Laemmi_YoutubeDownload_Http_Client_Adapter_Curl implements Laemmi_YoutubeDownload_Http_Client_Interface
{
    public function saveFile($url, $local)
    {
        $fp = fopen($local, 'w');
        $ch = $this->curInit($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    public function getContent($url)
    {
        $ch = $this->curInit($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $contents = curl_exec($ch);
        curl_close($ch);

        return $contents;
    }

    public function getHeaderContentLength($url)
    {
        $ch = $this->curInit($url);
        curl_setopt($ch, CURLOPT_HEADER,         true);
        curl_setopt($ch, CURLOPT_NOBODY,         true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $r = curl_exec($ch);

        if(preg_match_all("/Content\-Length\:(.*)?\n/", $r, $match)) {
            return trim(array_pop($match[1]));
        }

        return '';
    }

    protected function curInit($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko Firefox/11.0");
        curl_setopt($ch, CURLOPT_REFERER, "http://www.youtube.com/");

        return $ch;
    }
}