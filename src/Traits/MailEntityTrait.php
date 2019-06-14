<?php

namespace Lle\MailerBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Lle\MailerBundle\Entity\DestinataireInterface;
use Lle\MailerBundle\Entity\Template;
use Lle\MailerBundle\Entity\TemplateInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Lle\MailerBundle\Entity\Destinataire;


trait MailEntityTrait
{

    /**
     * @var string
     *
     * @ORM\Column(name="html", type="text")
     */
    private $html;

    /**
     * @var string
     *
     * @ORM\Column(name="sujet", type="string")
     */
    private $sujet;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="json_array", nullable=true)
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
     * @ORM\Column(name="date_envoi_fini", type="datetime", nullable=true)
     */
    private $dateEnvoiFini;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_prevu", type="datetime", nullable=true)
     */
    private $datePrevu;

    /**
     * @var boolean
     *
     * @ORM\Column(name="envoye", type="boolean", nullable=true)
     */
    private $envoye = false;


    /**
     * @var string
     * @Assert\Email(
     *     message = "'{{ value }}' n'est pas un email valide.",
     *     checkMX = true
     * )
     * @ORM\Column(name="expediteur", type="text", nullable=true)
     */
    private $expediteur;


    /**
     * @var string
     * @Assert\Email(
     *     message = "'{{ value }}' n'est pas un email valide.",
     *     checkMX = true
     * )
     * @ORM\Column(name="reply_to", type="text", nullable=true)
     */
    private $replyTo;

    /**
     * @var string
     * @Assert\Email(
     *     message = "'{{ value }}' n'est pas un email valide.",
     *     checkMX = true
     * )
     * @ORM\Column(name="return_path", type="text", nullable=true)
     */
    private $returnPath;

    /**
     * @var string
     *
     * @ORM\Column(name="alias_expediteur", type="string", nullable=true)
     */
    private $alias;

    /**
     * @var string
     *
     * @ORM\Column(name="sender", type="string", nullable=true)
     */
    protected $sender;


    /**
     * @var string
     *
     * @ORM\Column(name="info_sender", type="json_array", nullable=true)
     */
    protected $infoSender;

    /**
     * @var string
     *
     * @ORM\Column(name="attachments", type="json_array", nullable=true)
     */
    protected $attachments = [];

    protected $streamAttachments = [];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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

    public function setTemplate(TemplateInterface $template)
    {
        $this->html = $template->getHtml();
        $this->sujet = $template->getSujet();
        $this->template = $template;

        return $this;
    }


    public function getTemplate()
    {
        return $this->template;
    }


    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }


    public function getData()
    {
        return $this->data;
    }

    public function addDestinataire(DestinataireInterface $destinataire)
    {
        $destinataire->setMail($this);
        $this->destinataires[] = $destinataire;
        return $this;
    }


    public function removeDestinataire(DestinataireInterface $destinataire)
    {
        $this->destinataires->removeElement($destinataire);
    }


    public function getDestinataires()
    {
        return $this->destinataires;
    }

    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }


    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }


    public function setEnvoye($envoye)
    {
        $this->envoye = $envoye;

        return $this;
    }


    public function getEnvoye()
    {
        return $this->envoye;
    }

    public function rewriteUrl($destinataire = null,$urlDeRedirection, $html = null){
        $destinataire = ($destinataire)? $destinataire:$this->destinataires->first();
        $callback = new CallbackReplaceUrl($this->getId(),$destinataire->getId(),$urlDeRedirection);
        $html = preg_replace_callback('#href="(http[^"]+)"#',array($callback, 'replaceUrl'), ($html)? $html:$this->getHtml());
        return $html;
    }


    public function getAllData($destinataire){
        $data = $this->getData();
        if ($destinataire) {
            $data['contact'] = $destinataire->getData();
        }
        return $data;
    }


    public function setSujet($sujet)
    {
        $this->sujet = $sujet;

        return $this;
    }


    public function getSujet()
    {
        return $this->sujet;
    }


    public function setDatePrevu($datePrevu)
    {
        $this->datePrevu = $datePrevu;

        return $this;
    }


    public function getDatePrevu()
    {
        return $this->datePrevu;
    }

    public function setDateEnvoiFini($dateEnvoiFini)
    {
        $this->dateEnvoiFini = $dateEnvoiFini;

        return $this;
    }

    public function getDateEnvoiFini()
    {
        return $this->dateEnvoiFini;
    }

    public function setExpediteur($expediteur)
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    public function getExpediteur()
    {
        return $this->expediteur;
    }

    public function setReturnPath($returnPath)
    {
        $this->returnPath = $returnPath;

        return $this;
    }

    public function getReturnPath()
    {
        return $this->returnPath;
    }

    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    public function getEtat()
    {
        return $this->etat;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function nbDestinataire(){
        return count($this->getDestinataires());
    }

    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    public function getSender()
    {
        return $this->sender;
    }

    public function setInfoSender($infoSender)
    {
        $this->infoSender = $infoSender;

        return $this;
    }

    public function getInfoSender()
    {
        return $this->infoSender;
    }

    public function valide(){
        $factory = new SenderFactory($this);
        $sender =  $factory->getSender($this);
        $sender->send($this);
    }

    public function getInfo(){
        $factory = new SenderFactory($this);
        $sender = $factory->getSender($this);
        return $sender->info($this);
    }

    public function getTracking(){
        $factory = new SenderFactory($this);
        $sender = $factory->getSender($this);
        return $sender->urlTracking($this);
    }

    public function getStatistique(){
        $factory = new SenderFactory($this);
        $sender = $factory->getSender($this);
        return $sender->statistique($this);
    }

    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    public function getReplyTo()
    {
        return $this->replyTo;
    }


    public function getHtml()
    {
        return $this->html;
    }
    
    public function setHtml(string $html)
    {
        $this->html = $html;

        return $this;
    }

    public function getAttachments(){
        return $this->attachments;
    }

    public function addAttachment(string $attach){
        $this->attachments[] = $attach;
        return $this;
    }

    /**
     * @return array
     */
    public function getStreamAttachments (): array
    {
        return $this->streamAttachments;
    }

    /**
     * @param array $streamAttachments
     */
    public function setStreamAttachments (array $streamAttachments): void
    {
        $this->streamAttachments = $streamAttachments;
    }

    public function addStreamAttachments (String $data, String $filename, String $contentType): void
    {
        $this->streamAttachments[] = [
            "data" => $data,
            "filename" => $filename,
            "contentType" => $contentType,
        ];
    }

    public function removeAllAttachments () {
        $this->streamAttachments = [];
    }
}

