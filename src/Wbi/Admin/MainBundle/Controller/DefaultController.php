<?php

namespace Wbi\Admin\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{

    public function indexAction()
    {
        return $this->render('WbiAdminMainBundle:Default:index.html.twig');
    }


    public function selectAppAction()
    {
        $om = $this->getDoctrine()->getManager();
        $apps = $om->getRepository("WbiApiUserBundle:Client")->findAll();
        return $this->render('WbiAdminMainBundle:Default:select_app.html.twig', [
            'apps' => $apps
        ]);
    }
}
