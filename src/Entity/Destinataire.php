<?php

namespace Lle\MailerBundle\Entity;

use Lle\MailerBundle\Lib\Sender\SenderFactory;

use Doctrine\ORM\Mapping as ORM;

/**
 * Destinataire
 *
 * @ORM\Table(name="lle_mailer_destinataire")
 * @ORM\Entity(repositoryClass="Lle\MailerBundle\Entity\DestinataireRepository")
 */
class Destinataire
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="json_array")
     */
    private $data;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="datetime", nullable=true)
     */
    private $dateEnvoi;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_ouvert", type="datetime", nullable=true)
     */
    private $dateOuvert;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    private $url;

     /**
     * @ORM\ManyToOne(targetEntity="Mail",cascade={"persist"},inversedBy="destinataires")
     * @ORM\JoinColumn(name="mail_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $mail;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Destinataire
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }


    public function getDonnesVide(){
        $factory = new SenderFactory();
        $sender = $factory->getSender($this->getMail());
        return $sender->getColumnDestinataires($this->getData());
    }

    /**
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     * @return Destinataire
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }

    /**
     * Set dateOuvert
     *
     * @param \DateTime $dateOuvert
     * @return Destinataire
     */
    public function setDateOuvert($dateOuvert)
    {
        $this->dateOuvert = $dateOuvert;

        return $this;
    }

    /**
     * Get dateOuvert
     *
     * @return \DateTime
     */
    public function getDateOuvert()
    {
        $factory = new SenderFactory();
        $sender = $factory->getSender($this->getMail());
        return $sender->getDateOuvert($this);
    }

    public function gdateOuvert(){
        return $this->dateOuvert;
    }

    public function isDateOuvertDispo(){
        return !is_string($this->getDateOuvert());
    }

    public function isUrlDispo(){
        return !is_string($this->getUrl());
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Destinataire
     */
    public function setUrl($url)
    {
        $this->url = json_encode($url);

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        $factory = new SenderFactory();
        $sender = $factory->getSender($this->getMail());
        return $sender->getUrlDest($this);
    }

    public function gurl(){
        return json_decode($this->url,true);
    }

    public function addUrl($url){
        $urls = $this->getUrl();
        $urls[] = $url;
        $this->setUrl($urls);
    }

    /**
     * Set mail
     *
     * @param \Lle\MailerBundle\Entity\Mail $mail
     * @return Destinataire
     */
    public function setMail(Mail $mail = null)
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * Get mail
     *
     * @return \Lle\MailerBundle\Entity\Mail
     */
    public function getMail()
    {
        return $this->mail;
    }



    /**
     * Set data
     *
     * @param array $data
     * @return Destinataire
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
