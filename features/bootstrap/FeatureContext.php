<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\MinkExtension\Context\MinkContext;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as DataFixturesLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Behat\Behat\Context\TranslatableContext;
use Behat\Gherkin\Node\TableNode;
//use Behat\Behat\Context\Step\Then;
//use Behat\Behat\Context\Step\When;
//use Behat\Behat\Context\Step\Given;


/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext, KernelAwareContext
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $manager;

    private $client;

    private $client_id;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->doctrine = $container->get('doctrine'); //doctrine;
        $this->kernel = $container->get('kernel');
        $this->manager = $this->doctrine->getManager();
        $this->schemaTool = new SchemaTool($this->manager);
        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     */
    public function setKernel(\Symfony\Component\HttpKernel\KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createDatabase()
    {
        $this->schemaTool->createSchema($this->classes);
    }

    /**
     * @AfterScenario @dropSchema
     */
    public function dropDatabase()
    {
        $this->schemaTool->dropSchema($this->classes);
    }

    /**
     * @Given I have fixtures loaded from :folder
     */
    public function loadFixtures($folder = null)
    {
        $em = $this->manager;
        $container = $this->container;

        $loader = new DataFixturesLoader($container);

            foreach ($container->get('kernel')->getBundles() as $bundle) {
                $paths[] = $bundle->getPath().'/DataFixtures/ORM'.$folder;
            }
            foreach ($paths as $path) {
                if (is_dir($path)) {
                    $loader->loadFromDirectory($path);
                }
            }
        $fixtures = $loader->getFixtures();
        if (!$fixtures) {
            throw new InvalidArgumentException(
                sprintf('Could not find any fixtures to load in: %s', "\n\n- ".implode("\n- ", $paths))
            );
        }

        $purger = new ORMPurger($em);
        $executor = new ORMExecutor($em, $purger);
        $executor->execute($fixtures, InputOption::VALUE_NONE);
    }

    /**
     * @Given I load all fixtures
     */
    public function loadAllFixtures()
    {
        $kernel = $this->container->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

//        $path = $kernel->getRootDir().'/../src/'.$folder;

        $input = new ArrayInput(array(
            'command' => 'doctrine:fixtures:load',
//            '--fixtures' => $path,
            '--append' => null,
        ));
        $output = new NullOutput();
        $application->run($input, $output);

        return;
    }


    /**
     * generate client with params
     * Example: Given I generate client with:
            | name | Store |
            | redirectUri | http://majdi.com:8085/ |
            | grantTupe | token |
     * @Given /^(?:|I )generate client with:$/
     */
    public function iGenerateClientWith(TableNode $params)
    {
        $clientParams = [];
        foreach ($params->getRowsHash() as $param => $value) {
            $clientParams[$param] = $value;
        }
        $client = $this->container->get('wbi_api_user.services.oauth2generate_client')->generateClient(
            $clientParams['name'],
            $clientParams['redirectUri'],
            $clientParams['grantType']
        );

        $this->setClient($client);
        $this->setClientId($client->getPublicId());

        echo sprintf("The client %s was created with \n %s as public id \n and %s as secret",
            $client->getName(),
            $client->getPublicId(),
            $client->getSecret()
        );
    }



    /**
     * @When I try authenticate in :arg1
     */
    public function iTryAuthenticateIn($arg1)
    {
        $client_id = $this->client_id;

        $table = new Behat\Gherkin\Node\TableNode([

        ]);

        return new When("I send a GET request to \"/admin/oauth/v2/auth\" with parameters:". $table);
    }





    /**
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param mixed $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * @param mixed $client_id
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;
    }



}