<?php

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

$container->setParameter('av_district.provider.class', 'DeejayPoolBundle\Provider\AvDistrictProvider');
$container->setParameter('franchise_pool.provider.class', 'DeejayPoolBundle\Provider\FranchisePoolProvider');
$container->setParameter('franchise_pool_video.provider.class', 'DeejayPoolBundle\Provider\FranchisePoolVideoProvider');
$container->setParameter('avd.command.download.class', 'DeejayPoolBundle\Command\DownloaderCommand');
$container->setParameter('avd.command.status.class', 'DeejayPoolBundle\Command\StatusCommand');
$container->setParameter('deejay_provider_manager.class', 'DeejayPoolBundle\Provider\ProviderManager');

$container
    ->setDefinition(
        'deejay_provider_manager',
        new Definition(
            '%deejay_provider_manager.class%',
            []
        )
    )
;

$container
    ->setDefinition(
        'avd.session',
        new Definition(
            '%av_district.provider.class%',
            array(
                new Reference('event_dispatcher'),
                new Reference('logger', \Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE)
            ))
    )
    ->addMethodCall('setContainer', [new Reference('service_container')])
    ->addTag('monolog.logger', array('channel' => 'avd'))
    ->addTag('deejay_provider', []);

$container
    ->setDefinition(
        'deejay_pool.provider.franchise',
        new Definition(
            '%franchise_pool.provider.class%',
            array(
                new Reference('event_dispatcher'),
                new Reference('logger', \Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE)
            ))
    )
    ->addMethodCall('setContainer', [new Reference('service_container')])
    ->addTag('monolog.logger', array('channel' => 'avd'))
    ->addTag('deejay_provider', []);

$container
    ->setDefinition(
        'deejay_pool.provider.franchise_video',
        new Definition(
            '%franchise_pool_video.provider.class%',
            array(
                new Reference('event_dispatcher'),
                new Reference('logger', \Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE)
            ))
    )
    ->addMethodCall('setContainer', [new Reference('service_container')])
    ->addTag('monolog.logger', array('channel' => 'avd'))
    ->addTag('deejay_provider', []);


$container
    ->register(
        'deejay_pool.command.download',
        '%avd.command.download.class%'
    )
    ->addArgument(new Reference('deejay_provider_manager'))
    ->addArgument(new Reference('event_dispatcher'))
    ->addArgument(new Reference('logger', \Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE))
    ->addTag('console.command');

$container
    ->register(
        'deejaypool.command.status',
        '%avd.command.status.class%'
    )
    ->addArgument(new Reference('avd.session'))
    ->addArgument(new Reference('logger', \Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_INVALID_REFERENCE))
    ->addTag('console.command');
