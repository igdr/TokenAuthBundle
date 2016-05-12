Token Auth Bundle
========================
Installation
------------

Add the bundle to your `composer.json`:

    "igdr/token-auth-bundle" : "dev-master"

and run:

    php composer.phar update

Then add the IgdrEmployeeBundle to your application kernel:

    // app/AppKernel.php
    public function registerBundles()
    {
        return array(
            // ...
            new Igdr\Bundle\TokenAuthBundle\IgdrTokenAuthBundle(),
            // ...
        );
    }

config.yml

    doctrine:
        orm:
            ....
            resolve_target_entities:
                Symfony\Component\Security\Core\User\UserInterface: Enadvance\Bundle\UserBundle\Entity\User

securiry.yml
    security:
        firewalls:
            api_token_secured:
                pattern:  ^/api/.*
                stateless:    true
                api_token: true
                anonymous: true


console:
    php app/console doctrine:schema:update --force