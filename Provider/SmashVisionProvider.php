<?php

namespace DeejayPoolBundle\Provider;

use DeejayPoolBundle\DeejayPoolBundle;
use DeejayPoolBundle\Entity\ProviderItemInterface;
use DeejayPoolBundle\Entity\SvItem;
use DeejayPoolBundle\Serializer\Normalizer\SvItemNormalizer;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Promise;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Serializer\Serializer;

/**
 * @author Pyrex-FWI <yemistikris@hotmail.fr>
 *
 * SmashVisionProvider
 */
class SmashVisionProvider extends Provider implements PoolProviderInterface, SearchablePoolProviderInterface
{

    protected $noTracksFromPage;

    /** @var  EventDispatcher */
    protected $eventDispatcher;
    /**
     * Get all embed children video into Parent VideoGroup.
     * @method getChild
     * @param  SvItem   $svGroup Parent svItem
     * @return SvItem[]          List of available versions
     */
    private function getChild(SvItem $svGroup)
    {
        $itemsArray = [];
        if ($svGroup->isParent() && $svGroup->getSvItems()->count() > 1) {
            foreach ($svGroup->getSvItems() as $svItem) {
                $itemsArray[] = $svItem;
            }
        } else {
            //$itemsArray[] = $svGroup;
        }

        return $itemsArray;
    }

    public function getDownloadResponse(\DeejayPoolBundle\Entity\ProviderItemInterface $item, $tempName)
    {
        $item->setDownloadlink($this->getConfValue('download_url') . '?' . http_build_query([
                'id' => $item->getVideoId(),
                'fg' => 'true',
        ]));
        $resource = fopen($tempName, 'w');

        return $this->client->get(
                $this->getConfValue('download_url'), 
                [
                    'cookies'         => $this->cookieJar,
                    'allow_redirects' => false,
                    'debug'           => $this->debug,
                    'sink'            => $resource,
                    'query'           => [
                        'id' => $item->getVideoId(),
                        'fg' => 'true',
                    ],
                    'headers'         => [
                        'Pragma'                    => 'no-cache',
                        'Accept-Encoding'           => 'gzip, deflate, sdch',
                        'Accept-Language'           => 'fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4',
                        'Upgrade-Insecure-Requests' => 1,
                        'User-Agent'                => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.99 Safari/537.36',
                        'Accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        'Referer'                   => 'https://www.smashvision.net/Videos?sort=date&dir=desc&keywords=&genreId=15&subGenreId=0&toolId=&featured=0&releaseyear=',
                        'Cache-Control'             => 'no-cache',
                        'Connection'                => 'keep-alive',
                    ]
                ]
        );
    }

    /**
     * @return bool
     */
    public function supportAsyncDownload()
    {
        return false;
    }

    /**
     * This Retreive all SPECIFIC video from groupID
     */
    public function getAllVideos($datas)
    {
        $promises = [];

        foreach ($datas as $index => $videoGroup) {
            $uri              = $this->getConfValue('items_versions_url') . '?' . http_build_query([
                    'cc'      => 'eu',
                    'rowId'   => '',
                    'groupId' => $videoGroup['groupId'],
                    'title'   => $videoGroup['title'],
                    '_'       => microtime(),
            ]);
            $promises[$index] = $this->client->getAsync(
                $uri, [
                'cookies'         => $this->cookieJar,
                'allow_redirects' => true,
                'debug'           => $this->debug,
                ]
            );
        }
        $results = Promise\unwrap($promises);

        foreach ($results as $index => $result) {
            $datas[$index]['videos'] = [];
            /** @var Response $result */
            $datas[$index]['videos'] = json_decode($result->getBody()->__toString(), true);
        }

        return $datas;
    }

    /**
     * Get download status from SmashVisionProvider to know if
     * video is available for download.
     * Return true is download is available else false
     * @method checkDownloadStatus
     * @param  SvItem              $svItem video item
     * @return boolean                     result
     */
    protected function checkDownloadStatus(SvItem $svItem)
    {
        $videoCanBeDownloaded = false;
        $this->logger->info(sprintf('get Download status for %s',$svItem->getItemId()));

        $response = $this->client->post(
            $this->getConfValue('check_download_status_url'), [
            'cookies'     => $this->cookieJar,
            'debug'       => $this->debug,
            'form_params' => [
                'videoId'  => $svItem->getVideoId(),
                'fromGrid' => 'true',
            ]
            ]
        );

        if ($response->getStatusCode() == 200) {
            $responseString = json_decode($response->getBody()->__toString(), 1);
            if (isset($responseString['haserrors']) && boolval($responseString['haserrors']) === false) {
                $videoCanBeDownloaded = true;
            }
            $this->logger->info(sprintf('Download status for %s : %s',$svItem->getItemId(), $responseString['msg']));
            $svItem->setDownloadStatus($responseString['msg']);
        } else {
            $svItem->setDownloadStatus(sprintf("Can't get download status for video #%s", $svItem->getItemId()));
            $this->logger->error(sprintf("Can't get download status for video #%s", $svItem->getItemId()));
        }

        return $videoCanBeDownloaded;
    }

