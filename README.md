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

If you want to display the emails / display,edit and show the templates / display the receivers you need the bundle **esayadminplus** and the different configuration files : 
https://packagist.org/packages/2lenet/easyadmin-plus-bundle


menu.yaml
```yaml
easy_admin:
    design:
        menu:
            -
                label: menu.accueil
                route: home
                icon: home
            -
                label: menu.echeance
                entity: Controle
                icon: bell-o
            -
                label: menu.ressource
                url: '/service.core/ressources?menuIndex=2&submenuIndex=0'
                icon: clone
            -
                label: menu.obligation
                entity: Obligation
                icon: bell
            -
                label: menu.suivi
                icon: wrench
                children:
                    - { entity: Rapport, label: menu.rapport, icon: file-text }
                    - { entity: Ticket, label: ticket.liste.nom, icon: ticket }
                    - { label: menu.import_rapport, url: 'http://rapport.nathyslog.com', icon: sign-in }
            -
                label: menu.parametrage
                icon: gear
                children:
                    - { entity: Service, label: menu.service, icon: sitemap }
                    - { entity: Gabarit, label: menu.categorie_ressource }
                    - { entity: Responsableobligation, label: menu.responsableobligation, icon: users }
                    - { entity: Rubrique, label: menu.rubrique, icon: cubes }
                    - { entity: Attributserviceclient, label: menu.attribut_service, icon: crosshairs }
                    - { entity: TypeTicket, label: typeTicket.liste.nom, icon: list }
                    - { entity: Priorite, label: priorite.liste.nom, icon: sort }
            -
                label: menu.admin_generale
                icon: cogs
                children:
                    - { entity: Client, label: menu.client, default: true }
                    - { entity: Categorie, label: menu.categorie }
                    - { entity: Typegabarit, label: menu.type_categorie_ressource }
                    - { entity: Typerapport, label: menu.type_rapport }
                    - { entity: Typeresponsabilite, label: menu.typeresponsabilite }
                    - { entity: Theme, label: menu.theme }
                    - { entity: Typecontrole, label: menu.typecontrole }
                    - { entity: Template, label: menu.template }
                    - { entity: Mail, label: menu.mail }
```


Mail.yaml
``` yaml
    easy_admin:
    entities:
        Mail:
            class: Lle\MailerBundle\Entity\Mail
            disabled_actions: [delete, new, edit]
            list:
                title: title.mail.list
                actions:
                    - { name: new, icon: add }
                    - { name: show, icon: search }
                    - { name: edit, icon: edit }
                    - { name: delete, icon: trash }
                fields:
                    #- { property: id, label: field.id }
                    - { property: sujet, label: field.sujet }
                    - { property: expediteur, label: field.expediteur, type: email }
                    - { property: template, label: field.template }
                    - { property: dateEnvoi, label: field.dateEnvoi }
                    - { property: dateEnvoiFini, label: field.dateEnvoiFini }
                    - { property: datePrevu, label: field.datePrevu }
                    - { property: envoye, label: field.envoye, type: boolean }
                sort:
                    - dateEnvoi
                    - DESC
            show:
                title: title.mail.show
                actions:
                    - { name: edit, icon: edit }
                    - { name: delete, icon: trash }
                fields:
                    #- { property: id, label: field.id }
                    - { property: sujet, label: field.sujet }
                    - { property: data, label: field.data }
                    - { property: template, label: field.template }
                    - { property: dateEnvoi, label: field.dateEnvoi }
                    - { property: dateEnvoiFini, label: field.dateEnvoiFini }
                    - { property: datePrevu, label: field.datePrevu }
                    - { property: envoye, label: field.envoye, type: boolean }
                    - { property: expediteur, label: field.expediteur, type: email }
                    - { property: replyTo, label: field.replyTo, type: email }
                    - { property: returnPath, label: field.returnPath, type: email }
                    - { property: alias, label: field.alias }
                    - { property: sender, label: field.sender }
                    - { property: infoSender, label: field.infoSender }
                    - { type: 'sublist', id: 'destinataires', label: 'tab.destinataires', entity: 'Destinataire', property: 'Destinataires'}                    
```

Template.yaml
``` yaml
easy_admin:
    entities:
        Template:
            class: Lle\MailerBundle\Entity\Template
            disabled_actions: []
            list:
                title: title.template.list
                actions:
                    - { name: new, icon: add }
                    - { name: show, icon: search }
                    - { name: edit, icon: edit }
                    - { name: delete, icon: trash }
                fields:
                    #- { property: id, label: field.id }
                    - { property: sujet, label: field.sujet }
                    - { property: code, label: field.code }
                    - { property: expediteurMail, label: field.expediteurMail, type: email }
                    - { property: expediteurName, label: field.expediteurName }
                sort:
                    - id
                    - DESC
            show:
                title: title.template.show
                actions:
                    - { name: edit, icon: edit }
                    - { name: delete, icon: trash }
                fields:
                    #- { property: id, label: field.id }
                    - { property: html, label: field.html, type: raw }
                    - { property: text, label: field.text, type: raw }
                    - { property: sujet, label: field.sujet }
                    - { property: code, label: field.code }
                    - { property: expediteurMail, label: field.expediteurMail, type: email }
                    - { property: expediteurName, label: field.expediteurName }
            edit:
                title: title.template.edit
                actions: []
                fields:
                    - { property: sujet, label: field.sujet }
                    - { property: expediteurMail, label: field.expediteurMail, help: 'The e-mail address must include an @ followed by a domain name (ex: john@free.fr).' }
                    - { property: expediteurName, label: field.expediteurName }
                    - { property: code, label: field.code }
                    - { property: text, label: field.text }                    
                    - { property: html, label: field.html }     
            new:
                title: title.template.new
                actions: []
                fields:
                    - { property: sujet, label: field.sujet }
                    - { property: expediteurMail, label: field.expediteurMail, help: 'The e-mail address must include an @ followed by a domain name (ex: john@free.fr).' }
                    - { property: expediteurName, label: field.expediteurName }
                    - { property: code, label: field.code }
                    - { property: text, label: field.text }                    
                    - { property: html, label: field.html }                                        
```

Destinataire.yaml
```yaml 
easy_admin:
    entities:
        Destinataire:
            class: Lle\MailerBundle\Entity\Destinataire
            disabled_actions: []
            list:
                title: title.destinataire.list
                actions:
                    - { name: new, icon: add }
                    - { name: show, icon: search }
                    - { name: edit, icon: edit }
                    - { name: delete, icon: trash }
                fields:
                    - { property: email, label: field.email }
                    - { property: data, label: field.data, type: json }
                    - { property: dateEnvoi, label: field.dateEnvoi }
                    - { property: dateOuvert, label: field.dateOuvert }
                sort:
                    - id
                    - DESC
            show:
                title: title.destinataire.show
                actions:
                    - { name: edit, icon: edit }
                    - { name: delete, icon: trash }
                fields:
                    - { property: id, label: field.id }
                    - { property: email, label: field.email }
                    - { property: data, label: field.data, type: collection }
                    - { property: dateEnvoi, label: field.dateEnvoi }
                    - { property: dateOuvert, label: field.dateOuvert }
                    - { property: url, label: field.url, type: raw }
                    - { property: mail, label: field.mail }
```
                   
