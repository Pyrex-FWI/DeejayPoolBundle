<?php

namespace DeejayPoolBundle\DependencyInjection;

use DeejayPoolBundle\DeejayPoolBundle;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\ScalarNode;

/**
 * Class Configuration
 *
 * @package DeejayPoolBundle\DependencyInjection
 * @author Christophe Pyree <yemistikris@hotmail.fr>
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    const AVD_LOGIN_CHECK = 'http://www.avdistrict.net/Account/CheckLogin';
    const ITEMS_PER_PAGE = 25;

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $digital_dj_poolRoot = $treeBuilder->root('deejay_pool');

        $digital_dj_poolRoot
            ->children()
                ->arrayNode(DeejayPoolBundle::PROVIDER_DPP)
                ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->getCredentialsDefinition())
                        ->append($this->getDdpConfigurationDefinition())
                    ->end()
                ->end()
                ->arrayNode(DeejayPoolBundle::PROVIDER_AVD)
                ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->getCredentialsDefinition())
                        ->append($this->getAvdConfigurationDefinition())
                    ->end()
                ->end()
                ->arrayNode(DeejayPoolBundle::PROVIDER_FPR_AUDIO)
                ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->getCredentialsDefinition())
                        ->append($this->getFrpConfigurationDefinition())
                    ->end()
                ->end()
                ->arrayNode(DeejayPoolBundle::PROVIDER_FPR_VIDEO)
                ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->getCredentialsDefinition())
                        ->append($this->getFrpConfigurationDefinition(
                            'http://www.franchiserecordpool.com/video/list',
                            'http://www.franchiserecordpool.com/download/video/'
                        ))
                    ->end()
                ->end()
                ->arrayNode(DeejayPoolBundle::PROVIDER_SV)
                ->addDefaultsIfNotSet()
                    ->children()
                        ->append($this->getCredentialsDefinition())
                        ->append($this->getSmashVisionConfigurationDefinition())
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * @param string $v
     * @return mixed
     */
    public function isValidurl($v)
    {
        return filter_var($v, FILTER_VALIDATE_URL);
    }

    /**
     * @param string $v
     * @return mixed
     */
    public function isValidRegex($v)
    {
        return filter_var($v, FILTER_VALIDATE_REGEXP);
    }

    /**
     * Credentials configuration part
     * For AvDistrict and Franchise Record Pool Providers.
     *
     * @return ArrayNodeDefinition
     */
    public function getCredentialsDefinition()
    {
        $node = new ArrayNodeDefinition('credentials');
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('login')
                ->info('Login of your account')
                ->cannotBeEmpty()
            ->end();
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('password')
                ->info('Password of your account')
                ->cannotBeEmpty()
            ->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    public function getAvdConfigurationDefinition()
    {
        $configurationDef = new ArrayNodeDefinition('configuration');
        $configurationDef

            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('root_path')
                ->info('Path of your video files')
                ->example('/your/path/to/download/target/files/')
                ->defaultValue('%kernel.cache_dir%')
                ->isRequired()
            ->end();
        $configurationDef
            ->children()
                ->scalarNode('items_properties')
                ->defaultValue('videoid,title,artist,genres,bpm,posted,credits,hd,advisory,filename,approved,editor,downloadid')
                ->cannotBeEmpty()
            ->end();
        $configurationDef
            ->children()
                ->scalarNode('login_check')
                    ->info('Login url for authentication')
                    ->defaultValue(self::AVD_LOGIN_CHECK)
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout login_check value.')
                    ->end()
                ->end()
                ->integerNode('items_per_page')
                    ->info('Items per page')
                    ->defaultValue(self::ITEMS_PER_PAGE)
                ->end()
            ->end();
        $configurationDef
            ->children()
                ->scalarNode('login_form_name')
                    ->info('Name of login field into form')
                    ->defaultValue('email')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('password_form_name')
                    ->info('Name of password field into form')
                    ->defaultValue('password')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('items_url')
                    ->info('Items page')
                    ->defaultValue('http://www.avdistrict.net/Videos/LoadVideosData')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout item_url value.')
                    ->end()
                ->end()
                ->scalarNode('download_url')
                    ->info('Download url')
                    ->defaultValue('http://www.avdistrict.net/Handlers/DownloadHandler.ashx')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout download_url value.')
                    ->end()
                ->end()
                ->scalarNode('donwload_keygen_url')
                    ->info('Download keygen url')
                    ->defaultValue('http://www.avdistrict.net/Videos/InitializeDownload')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout download_keygen_url value.')
                    ->end()
                ->end()
            ->end();

        return $configurationDef;
    }

    /**
     * @return ArrayNodeDefinition
     */
    public function getDdpConfigurationDefinition()
    {
        $configurationDef = new ArrayNodeDefinition('configuration');
        $configurationDef

            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('root_path')
                ->info('Path of your video files')
                ->example('/your/path/to/download/target/files/')
                ->defaultValue('%kernel.cache_dir%')
                ->isRequired()
            ->end();
        $configurationDef
            ->children()
                ->scalarNode('login_check')
                    ->info('Login url for authentication')
                    ->defaultValue('https://digitaldjpool.com/Account/SignIn')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout login_check value.')
                    ->end()
                ->end()
                ->integerNode('items_per_page')
                    ->info('Items per page')
                    ->defaultValue(self::ITEMS_PER_PAGE)
                ->end()
            ->end();
        $configurationDef
            ->children()
                ->scalarNode('login_form_name')
                    ->info('Name of login field into form')
                    ->defaultValue('UserName')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('password_form_name')
                    ->info('Name of password field into form')
                    ->defaultValue('Password')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('items_url')
                    ->info('Items page')
                    ->defaultValue('https://digitaldjpool.com/RecordPool/FilterSearch')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout item_url value.')
                    ->end()
                ->end()
                ->scalarNode('download_url')
                    ->info('Download url')
                    ->defaultValue('http://www.avdistrict.net/Handlers/DownloadHandler.ashx')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout download_url value.')
                    ->end()
                ->end()
            ->end();

        return $configurationDef;
    }

    /**
     * @param null $itemsUrl
     * @param null $dlUrl
     * @return ArrayNodeDefinition
     */
    public function getFrpConfigurationDefinition($itemsUrl = null, $dlUrl = null)
    {
        if ($itemsUrl == null) {
            $itemsUrl = 'http://www.franchiserecordpool.com/track/list';
        }
        if ($dlUrl == null) {
            $dlUrl = 'http://www.franchiserecordpool.com/download/track/';
        }

        $configurationDef = new ArrayNodeDefinition('configuration');
        $configurationDef

            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('root_path')
                ->info('Path of your video files')
                ->example('/your/path/to/download/target/files/')
                ->defaultValue('%kernel.cache_dir%')
                ->isRequired()
            ->end();
        $configurationDef
            ->children()
                ->scalarNode('login_check')
                    ->info('Login url for authentication')
                    ->defaultValue('https://www.franchiserecordpool.com/signin')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout login_check value.')
                    ->end()
                ->end()
                ->scalarNode('login_success_redirect')
                    ->info('Url when login is success')
                    ->defaultValue('http://www.franchiserecordpool.com/welcome')
                    ->cannotBeEmpty()
                ->end()
            ->end();
        $configurationDef
            ->children()
                ->scalarNode('login_form_name')
                    ->info('Name of login field into form')
                    ->defaultValue('login')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('password_form_name')
                    ->info('Name of password field into form')
                    ->defaultValue('password')
                    ->cannotBeEmpty()
                ->end()
                ->integerNode('items_per_page')
                    ->info('Items per page')
                    ->defaultValue(10)
                ->end()
                ->scalarNode('items_url')
                    ->info('Items page')
                    ->defaultValue($itemsUrl)
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout item_url value.')
                    ->end()
                ->end()
                ->scalarNode('download_url')
                    ->info('Download url')
                    ->defaultValue($dlUrl)
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout download_url value.')
                    ->end()
                ->end()
            ->end();

        return $configurationDef;
    }

    /**
     * @param null $dlUrl
     * @return ArrayNodeDefinition
     */
    public function getSmashVisionConfigurationDefinition($dlUrl = null)
    {
        $configurationDef = new ArrayNodeDefinition('configuration');
        $configurationDef

            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('root_path')
                ->info('Path of your video files')
                ->example('/your/path/to/download/target/files/')
                ->defaultValue('%kernel.cache_dir%')
                ->isRequired()
            ->end();
        $configurationDef
            ->children()
                ->scalarNode('login_check')
                    ->info('Login url for authentication')
                    ->defaultValue('https://www.smashvision.net/Account/CheckLogin')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout login_check value.')
                    ->end()
                ->end()
            ->end();
        $configurationDef
            ->children()
                ->scalarNode('login_form_name')
                    ->info('Name of login field into form')
                    ->defaultValue('email')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('password_form_name')
                    ->info('Name of password field into form')
                    ->defaultValue('password')
                    ->cannotBeEmpty()
                ->end()
                ->integerNode('items_per_page')
                    ->info('Items per page')
                    ->defaultValue(25)
                ->end()
                ->scalarNode('items_url')
                    ->info('Items page')
                    ->defaultValue('https://www.smashvision.net/Videos/GetVideos')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout item_url value.')
                    ->end()
                ->end()
                ->scalarNode('items_versions_url')
                    ->info('Items versions page')
                    ->defaultValue('https://www.smashvision.net/Videos/GetButtons')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout items_versions_url value.')
                    ->end()
                ->end()
                ->scalarNode('download_url')
                    ->info('Download url')
                    ->defaultValue('https://www.smashvision.net/Handlers/DownloadHandler.ashx')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout download_url value.')
                    ->end()
                ->end()
                ->scalarNode('check_download_status_url')
                    ->info('Check status Download url')
                    ->defaultValue('https://www.smashvision.net/Videos/CheckDownload')
                    ->cannotBeEmpty()
                    ->validate()
                        ->ifTrue(function ($v) {
                            return !$this->isValidurl($v);
                        })
                        ->thenInvalid('%s is not a valid url, update yout download_url value.')
                    ->end()
                ->end()
            ->end();

        return $configurationDef;
    }
}
