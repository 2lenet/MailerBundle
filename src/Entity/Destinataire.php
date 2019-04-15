<?php
namespace Lle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lle\MailerBundle\Traits\DestinataireEntityTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Destinataire
 *
 * @ORM\Table(name="lle_mailer_destinataire")
 * @ORM\Entity(repositoryClass="Lle\MailerBundle\Entity\DestinataireRepository")
 */
class Destinataire implements DestinataireInterface
{
    use DestinataireEntityTrait;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Mail",cascade={"persist"},inversedBy="destinataires")
     * @ORM\JoinColumn(name="mail_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $mail;
}
