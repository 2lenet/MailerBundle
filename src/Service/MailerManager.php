<?php
namespace Lle\MailerBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Lle\MailerBundle\Entity\Destinataire;
use Symfony\Component\Routing\RouterInterface;
use Lle\MailerBundle\MailInterface;
use Lle\MailerBundle\Entity\Mail;

class MailerManager
{

    /**
     *
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     *
     * @var RouterInterface
     */
    protected $router;

    /**
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     *
     * @var \Swift_Mailer
     */
    protected $mailer;

    public function __construct(EntityManagerInterface $em, RouterInterface $router, \Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->twig = $twig;
        $this->em = $em;
        $this->router = $router;
        $this->mailer = $mailer;
    }

    protected function findTemplate($code)
    {
        $template = $this->em->getRepository('LleMailerBundle:Template')->findOneBy(array(
            'code' => $code
        ));
        if (! $template) {
            throw new \Exception('Code ' . $code . ' ne correspond a aucun template d\'email');
        }
        return $template;
    }

    protected function createMail()
    {
        return new Mail();
    }

    public function has($code)
    {
        $template = $this->em->getRepository('LleMailerBundle:Template')->findOneBy(array(
            'code' => $code
        ));
        return (bool) $template;
    }

    /**
     *
     * @param string $code
     * @param array $destinataires
     * @return MailInterface
     * @throws \Exception
     */
    public function create($code, $destinataires, $expediteur = ['2le' => '2le@2le.net'], $returnPath = null)
    {
        $template = $this->findTemplate($code);
        $mail = $this->createMail();
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
     *
     * @param string $code
     * @param array $destinataires
     * @return Mail
     * @throws \Exception
     */
    public function createFromHtml($html, $sujet, $destinataires, $expediteur = ['2le' => '2le@2le.net'], $returnPath = null)
    {
        $mail = $this->createMail();
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
     *
     * @param MailInterface $mail
     * @return MailInterface
     */
    public function send(MailInterface $mail)
    {
        $mail->setDateEnvoi(new \Datetime());

        $templateHtml = $this->twig->createTemplate($mail->getTemplate()
            ->getHtml());
        $templateText = $this->twig->createTemplate($mail->getTemplate()
            ->getText());
        $templateSujet = $this->twig->createTemplate($mail->getTemplate()
            ->getSujet());
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

            if ($destinataire->isValidEmail(true)) {
                $message = (new \Swift_Message())->setSubject($sujet)
                    ->setFrom($expediteur)
                    ->setReturnPath($mail->getReturnPath())
                    ->setTo($destinataire->getEmail())
                    ->setReplyTo($mail->getReplyTo())
                    ->setBody($html, 'text/html')
                    ->addPart($text, 'text/plain');
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
