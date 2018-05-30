<?php
namespace Lle\MailBundle\Service;


use Symfony\Component\HttpFoundation\Response;
use Lle\MailBundle\Entity\Mail;
use Lle\MailBundle\Entity\Destinataire;
use Symfony\Component\Routing\Router;


class MailManager{
    private $container;
    private $em;

    public function __construct($container) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    private function get($name){
        return $this->container->get($name);
    }


    /**
     * @param string $code
     * @param array  $destinataires
     * @return Mail
     * @throws \Exception
     */
    public function create($code, $destinataires, $expediteur = ['2le' => '2le@2le.net'], $returnPath = null){
        $template = $this->em->getRepository('LleMailBundle:Template')->findOneBy(array('code'=>$code));
        if(!$template){
            throw new \Exception('Code '.$code.' ne correspond a aucun template d\'email');
        }
        $mail = new Mail();
        foreach($destinataires as $k => $destinataire){
            $mail->addDestinataire($this->createDestinataire($k,$destinataire));
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
    public function createFromHtml($html, $sujet, $destinataires, $expediteur = ['2le' => '2le@2le.net'], $returnPath = null){
        $mail = new Mail();
        foreach($destinataires as $k => $destinataire){
            $mail->addDestinataire($this->createDestinataire($k,$destinataire));
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
    public function send(Mail $mail){
        $mail->setDateEnvoi(new \Datetime());
        /** @var Destinataire $destinataire */
        foreach($mail->getDestinataires() as $destinataire){
            /** @var Router $router */
            $router = $this->get('router');
            $urlTracking = $router->generate('llemailbundle_admin_tracking', array('id_mail'=>$mail->getId(),'id_destinataire'=>$destinataire->getId()), true);
            $urlRedirect = $this->get('router')->generate('llemailbundle_admin_tracking_redirect',array('id_mail'=>$mail->getId(),'id_destinataire'=>$destinataire->getId()),true);
            $html = $mail->render($destinataire);
            $text = $mail->renderPlainText($destinataire);
            $html = $mail->rewriteUrl($destinataire,$urlRedirect, $html);
            $html.= '<img src="'.$urlTracking.'" alt="">';
            $message = \Swift_Message::newInstance()
                ->setSubject($mail->renderSujet())
                ->setFrom(array($mail->getExpediteur()=>$mail->getAlias()))
                ->setReturnPath($mail->getReturnPath())
                ->setTo($destinataire->getEmail())
                ->setReplyTo($mail->getReplyTo())
                ->setBody($html, 'text/html')
                ->addPart($text, 'text/plain');
            $mail->setEnvoyer($this->get('mailer')->send($message));
            $destinataire->setDateEnvoi(new \DateTime('now'));
            $this->em->persist($destinataire);
        }
        $mail->setDateEnvoiFini(new \Datetime());

        return $this->save($mail);
    }


    public function createDestinataire($email,$data){
        $destinataire = new Destinataire();
        $destinataire->setData($data);
        $destinataire->setEmail($email);
        return $destinataire;
    }

    private function save($item){
        $this->em->persist($item);
        $this->em->flush();
        return $item;
    }



}
