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
    function index(MailerManager $mailerManager) {

        $destinataires = [
            '2le@2le.net' => ["nom" =>"Sébastien"],
            'jules@2le.net' => ["nom" =>"Jules"]
        ];
        
        $mail = $mailerManager->create('TEST', $destinataires); // create mail for all destinataires
        $mailerManager->send($mail);  // send the mail
    }    

```
