# MailerBundle

### Requirements

* PHP >= 7.1
* Symfony 4

### Install

```shell
$ composer require 2lenet/mailer-bundle
```


### Usage

```php

        $configs = ['titre' => '$alerte->getTitre(), 'contenu' => $alerte->getContenu()];
  	    $userList = ['2le@2le.net','jules@2le.net'];

        $destinataires = [];
        foreach ($userList as $user) {
            $destinataires[$user] = $configs;
        }
        $mailBundle = $this->get('lle_mail_manager');
        $mail = $mailBundle->create($alerte->getCodeTemplateMail(), $destinataires, ["Soléa" => "noreply@solea.info"]);
        $mailBundle->send($mail);
```
