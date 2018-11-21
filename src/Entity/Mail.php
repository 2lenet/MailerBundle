<?php

namespace Lle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Lle\MailerBundle\Traits\MailEntityTrait;
use Lle\MailerBundle\MailInterface;

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
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="Template", cascade={"persist"})
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $template;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setTemplate(Template $template)
    {
        $this->html = $template->getHtml();
        $this->sujet = $template->getSujet();
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \Lle\MailerBundle\Entity\Template
     */
    public function getTemplate()
    {
        return $this->template;
    }


}
