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

function mailAction(MailerManager $mailerManager) {

        $destinataires = array(); // array of dest.
        
        $email = '2le@2le.net';
        $data = '{"nom":"Sébastien"}'; // data is json string
        $dest = $mailerManager->createDestinataire('2le@2le.net', $data);
        $destinataires[] = dest; // add one dest to array

        //  create($code, $destinataires, $expediteur = ['2le' => '2le@2le.net'], $returnPath = null)
        
        $mail = $mailerService->create('CODE_MODELE', $destinataires); // create mail for all destinataires
        $mailService->send($mail);  // send the mail
}

```
