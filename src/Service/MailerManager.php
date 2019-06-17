<?php
namespace Lle\MailerBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lle\MailerBundle\Entity\Destinataire;
use Lle\MailerBundle\Entity\DestinataireInterface;
use Lle\MailerBundle\Entity\Template;
use Symfony\Component\Routing\RouterInterface;
use Lle\MailerBundle\Entity\MailInterface;
use Lle\MailerBundle\Entity\Mail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class MailerManager
{

    protected $em;
    protected $router;
    protected $twig;
    protected $mailer;
    protected $parameters;

    public function __construct(EntityManagerInterface $em, RouterInterface $router, \Swift_Mailer $mailer, \Twig_Environment $twig, ParameterBagInterface $parameters)
    {
        $this->twig = $twig;
        $this->em = $em;
        $this->router = $router;
        $this->mailer = $mailer;
        $this->parameters = $parameters;
    }

    public function getTemplateRepository() {
        return $this->em->getRepository($this->parameters->get('lle.mailer.template_class'));
    }

    public function getMailRepository() {
        return $this->em->getRepository($this->parameters->get('lle.mailer.mail_class'));
    }

    public function getDestinataireRepository() {
        return $this->em->getRepository($this->parameters->get('lle.mailer.destinataire_class'));
    }

    protected function findTemplate($code)
    {
        /* @var Template $template */
        if(is_array($code)){
            $template = $this->getTemplateRepository()->findOneBy($code);
        }else{
            $template = $this->getTemplateRepository()->findOneBy(['code' => $code]);
        }

        if (! $template) {
            throw new \Exception('Code ' . $code . ' ne correspond a aucun template d\'email');
        }
        return $template;
    }

    protected function newInstanceMail()
    {
        return $this->em->getClassMetadata($this->parameters->get('lle.mailer.mail_class'))->newInstance();
    }

    protected function newInstanceDestinataire(){
        return $this->em->getClassMetadata($this->parameters->get('lle.mailer.destinataire_class'))->newInstance();
    }

    public function has($code): bool
    {
        $template = $this->getTemplateRepository()->findOneBy(['code' => $code]);
        return (bool) $template;
    }

    /**
     *
     * @param string $code
     * @param array $destinataires
     * @return Mail
     * @throws \Exception
     */
    public function create($code, $destinataires, array $expediteur = [], $returnPath = null): MailInterface
    {
        return $this->save($this->generate($code,$destinataires,$expediteur,$returnPath));
    }

    public function generate($code, $destinataires, array $expediteur = [], $returnPath = null): MailInterface{
        $template = $this->findTemplate($code);
        $mail = $this->newInstanceMail();
        foreach ($destinataires as $k => $destinataire) {
            $mail->addDestinataire($this->createDestinataire($k, $destinataire));
        }

        $mail->setExpediteur($expediteur[0] ?? null);
        $mail->setReplyTo($expediteur[0] ?? null);
        $mail->setAlias(key($expediteur));
        $mail->setTemplate($template);
        if ($returnPath) {
            $mail->setReturnPath($returnPath);
        }
        return $mail;
    }

    /**
     *
     * @param string $code
     * @param array $destinataires
     * @return Mail
     * @throws \Exception
     */
    public function createFromHtml($html, $sujet, $destinataires, array $expediteur = [], $returnPath = null): MailInterface
    {
        $mail = $this->newInstanceMail();
        foreach ($destinataires as $k => $destinataire) {
            $mail->addDestinataire($this->createDestinataire($k, $destinataire));
        }
        $mail->setExpediteur(current($expediteur));
        $mail->setReplyTo(current($expediteur));
        $mail->setAlias(key($expediteur));
        $mail->setHtml($html);
        $mail->setSujet($sujet);
        if ($returnPath) {
            $mail->setReturnPath($returnPath);
        }

        return $this->save($mail);
    }

    public function send(MailInterface $mail): MailInterface
    {
        $mail->setDateEnvoi(new \Datetime());

        $templateHtml = $this->twig->createTemplate($mail->getTemplate()->getHtml());
        $templateText = $this->twig->createTemplate($mail->getTemplate()->getText() ?? '');
        $templateSujet = $this->twig->createTemplate($mail->getTemplate()->getSujet());
        if ($mail->getExpediteur()) {
            $expediteur = array(
                $mail->getExpediteur() => $mail->getAlias()
            );
        } else {
            $expediteur = array(
                $mail->getTemplate()->getExpediteurMail() => $mail->getTemplate()->getExpediteurName()
            );
        }

        /** @var Destinataire $destinataire */
        foreach ($mail->getDestinataires() as $destinataire) {
            /** @var Router $router */
            // $urlTracking = $this->router->generate('llemailbundle_admin_tracking', array('id_mail' => $mail->getId(), 'id_destinataire' => $destinataire->getId()), true);
            // $urlRedirect = $this->router->generate('llemailbundle_admin_tracking_redirect', array('id_mail' => $mail->getId(), 'id_destinataire' => $destinataire->getId()), true);

            $html = $templateHtml->render($destinataire->getData());
            $text = $templateText->render($destinataire->getData());
            $sujet = $templateSujet->render($destinataire->getData());

            // $html = $mail->rewriteUrl($destinataire, $urlRedirect, $html);
            // $html .= '<img src="' . $urlTracking . '" alt="">';
            if ($destinataire->isValidEmail($this->parameters->get('lle.mailer.check_mx'))) {
                $message = (new \Swift_Message())->setSubject($sujet)
                    ->setFrom($expediteur)
                    ->setReturnPath($mail->getReturnPath())
                    ->setTo($destinataire->getEmail())
                    ->setReplyTo($mail->getReplyTo())
                    ->setBody($html, 'text/html')
                    ->addPart($text, 'text/plain');
                foreach($mail->getAttachments() as $attach){
                    $message->attach(\Swift_Attachment::fromPath($attach));
                }
                foreach ($mail->getStreamAttachments() as $streamAttachment){
                    $message->attach(new \Swift_Attachment($streamAttachment['data'], $streamAttachment['filename'], $streamAttachment['contentType']));
                }
                $mail->setEnvoye($this->mailer->send($message));
                $destinataire->setSuccess(TRUE);
            } else {
                $destinataire->setSuccess(FALSE);
            }
            $destinataire->setDateEnvoi(new \DateTime('now'));
            $destinataire->setHtml($html);
            $destinataire->setSujet($sujet);
            $this->em->persist($destinataire);
        }
        $mail->setDateEnvoiFini(new \Datetime());

        return $this->save($mail);
    }

    public function createDestinataire($email, $data): DestinataireInterface
    {
        $destinataire = $this->newInstanceDestinataire();
        $destinataire->setData($data);
        $destinataire->setEmail($email);
        return $destinataire;
    }

    private function save($item)
    {
        $this->em->persist($item);
        $this->em->flush();
        return $item;
    }
}
