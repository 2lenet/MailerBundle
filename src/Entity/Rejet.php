<?php

namespace Lle\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Destinataire
 *
 * @ORM\Table(name="lle_mail_rejet")
 * @ORM\Entity(repositoryClass="Lle\MailBundle\Entity\RejetRepository")
 */
class Rejet {

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
     * @ORM\Column(name="contenu", type="blob", length=255)
     */
    private $contenu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getContenu() {
        return $this->contenu;
    }

    public function getDate() {
        return $this->date;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setContenu($contenu) {
        $this->contenu = $contenu;
    }

    public function setDate(\DateTime $date) {
        $this->date = $date;
    }

}
