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

class Youtube implements ServiceInterface
{
    const YT_URL_INFO           = 'https://www.youtube.com/get_video_info?video_id=';  // &el=embedded&ps=default&eurl=&gl=US&hl=en
    const YT_URL_IMG_PREVIEW    = 'https://img.youtube.com/vi/'; // http://i1.ytimg.com/vi/

    private $HttpClient = null;

    private $id = '';

    public function __construct(ClientInterface $HttpClient, ServiceOptionsInterface $service_options)
    {
        $this->HttpClient = $HttpClient;
    }

    /**
     * Set id / url
     *
     * @param $value
     * @throws YoutubeException
     */
    public function setId($value)
    {
        $urldata = parse_url($value);

        if(! isset($urldata['query'])) {
            throw new YoutubeException('no query found', YoutubeException::NO_QUERY_FOUND);
        }

        parse_str($urldata['query'], $query);

        if(! isset($query['v'])) {
            throw new YoutubeException('no query param v found', YoutubeException::NO_QUERY_PARAM_FOUND);
        }

        $this->id = $query['v'];
    }

    /**
     * Get data
     *
     * @return Data
     */
    public function getData()
    {
        $id = $this->id;

        $info = $this->getVideoInfo($id);

        $data = new Data();
        $data->setTitle($info['meta']['title']);
        $data->setPreviewUrl(self::YT_URL_IMG_PREVIEW.$id.'/default.jpg');

        $sort = [];

        foreach($info['stream'] as $key => $val)
        {
            $x = explode(';', $val['type']);
            $content_type   = $x[0];
            $videotype      = $this->getVideotype($val);
            $size           = $this->HttpClient->getHeaderContentLength($val['url']);

            $stream = new Data\Stream();
            $stream->setUrl($val['url']);
            $stream->setContentType($content_type);
            $stream->setSize($size);
            $stream->setFileExtension($videotype['type']['extension']);
            $stream->setFilename($info['meta']['title'].$videotype['type']['extension']);
            $stream->setFormat($videotype['type']['name']);
            $stream->setQuality($videotype['quality']);

            $data->append($stream);

            $sort[] = $videotype['sort'];

//            $data['stream'][] = array(
//                'url'          => $val['url'],
//                'content_type' => $content_type,
//                'size'         => $size,
//                'size_format'  => $this->formatBytes($size),
//                'fileextension'=> $videotype['type']['extension'],
//                'filename'     => $info['meta']['title'].$videotype['type']['extension'],
//                'typename'     => $videotype['type']['name'],
//                'typesort'     => $videotype['sort'],
//
//                'url_download' => $val['url'].'&title='.$info['meta']['title'],
//                'quality'      => $videotype['quality'],
//            );
        }

//        foreach ($data['stream'] as $key => $row)
//        {
//            $typesort[$key] = $row['typesort'];
//        }
//        array_multisort($typesort, SORT_ASC, $data['stream']);

        return $data;
    }

    /**
     * Get video info
     *
     * @param $id
     * @throws YoutubeException
     * @return array
     */
    private function getVideoInfo($id)
    {
        $data = array('meta' => array(), 'stream' => array());

        parse_str($this->HttpClient->getContent(self::YT_URL_INFO.$id), $info);

        if(isset($info['status']) && 'fail' === $info['status']) {
            throw new YoutubeException($info['reason'], YoutubeException::FAILED_STATUS_REASON);
        }

        $data['meta']['title'] = $info['title'];

        $streams = explode(',', $info['url_encoded_fmt_stream_map']);
        foreach($streams as $stream)
        {
            parse_str($stream, $real_stream);
            $data['stream'][] = $real_stream;
        }

        return $data;
    }

    /**
     * Get video type
     *
     * @param $value
     * @return mixed
     */
    private function getVideotype($value)
    {
        $type = array(
            'video/webm' => array(
                'extension' => '.webm',
                'name'      => 'WebM'
            ),
            'video/3gpp' => array(
                'extension' =>'.3gp',
                'name'      => '3GPP'
            ),
            'video/x-flv' => array(
                'extension' =>'.flv',
                'name'      => 'FLV'
            ),
            'video/mp4' => array(
                'extension' =>'.mp4',
                'name'      => 'MPEG4'
            )
        );

        $map = array(


            37 => array(
                'type'      => $type['video/mp4'],
                'quality'   => $value['quality'].' (1080p)',
                'sort'      => 1000
            ),
            84 => array(
                'type'      => $type['video/mp4'],
                'quality'   => $value['quality'], // 720p
                'sort'      => 1001
            ),
            22 => array(
                'type'      => $type['video/mp4'],
                'quality'   => $value['quality'], // 720p
                'sort'      => 1002
            ),
            82 => array(
                'type'      => $type['video/mp4'],
                'quality'   => $value['quality'],
                'sort'      => 1003
            ),
            18 => array(
                'type'      => $type['video/mp4'],
                'quality'   => $value['quality'], // 480p
                'sort'      => 1004
            ),

            35 => array(
                'type'      => $type['video/x-flv'],
                'quality'   => $value['quality'], // 480p
                'sort'      => 2000
            ),
            34 => array(
                'type'      => $type['video/x-flv'],
                'quality'   => $value['quality'], // 320p
                'sort'      => 2001
            ),
            6 => array(
                'type'      => $type['video/x-flv'],
                'quality'   => $value['quality'], // 240p
                'sort'      => 2002
            ),
            5 => array(
                'type'      => $type['video/x-flv'],
                'quality'   => $value['quality'], // 240p
                'sort'      => 2003
            ),

            36 => array(
                'type'      => $type['video/3gpp'],
                'quality'   => $value['quality'], // 320p
                'sort'      => 3000
            ),
            17 => array(
                'type'      => $type['video/3gpp'],
                'quality'   => $value['quality'], // 240p
                'sort'      => 3001
            ),
            13 => array(
                'type'      => $type['video/3gpp'],
                'quality'   => $value['quality'], // 240p
                'sort'      => 3002
            ),

            43 => array(
                'type'      => $type['video/webm'],
                'quality'   => $value['quality'],
                'sort'      => 4000
            ),

            100 => array(
                'type'      => $type['video/webm'],
                'quality'   => $value['quality'],
                'sort'      => 4000
            ),
        );

        if(isset($map[$value['itag']])) {
            return $map[$value['itag']];
        }
        $x = explode(';', $value['type']);
        return array(
            'type'      => $type[$x[0]],
            'quality'   => $value['quality'].' '.$value['itag'],
            'sort'      => 99
        );
    }
}
