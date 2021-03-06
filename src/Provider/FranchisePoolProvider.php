<?php

namespace DeejayPoolBundle\Provider;

use DeejayPoolBundle\DeejayPoolBundle;
use DeejayPoolBundle\Entity\FranchisePoolItem;
use DeejayPoolBundle\Entity\ProviderItemInterface;
use DeejayPoolBundle\Serializer\Normalizer\FranchiseRecordPoolItemNormalizer;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Serializer\Serializer;

/**
 * Class FranchisePoolProvider
 *
 * @package DeejayPoolBundle\Provider
 * @author Christophe Pyree <yemistikris@hotmail.fr>
 */
class FranchisePoolProvider extends AbstractProvider implements PoolProviderInterface
{
    /** @var EventDispatcher */
    protected $eventDispatcher;
    /** @var Serializer */
    protected $serializer;
    /** @var string */
    private $lastDownloadLink;
    /**
     * @var string
     */
    protected $downloadedFileName;

    /**
     * FranchisePoolProvider constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param Logger|null                                                 $logger
     */
    public function __construct(
        $eventDispatcher,
        Logger $logger = null
    ) {
        parent::__construct($eventDispatcher, $logger);
        $this->serializer = new Serializer([new FranchiseRecordPoolItemNormalizer()]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return DeejayPoolBundle::PROVIDER_FPR_AUDIO;
    }

    /**
     * @return bool
     */
    public function supportAsyncDownload()
    {
        return false;
    }

    /**
     * @param ProviderItemInterface $item
     * @return bool
     */
    public function itemCanBeDownload(ProviderItemInterface $item)
    {
        $item->setDownloadLink($this->getConfValue('download_url').$item->getItemId());

        return true;
    }

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public function getDownloadedFileName(ResponseInterface $response)
    {
        $ctDisp = $response->getHeader('Content-Disposition')[0];
        preg_match('/filename="+(?P<filename>.+)"+/', $ctDisp, $matches);

        return $fileName = $matches['filename'];
    }

    /**
     * @param ResponseInterface $response
     * @param string            $tempName
     * @return bool
     */
    public function hasCorrectlyDownloaded(ResponseInterface $response, $tempName)
    {
        return ($response->getStatusCode() === 200) && parent::hasCorrectlyDownloaded($response, $tempName);
    }

    /**
     * @param $login
     * @param $password
     * @return ResponseInterface
     */
    protected function getLoginResponse($login, $password)
    {
        return $response = $this->client->post(
            $this->getConfValue('login_check'),
            [
                //'cookies'           => $this->cookieJar,
                'allow_redirects' => false,
                'debug' => $this->debug,
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'form_params' => [
                    $this->container->getParameter(DeejayPoolBundle::PROVIDER_FPR_AUDIO.'.configuration.login_form_name') => $login ? $login : $this->container->getParameter(DeejayPoolBundle::PROVIDER_FPR_AUDIO.'.credentials.login'),
                    $this->container->getParameter(DeejayPoolBundle::PROVIDER_FPR_AUDIO.'.configuration.password_form_name') => $password ? $password : $this->container->getParameter(DeejayPoolBundle::PROVIDER_FPR_AUDIO.'.credentials.password'),
                ],
            ]
        );
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    protected function hasCorrectlyConnected(ResponseInterface $response)
    {
        if ($response->getHeaderLine('Location') === $this->getConfValue('login_success_redirect')) {
            return true;
        }

        return false;
    }

    /**
     * @param       $page
     * @param array $filter
     * @return ResponseInterface
     */
    protected function getItemsResponse($page, $filter = [])
    {
        return $response = $this->client->get(
            $this->getConfValue('items_url'),
            [
                //'cookies'           => $this->cookieJar,
                'allow_redirects' => true,
                'debug' => $this->debug,
                'query' => $this->getItemsQuery($page),
            ]
        );
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function parseItemResponse(ResponseInterface $response)
    {
        $itemsArray = [];
        $rep = json_decode($response->getBody(), true);

        if (isset($rep['rows']) && count($rep['rows']) > 0) {
            foreach ($rep['rows'] as $avItemArray) {
                /** @var FranchisePoolItem $item */
                $item = $this->getNormalizedObject($avItemArray);
                $item->setAudio(true);
                $itemsArray[] = $item;
            }
        }

        return $itemsArray;
    }

    /**
     * @param ProviderItemInterface $item
     * @param string                $tempName
     * @return ResponseInterface
     */
    protected function getDownloadResponse(ProviderItemInterface $item, $tempName)
    {
        $requestParams = [
            //'cookies' => $this->cookieJar,
            'allow_redirects' => false,
            'debug' => $this->debug,
            'headers' => [
                'Referer' => $this->getConfValue('login_success_redirect'),
                'Upgrade-Insecure-Requests' => 1,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, sdch',
                'Accept-Language' => 'fr-FR,fr;q=0.8,en-US;q=0.6,en;q=0.4',
            ],
        ];
        do {
            $resource = fopen($tempName, 'w');
            $requestParams['sink'] = $resource;
            $response = $this->client->get(
                $item->getDownloadLink(),
                $requestParams
            );
            if ($requestUrl = $response->getHeaderLine('Location')) {
                $item->setDownloadLink($requestUrl);
                $this->lastDownloadLink = $requestUrl;
            }
            fclose($resource);
        } while ($response->hasHeader('Location'));

        return $response;
    }

    /**
     * @param $page
     *
     * @return array
     */
    protected function getItemsQuery($page)
    {
        return [
            '_search' => false,
            'nd' => time(),
            'rows' => $this->getConfValue('items_per_page'),
            'page' => $page,
            'sidx' => 'tracks.created',
            'sord' => 'desc',
        ];
    }

    /**
     * @param $avItemArray
     *
     * @return FranchisePoolItem
     */
    protected function getNormalizedObject($avItemArray)
    {
        return $this->serializer->denormalize($avItemArray, FranchiseRecordPoolItemNormalizer::ITEM_AUDIO);
    }

}
