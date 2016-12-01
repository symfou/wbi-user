<?php

namespace Wbi\Api\ProductBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Wbi\Api\UserBundle\Entity\User;

/**
 * Class LoadProductData
 *
 * @category
 * @author Boumaiza Majdi <boumaiza.majdi@mail.com>
 */
class LoadUserData implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        //generate cleint
//        $this->container->get('wbi_api_user.services.oauth2generate_client')->generateClient("4lit6kyrv86cwgw8cc0c8ogwgws0oookk0o40kccww840s00wc", "30wn0vfonj0g0wgckw4okggk88w8888o0c4o8kgksw08kgww4s");

        $encoder = $this->container->get('security.password_encoder');

        $user1 = new User();
        $user1->setUsername('admin')
            ->setEmail("boumaiza.majdi@gmail.com")
            ->setEmailCanonical("boumaiza.majdi@gmail.com")
            ->setRoles(array('ROLE_API', 'ROLE_ADMIN'))
            ->setEnabled(true);
//        $user->setSalt(md5(uniqid()));
        $password = $encoder->encodePassword($user1, 'admin');
        $user1->setPassword($password);

        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('majdi')
            ->setEmail("boumaiza.majdi1@gmail.com")
            ->setEmailCanonical("boumaiza.majdi1@gmail.com")
            ->setRoles(array('ROLE_API', 'ROLE_ADMIN'))
            ->setEnabled(true);
//        $user->setSalt(md5(uniqid()));
        $password = $encoder->encodePassword($user2, 'majdi');
        $user2->setPassword($password);

        $manager->persist($user2);
        $manager->flush();
    }


    public function getOrder()
    {
        return 2;
    }

}