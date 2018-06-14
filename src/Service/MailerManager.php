<?php

namespace Lle\MailerBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Lle\MailerBundle\Entity\Mail;
use Lle\MailerBundle\Entity\Destinataire;
use Symfony\Component\Routing\RouterInterface;

class MailerManager
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(EntityManagerInterface $em, RouterInterface $router, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->router = $router;
        $this->mailer = $mailer;
    }

    /**
     * @param string $code
     * @param array  $destinataires
     * @return Mail
     * @throws \Exception
     */
    public function create($code, $destinataires, $expediteur = ['2le' => '2le@2le.net'], $returnPath = null)
    {
        $template = $this->em->getRepository('LleMailerBundle:Template')->findOneBy(array('code' => $code));
        if (!$template) {
            throw new \Exception('Code ' . $code . ' ne correspond a aucun template d\'email');
        }
        $mail = new Mail();
        foreach ($destinataires as $k => $destinataire) {
            $mail->addDestinataire($this->createDestinataire($k, $destinataire));
        }
        $mail->setExpediteur(current($expediteur));
        $mail->setReplyTo(current($expediteur));
        $mail->setAlias(key($expediteur));
        $mail->setTemplate($template);
        if ($returnPath) {
            $mail->setReturnPath($returnPath);
        }

        return $this->save($mail);
    }

    /**
     * @param string $code
     * @param array  $destinataires
     * @return Mail
     * @throws \Exception
     */
    public function createFromHtml($html, $sujet, $destinataires, $expediteur = ['2le' => '2le@2le.net'], $returnPath = null)
    {
        $mail = new Mail();
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

    /**
     * @param Mail $mail
     * @return Mail
     */
    public function send(Mail $mail)
    {
        $mail->setDateEnvoi(new \Datetime());
        /** @var Destinataire $destinataire */
        foreach ($mail->getDestinataires() as $destinataire) {
            /** @var Router $router */
            $urlTracking = $this->router->generate('llemailbundle_admin_tracking', array('id_mail' => $mail->getId(), 'id_destinataire' => $destinataire->getId()), true);
            $urlRedirect = $this->router->generate('llemailbundle_admin_tracking_redirect', array('id_mail' => $mail->getId(), 'id_destinataire' => $destinataire->getId()), true);
            $html = $mail->render($destinataire);
            $text = $mail->renderPlainText($destinataire);
            $html = $mail->rewriteUrl($destinataire, $urlRedirect, $html);
            $html .= '<img src="' . $urlTracking . '" alt="">';
            $message = \Swift_Message::newInstance()
                    ->setSubject($mail->renderSujet())
                    ->setFrom(array($mail->getExpediteur() => $mail->getAlias()))
                    ->setReturnPath($mail->getReturnPath())
                    ->setTo($destinataire->getEmail())
                    ->setReplyTo($mail->getReplyTo())
                    ->setBody($html, 'text/html')
                    ->addPart($text, 'text/plain');
            $mail->setEnvoyer($this->mailer->send($message));
            $destinataire->setDateEnvoi(new \DateTime('now'));
            $this->em->persist($destinataire);
        }
        $mail->setDateEnvoiFini(new \Datetime());

        return $this->save($mail);
    }

    public function createDestinataire($email, $data)
    {
        $destinataire = new Destinataire();
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
