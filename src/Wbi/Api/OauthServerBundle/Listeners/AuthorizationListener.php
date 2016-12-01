<?php
/**
 * Class AuthorizationListener
 *
 * @category
 * @package Wbi\Api\OauthServerBundle\Listeners
 * @author Boumaiza Majdi <boumaiza.majdi@mail.com>
 */


namespace Wbi\Api\OauthServerBundle\Listeners;


use OAuth2\OAuth2;
use OAuth2\OAuth2AuthenticateException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Wbi\Api\UserBundle\Entity\ClientHasUser;

class AuthorizationListener
{
    private $om;

    /**
     * AuthorizationListener constructor.
     * @param $authorizedClients
     */
    public function __construct($om)
    {
        $this->om = $om; 
    }

    public function preAutorization($event){
//        dump($event->getClient()->getRandomId());die();
        /*if (in_array($event->getClient()->getRandomId(), $this->authorizedClients)){
            $event->setAuthorizedClient(true);
        }*/
        $clientHasUser = $this->om->getRepository("WbiApiUserBundle:ClientHasUser")->findOneBy([
            'client' => $event->getClient(),
            'user' => $event->getUser()
        ]);

        if ($clientHasUser instanceof ClientHasUser){
            $event->setAuthorizedClient(true);
        }else{
            $event->setAuthorizedClient(false);
            throw new AccessDeniedException('Applaction Access Denied.');
        }

    }
}