<?php
namespace Lle\MailerBundle\DependencyInjection;
use Lle\MailerBundle\Entity\Template;
use Lle\MailerBundle\Entity\Destinataire;
use Lle\MailerBundle\Entity\Mail;
use Lle\PdfGeneratorBundle\Entity\PdfModel;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lle_mailer');
        $rootNode->children()
            ->scalarNode('template_class')->defaultValue(Template::class)->end()
            ->scalarNode('mail_class')->defaultValue(Mail::class)->end()
            ->scalarNode('destinataire_class')->defaultValue(Destinataire::class)->end()
            ->scalarNode('check_mx')->defaultValue(true)->end();
        return $treeBuilder;
    }
}
