<?php
namespace Lle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Destinataire
 *
 * @ORM\Table(name="lle_mailer_destinataire")
 * @ORM\Entity(repositoryClass="Lle\MailerBundle\Entity\DestinataireRepository")
 */
class Destinataire
{

    /**
     *
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Assert\Email(
     *     checkMX = true
     * )
     */
    private $email;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="data", type="json")
     */
    private $data;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="datetime", nullable=true)
     */
    private $dateEnvoi;

    /**
     *
     * @var \DateTime
     *
     * @ORM\Column(name="date_ouvert", type="datetime", nullable=true)
     */
    private $dateOuvert;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="url", type="text", nullable=true)
     */
    private $url;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Mail",cascade={"persist"},inversedBy="destinataires")
     * @ORM\JoinColumn(name="mail_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $mail;

    /**
     *
     * @var bool
     *
     * @ORM\Column(name="success", type="boolean", nullable=false)
     */
    protected $success;

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

    public function getDateOuvert()
    {
        return $this->dateOuvert;
    }

    public function isDateOuvertDispo()
    {
        return ! is_string($this->getDateOuvert());
    }

    public function isUrlDispo()
    {
        return ! is_string($this->getUrl());
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

    public function getUrl()
    {
        return json_decode($this->url, true);
    }

    public function addUrl($url)
    {
        $urls = $this->getUrl();
        $urls[] = $url;
        $this->setUrl($urls);
    }

    /**
     * Set mail
     *
     * @param Mail $mail
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
     * @return Mail
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

    /**
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     *
     * @param bool $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     *
     * @param boolean $checkMX
     * @param boolean $checkHost
     * @return bool
     */
    public function isValidEmail($checkMX = false, $checkHost = false): bool
    {
        $emailValidator = new Email();
        $emailValidator->checkMX = $checkMX;
        $emailValidator->checkHost = $checkHost;
        $validator = Validation::createValidator();
        $violations = $validator->validate($this->email, array(
            $emailValidator
        ));

        if (0 !== count($violations)) {
            return false;
        }
        return true;
    }
}
