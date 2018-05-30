<?php

namespace  Lle\MailerBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Model
 *
 * @ORM\Table(name="lle_mailer_template")
 * @ORM\Entity(repositoryClass="Lle\MailerBundle\Entity\TemplateRepository")
 */
class Template {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string
     *
     * @ORM\Column(name="html", type="text", nullable=true)
     */
    protected $html;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    protected $text;

    /**
     * @var string
     *
     * @ORM\Column(name="sujet", type="string")
     */
    private $sujet;

    /**
     * @ORM\Column(name="code", type="string", nullable=true)
     */
    protected $code;

    public function __toString()
    {
        return (string)$this->getSujet();
    }

    public function render($destinataire = null){
        $destinataire = ($destinataire)? $destinataire:$this->destinataires->first();
        return $this->twig($this->getHtml(),$this->getAllData($destinataire));
    }

    public function renderPlainText($destinataire = null){
        $destinataire = ($destinataire)? $destinataire:$this->destinataires->first();
        $text = "";
        if ($this->getTemplate()) {
            $text = $this->getTemplate()->getText();
        }
        return $this->twig($text, $this->getAllData($destinataire));
    }

    public function renderSujet($destinataire = null){
        $destinataire = ($destinataire)? $destinataire:$this->destinataires->first();
        return $this->twig($this->getSujet(),$this->getAllData($destinataire));
    }

    private function twig($html,$vars){
        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);
        try{
            $texte = $twig->render($html,$vars);
        }catch(\Exception $e){
            $this->error = $e->getMessage();
            $texte = false;
        }
        return $texte;
    }

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
     * Set html
     *
     * @param string $html
     * @return Template
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get html
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Template
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set sujet
     *
     * @param string $sujet
     * @return Template
     */
    public function setSujet($sujet)
    {
        $this->sujet = $sujet;

        return $this;
    }

    /**
     * Get sujet
     *
     * @return string
     */
    public function getSujet()
    {
        return $this->sujet;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Template
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
