<?php

namespace Lle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


interface MailInterface
{
    public function addDestinataire(DestinataireInterface $destinataire);
    public function removeDestinataire(DestinataireInterface $destinataire);
    public function getDestinataires();
    public function setDateEnvoi($dateEnvoi);
    public function getDateEnvoi();
    public function setEnvoye($envoye);
    public function getEnvoye();
    public function rewriteUrl($destinataire = null,$urlDeRedirection, $html = null);
    public function getAllData($destinataire);
    public function setSujet($sujet);
    public function getSujet();
    public function setDatePrevu($datePrevu);
    public function getDatePrevu();
    public function setDateEnvoiFini($dateEnvoiFini);
    public function getDateEnvoiFini();
    public function setExpediteur($expediteur);
    public function getExpediteur();
    public function setReturnPath($returnPath);
    public function getReturnPath();
    public function setEtat($etat);
    public function getEtat();
    public function setAlias($alias);
    public function getAlias();
    public function nbDestinataire();
    public function setSender($sender);
    public function getSender();
    public function setInfoSender($infoSender);
    public function getInfoSender();
    public function valide();
    public function getInfo();
    public function getTracking();
    public function getStatistique();
    public function setReplyTo($replyTo);
    public function getReplyTo();
    public function getHtml();
    public function setHtml(string $html);
    public function getAttachments();
    public function addAttachment(string $attach);
    public function setTemplate(TemplateInterface $template);
    public function getTemplate();
    //public function getCodeTemplate(): string;
    //public function setCodeTemplate(string $codeTemplate): void;

}
