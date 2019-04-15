<?php

namespace Lle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Lle\MailerBundle\Traits\MailEntityTrait;
/**
 * Mail
 *
 * @ORM\Table(name="lle_mailer_mail")
 * @ORM\Entity(repositoryClass="Lle\MailerBundle\Entity\MailRepository")
 */
class Mail implements MailInterface
{
    use MailEntityTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Template", cascade={"persist"})
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $template;
    
    /**
     * @ORM\OneToMany(targetEntity="Lle\MailerBundle\Entity\Destinataire",mappedBy="mail", cascade={"persist"})
     */
    private $destinataires;
}
