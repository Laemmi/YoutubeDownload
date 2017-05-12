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

namespace Laemmi\YoutubeDownload;

use PHPUnit_Framework_TestCase;
use Laemmi\YoutubeDownload\Service\Youtube;
use Laemmi\YoutubeDownload\Service\Vimeo;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test if class has correctly instance
     *
     * @dataProvider providerFactoryReturnCorrectObject
     */
    public function testFactoryReturnCorrectObject($value, $expected)
    {
        $this->assertInstanceOf($expected, Service::factory($value));
    }

    /**
     * DataProvider for testFactoryReturnCorrectObject
     *
     * @return array
     */
    public function providerFactoryReturnCorrectObject()
    {
        return [
            ['https://www.youtube.com/watch?v=Rd7YsqomnGI', Youtube::class],
            ['https://youtube.com/watch?v=Rd7YsqomnGI', Youtube::class],

            ['https://www.vimeo.com/30857968', Vimeo::class],
            ['https://vimeo.com/30857968', Vimeo::class],
        ];
    }

}