    public function getName()
    {
        return 'smashvision';
    }

    public function itemCanBeDownload(ProviderItemInterface $item)
    {
        return $this->checkDownloadStatus($item);
    }

    public function getItemsBy($queryParameters = array(), $limit = null)
    {
        
    }

    /**
     * 
     * @param type $login
     * @param type $password
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function getLoginResponse($login, $password)
    {
        return $response = $this->client->post(
                $this->getConfValue('login_check'), [
                'cookies'         => $this->cookieJar,
                //'cookies'         => true,
                'allow_redirects' => true,
                'debug'           => $this->debug,
                'headers'         => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'form_params'     => [
                    $this->container->getParameter(DeejayPoolBundle::PROVIDER_SV . '.configuration.login_form_name')    => $login ? $login : $this->container->getParameter(DeejayPoolBundle::PROVIDER_SV . '.credentials.login'),
                    $this->container->getParameter(DeejayPoolBundle::PROVIDER_SV . '.configuration.password_form_name') => $password ? $password : $this->container->getParameter(DeejayPoolBundle::PROVIDER_SV . '.credentials.password'),
                    'rememberme'                                                                                        => true,
                    'ReturnUrl'                                                                                         => ''
                ]
                ]
        );
    }

    protected function hasCorrectlyConnected(\Psr\Http\Message\ResponseInterface $response)
    {
        if ($response->getStatusCode() == 200) {
            $rep = json_decode($response->getBody(), true);
            if (array_key_exists('Set-Cookie', $response->getHeaders())) {
                return true;
            } else {
                return false;
            }
        }
        
        return false;
    }

    protected function getItemsResponse($page, $filter = [])
    {
        return $response   = $this->client->post(
            $this->getConfValue('items_url'), [
            'cookies'         => $this->cookieJar,
            'allow_redirects' => true,
            'debug'           => $this->debug,
            'form_params'     => array_merge([
                        'rows'        => $this->getConfValue('items_per_page'),
                        'page'        => $page,
                        'cc'          => 'eu',
                        'sort'        => 'date',
                        'dir'         => 'desc',
                        '_'           => microtime(false),
                    ], $this->getCriteria($filter))
            ]
        );        
    }

    protected function parseItemResponse(\Psr\Http\Message\ResponseInterface $response)
    {
        $itemsArray              = [];
        $rep                     = json_decode($response->getBody(), true);
        $this->serializer        = new Serializer([new SvItemNormalizer()]);
        $context['download_url'] = $this->getConfValue('download_url');

        if (isset($rep['data']) && count($rep) > 0) {
            $rep['data'] = $this->getAllVideos($rep['data']);
            foreach ($rep['data'] as $svItemArray) {
                $svGroupItem = $this->serializer->denormalize($svItemArray, SvItemNormalizer::SVITEM, null, $context);
                $itemsArray  = array_merge($itemsArray, $this->getChild($svGroupItem));
            }
        }
        return $itemsArray;
    }

    protected function getDownloadedFileName(\Psr\Http\Message\ResponseInterface $response)
    {
        $ctDisp   = str_replace('"', '', $response->getHeader('Content-Disposition')[0]);
        preg_match('/filename="?(?P<filename>.+)$/', $ctDisp, $matches);
        return $matches['filename'];                
    }
    
    public function getCriteria($filter = [])
    {
        return array_merge(
            [
                'keywords'    => '',
                'genreId'     => 1000, //all video
                'hd'          => -1, //Get only hd
                'subGenreId'  => 0,
                'toolId'      => '',
                'featured'    => 0,
                'releaseyear' => '',
            ],
            (array)$filter
            );
    }

    public function getAvailableCriteria()
    {
        return [
            'keywords',
            'releaseyear',
            'genreId',
            'subGenreId',
            'hd',
            //'cc'
        ];
    }

}
