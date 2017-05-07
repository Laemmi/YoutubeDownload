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
 * @since      07.05.17
 */

namespace Laemmi\YoutubeDownload\Data;

class Stream
{
    /**
     * @var string
     */
    private $url = '';

    /**
     * @var string
     */
    private $content_type = '';

    /**
     * @var int
     */
    private $size = 0;

    /**
     * @var string
     */
    private $fileextension = '';

    /**
     * @var string
     */
    private $filename = '';

    /**
     * @var string
     */
    private $format = '';

    /**
     * @var string
     */
    private $quality = 'normal';

    /**
     * @param $value
     */
    public function setUrl($value)
    {
        $this->url = $value;
    }

    /**
     * @param $value
     */
    public function setContentType($value)
    {
        $this->content_type = $value;
    }

    /**
     * @param $value
     */
    public function setSize($value)
    {
        $this->size = $value;
    }

    /**
     * @param $value
     */
    public function setFileExtension($value)
    {
        $this->fileextension = $value;
    }

    /**
     * @param $value
     */
    public function setFilename($value)
    {
        $this->filename = $value;
    }

    /**
     * @param $value
     */
    public function setFormat($value)
    {
        $this->format = $value;
    }

    /**
     * @param $value
     */
    public function setQuality($value)
    {
        $this->quality = $value;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileextension;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return string
     */
    public function getQuality()
    {
        return $this->quality;
    }
}
