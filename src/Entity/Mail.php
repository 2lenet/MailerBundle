<?php

namespace Lle\MailerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Lle\MailerBundleBundle\lib\Api;
use Lle\MailerBundleBundle\lib\Sender\SenderFactory;

/**
 * Mail
 *
 * @ORM\Table(name="lle_mailer_mail")
 * @ORM\Entity(repositoryClass="Lle\MailerBundle\Entity\MailRepository")
 */
class Mail
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
     * @ORM\ManyToOne(targetEntity="Template", cascade={"persist"})
     * @ORM\JoinColumn(name="template_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    protected $template;

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
     * @ORM\OneToMany(targetEntity="Destinataire",mappedBy="mail", cascade={"persist"})
     */
    private $destinataires;

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
     * Constructor
     */
    public function __construct()
    {
        $this->destinataires = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set data
     *
     * @param string $data
     * @return Mail
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add destinataires
     *
     * @param \Lle\MailerBundle\Entity\Destinataire $destinataires
     * @return Mail
     */
    public function addDestinataire(Destinataire $destinataire)
    {
        $destinataire->setMail($this);
        $this->destinataires[] = $destinataire;
        return $this;
    }

    /**
     * Remove destinataires
     *
     * @param \Lle\MailerBundle\Entity\Destinataire $destinataires
     */
    public function removeDestinataire(Destinataire $destinataire)
    {
        $this->destinataires->removeElement($destinataire);
    }

    /**
     * Get destinataires
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDestinataires()
    {
        return $this->destinataires;
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
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     * @return Mail
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
     * Set envoye
     *
     * @param boolean $envoye
     * @return Mail
     */
    public function setEnvoye($envoye)
    {
        $this->envoye = $envoye;

        return $this;
    }

    /**
     * Get envoyer
     *
     * @return boolean
     */
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

    /**
     * Set sujet
     *
     * @param string $sujet
     * @return Mail
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
     * Set datePrevu
     *
     * @param \DateTime $datePrevu
     * @return Mail
     */
    public function setDatePrevu($datePrevu)
    {
        $this->datePrevu = $datePrevu;

        return $this;
    }

    /**
     * Get datePrevu
     *
     * @return \DateTime
     */
    public function getDatePrevu()
    {
        return $this->datePrevu;
    }

    /**
     * Set dateEnvoiFini
     *
     * @param \DateTime $dateEnvoiFini
     * @return Mail
     */
    public function setDateEnvoiFini($dateEnvoiFini)
    {
        $this->dateEnvoiFini = $dateEnvoiFini;

        return $this;
    }

    /**
     * Get dateEnvoiFini
     *
     * @return \DateTime
     */
    public function getDateEnvoiFini()
    {
        return $this->dateEnvoiFini;
    }

    /**
     * Set expediteur
     *
     * @param string $expediteur
     * @return Mail
     */
    public function setExpediteur($expediteur)
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    /**
     * Get expediteur
     *
     * @return string
     */
    public function getExpediteur()
    {
        return $this->expediteur;
    }

    /**
     * Set returnPath
     *
     * @param string $returnPath
     * @return Mail
     */
    public function setReturnPath($returnPath)
    {
        $this->returnPath = $returnPath;

        return $this;
    }

    /**
     * Get returnPath
     *
     * @return string
     */
    public function getReturnPath()
    {
        return $this->returnPath;
    }


    /**
     * Set etat
     *
     * @param string $etat
     * @return Mail
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string
     */
    public function getEtat()
    {
        return $this->etat;
    }


    /**
     * Set alias
     *
     * @param string $alias
     * @return Mail
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    public function nbDestinataire(){
        return count($this->getDestinataires());
    }
    /**
     * Set sender
     *
     * @param string $sender
     * @return Mail
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set infoSender
     *
     * @param string $infoSender
     * @return Mail
     */
    public function setInfoSender($infoSender)
    {
        $this->infoSender = $infoSender;

        return $this;
    }

    /**
     * Get infoSender
     *
     * @return string
     */
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

    /**
     * Set replyTo
     *
     * @param string $replyTo
     * @return Mail
     */
    public function setReplyTo($replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * Get replyTo
     *
     * @return string
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

}



class CallbackReplaceUrl{
    private $idMessage;
    private $idDestinataire;
    private $urlRedirect;

    public function __construct($idMessage,$idDestinataire,$urlRedirect){
        $this->idMessage  = $idMessage;
        $this->idDestinataire = $idDestinataire;
        $this->urlRedirect = $urlRedirect;
    }

    public function replaceUrl($matches){
        $idMessage = $this->idMessage;
        $idDestinataire = $this->idDestinataire;
        return 'href="'.$this->urlRedirect.'?url='.base64_encode($matches[1]).'"';
    }


}
