<?php

namespace Lle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


interface DestinataireInterface
{
    public function getId();
    public function setEmail($email);
    public function getEmail();
    public function setDateEnvoi($dateEnvoi);
    public function getDateEnvoi();
    public function setDateOuvert($dateOuvert);
    public function getDateOuvert();
    public function isDateOuvertDispo();
    public function isUrlDispo();
    public function setUrl($url);
    public function getUrl();
    public function addUrl($url);
    public function setMail(MailInterface $mail = null);
    public function getMail();
    public function setData($data);
    public function getData();
    public function isSuccess();
    public function setSuccess($success);
    public function getSujet();
    public function getHtml();
    public function setSujet($sujet);
    public function setHtml($html);
    public function isValidEmail($checkMX = false, $checkHost = false): bool;
}
