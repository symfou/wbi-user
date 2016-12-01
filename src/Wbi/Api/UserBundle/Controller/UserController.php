<?php

namespace Wbi\Api\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\RestBundle\Controller\FOSRestController;

class UserController extends FOSRestController
{


    public function checkValiditiesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $token = $this->get('security.token_storage')->getToken();
        $access_token = $token->getToken();
        //TODO A voir comment avoir accès à cet objet autrement (temps de chargement)
        $access_token_objet = $em->getRepository("WbiApiUserBundle:AccessToken")->findOneBy(['token' => $access_token]);
        $validityResponse = [
            'access_token' => $access_token,
            'client_id' => $access_token_objet->getClientId(),
            'expires_in' => $access_token_objet->getExpiresIn(),
            'user_id' => $token->getUser()->getId(),
            'scope' => $access_token_objet->getScope() ? $access_token_objet->getScope() : ''
        ];

        $view = $this->view($validityResponse, 200);
        return $this->handleView($view);
    }


    





}
