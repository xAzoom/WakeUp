<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 02.11.18
 * Time: 16:28
 */

namespace App\Tests;

use App\DataFixtures\AccountFixtures;
use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\PhotoFixtures;
use App\DataFixtures\PostFixtures;
use App\Entity\Account;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ApiTestCase extends KernelTestCase
{
    private static $staticClient;

    private $responseAsserter;

    /**
     * @var Client
     */
    protected $client;

    private $token;

    protected $fixtures = [];

    private static $fixturesFactory = [];

    public static function setUpBeforeClass()
    {
        $base_url = getenv('TEST_BASE_URL');

        self::$staticClient = new Client([
            'base_uri' => $base_url,
            'http_errors' => false,
            'defaults' => [
                'extensions' => false,
            ],
        ]);

        self::$staticClient->post('api/login');
        self::bootKernel(['environment' => 'test']);

        $fixtures = [AccountFixtures::class, CategoryFixtures::class, PhotoFixtures::class, PostFixtures::class];
        $factory = self::$kernel->getContainer()->get('fixtures.factory');
        foreach ($fixtures as $fixture) {
            self::$fixturesFactory[$fixture] = $factory->createFixture($fixture);
        }
    }

    protected function tearDown()
    {
    }

    protected function setUp(): void
    {
        $this->client = self::$staticClient;
        $this->fixtures = self::$fixturesFactory;
        $this->purgeDatabase();
    }

    protected function getAuthorizedHeaders(array $headers = []): array
    {
        if (!$this->getEntityManager()->getRepository(Account::class)->findBy(['username' => 'account_0'])) {
            $this->loadFixture(AccountFixtures::class);
        }

        $token = $this->getService('lexik_jwt_authentication.encoder')
            ->encode(['username' => "account_0"]);

        $headers['Authorization'] = 'Bearer ' . $token;

        return $headers;
    }

    private function purgeDatabase(): void
    {
        $ORMpurge = new ORMPurger($this->getService('doctrine')->getManager());
        $ORMpurge->purge();
    }

    protected function getService($id)
    {
        return self::$kernel->getContainer()->get($id);
    }

    protected function getParameter($param)
    {
        return self::$kernel->getContainer()->getParameter($param);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

    protected function loadFixture(string $className)
    {
        if (!isset($this->fixtures[$className])) {
            $factory = $this->getService('fixtures.factory');
            $this->fixtures[$className] = $factory->createFixture($className);
        }

        $fixture = $this->fixtures[$className];
        $fixture->load($this->getEntityManager());
    }

    /**
     * @return ResponseAsserter
     */
    protected function asserter()
    {
        if ($this->responseAsserter === null) {
            $this->responseAsserter = new ResponseAsserter();
        }
        return $this->responseAsserter;
    }
}