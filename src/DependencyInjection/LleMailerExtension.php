<?php

namespace Lle\MailerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LleMailerExtension extends Extension
{

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $configuration = new Configuration();

        $config =  $this->processConfiguration($configuration, $configs);
        $container->setParameter( 'lle.mailer.mail_class', $config[ 'mail_class' ] );
        $container->setParameter( 'lle.mailer.destinataire_class', $config[ 'destinataire_class' ] );
        $container->setParameter( 'lle.mailer.template_class', $config[ 'template_class' ] );
        $container->setParameter('lle.mailer.check_mx', $config['check_mx']);
    }

}
