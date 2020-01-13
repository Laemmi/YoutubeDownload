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
 * @copyright  ©2020 laemmi
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0.3
 * @since      13.01.2020
 */

namespace Laemmi\YoutubeDownload\Tests\Service;

use Laemmi\YoutubeDownload\Service\Youtube;
use PHPUnit_Framework_TestCase;

class YoutubeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $url
     * @param $expected
     * @throws \Laemmi\YoutubeDownload\Exception
     * @throws \Laemmi\YoutubeDownload\Service\YoutubeException
     *
     * @dataProvider providerDefaultValues
     */
    public function testGetData(string $url, array $expected)
    {
        $options = new \Laemmi\YoutubeDownload\Http\Client\Options();
        $options->referer = 'https://www.youtube.com';
        $client = new \Laemmi\YoutubeDownload\Http\Client\Adapter\Curl($options);
        $service_options = new \Laemmi\YoutubeDownload\Service\DefaultOptions();


        $service = new Youtube($client, $service_options);
        $service->setId($url);
        $data = $service->getData();
        $stream = $data->offsetGet(0);

        $actual = [
            'content_type' => $stream->getContentType(),
            'size'         => $stream->getSize(),
            'filename'     => $stream->getFilename()
        ];

        $this->assertSame($expected, $actual);
    }

    public function providerDefaultValues(): array
    {
        return [
            ['https://www.youtube.com/watch?v=rLuo_XqkFmY', ['content_type' => 'video/mp4', 'size' => '0', 'filename' => 'Erste Hilfe: Wiederbelebung.mp4']],
            ['https://www.youtube.com/watch?v=T6WDCCvwXYI', ['content_type' => 'video/mp4', 'size' => '4785236', 'filename' => 'DAS KRONTHALER****S.mp4']],
        ];
    }
}