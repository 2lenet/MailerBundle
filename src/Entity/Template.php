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
class Template implements TemplateInterface
{
    use TemplateEntityTrait;
}
