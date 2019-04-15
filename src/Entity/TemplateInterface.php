<?php

namespace Lle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


interface TemplateInterface
{
    public function getId();
    public function __toString();
    public function setHtml($html);
    public function getHtml();
    public function setText($text);
    public function getText();
    public function setSujet($sujet);
    public function getSujet();
    public function setCode($code);
    public function getCode();
    public function getExpediteurMail();
    public function getExpediteurName();
    public function setExpediteurMail($expediteurMail);
    public function setExpediteurName($expediteurName);
}
