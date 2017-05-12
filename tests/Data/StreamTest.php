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
 * @link       https://github.com/laemmi/youtube-download
 * @since      12.05.17
 */

namespace Laemmi\YoutubeDownload\Data;

use PHPUnit_Framework_TestCase;

class StreamTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test default values
     *
     * @param $getter
     * @param $expected
     *
     * @dataProvider providerDefaultValues
     */
    public function testDefaultValues($getter, $expected)
    {
        $this->assertSame($expected, (new Stream())->$getter());
    }

    /**
     * DataProvider for testDefaultValues
     *
     * @return array
     */
    public function providerDefaultValues()
    {
        return [
            ['getUrl',           ''],
            ['getContentType',   ''],
            ['getSize',          0],
            ['getFileExtension', ''],
            ['getFilename',      ''],
            ['getFormat',        ''],
            ['getQuality',       'normal'],
        ];
    }

    /**
     * Test setter and getter
     *
     * @param $setter
     * @param $getter
     * @param $value
     * @param $expected
     *
     * @dataProvider providerGetterAndSetterToGetValidValue
     */
    public function testGetterAndSetterToGetValidValue($setter, $getter, $value, $expected)
    {
        $stream = new Stream();
        $stream->$setter($value);

        $this->assertSame($expected, $stream->$getter());
    }

    /**
     * DataProvider for testGetterAndSetterToGetValidValue
     *
     * @return array
     */
    public function providerGetterAndSetterToGetValidValue()
    {
        return [
            ['setUrl',           'getUrl',           'https://example.com', 'https://example.com'],
            ['setContentType',   'getContentType',   'video/mp4',           'video/mp4'],
            ['setSize',          'getSize',          '20473227',            '20473227'],
            ['setFileExtension', 'getFileExtension', '.mp4',                '.mp4'],
            ['setFilename',      'getFilename',      'example.mp4',         'example.mp4'],
            ['setFormat',        'getFormat',        'MPEG4',               'MPEG4'],
            ['setQuality',       'getQuality',       '360p',                '360p'],
        ];
    }
}
