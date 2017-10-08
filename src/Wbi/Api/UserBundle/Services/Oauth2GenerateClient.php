<?php

namespace Wbi\Api\UserBundle\Services;

use Wbi\Api\UserBundle\Entity\ClientHasUser;
/**
 * Class Oauth2GenerateClient
 *
 * @category
 * @author Boumaiza Majdi <boumaiza.majdi@mail.com>
 */
class Oauth2GenerateClient
{

    private $container;

    /**
     * Oauth2GenerateClient constructor.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }


    public function generateClient($name, $redirectUri, $grantType){
        $container = $this->container;

//        $oauthServer = $container->get('fos_oauth_server.server');

        $clientManager = $container->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setName($name);
        $client->setRedirectUris([$redirectUri]);
        $client->setAllowedGrantTypes([$grantType]);
        $clientManager->updateClient($client);


        $users = $container->get('doctrine')->getRepository('WbiApiUserBundle:User')->findAll();

        foreach ($users as $user){
            $clientHasUser = new ClientHasUser();
            $clientHasUser->setClient($client)
                ->setUser($user)
            ;
            $container->get('doctrine')->getManager()->persist($clientHasUser);
        }

        $container->get('doctrine')->getManager()->flush();

        return $client;

        /*foreach ($customers as $customer) {
            $queryData = [];
            $queryData['client_id'] = $client->getPublicId();
            $queryData['redirect_uri'] = $client->getRedirectUris()[0];
            $queryData['response_type'] = 'code';
            $authRequest = new Request($queryData);

            $oauthServer->finishClientAuthorization(true, $customer, $authRequest, $grantType);

            $output->writeln(sprintf("<info>Customer <comment>%s</comment> linked to client <comment>%s</comment></info>",
                $customer->getId(),
                $client->getName()));
        }*/
    }
}