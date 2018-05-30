<?php

namespace Lle\MailerBundle\Tests\Helper;

use Lle\MailerBundle\Entity\Destinataire;
use Lle\MailerBundle\Entity\Mail;
use Lle\MailerBundle\Entity\Template;


use PHPUnit\Framework\TestCase;

class MailerTest extends TestCase
{

    public function testSimpleMailing()
    {
        $dest = new Destinataire();
	      $dest->setEmail('2le@2le.net');
	      $dest->setData('{"nom":"Sébastien"}');

        $template = new Template();
        $template->setSujet("Bonjour {{nom}}");
        $template->setHtml("<h1>Bonjour {{nom}}</h1> Vous allez bien ?");

        $mail = new Mail();
        $mail->setTemplate($template);
        $mail->addDestinataire($dest);

        $this->assertNotNull($dest);
        $this->assertNotNull($mail);
        $this->assertNotNull($template);
    }
}
