<?php

namespace Lle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Lle\MailerBundle\Traits\TemplateEntityTrait;

/**
 * Model
 *
 * @ORM\Table(name="lle_mailer_template")
 * @ORM\Entity(repositoryClass="Lle\MailerBundle\Entity\TemplateRepository")
 */
class Template
{
    use TemplateEntityTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

}
