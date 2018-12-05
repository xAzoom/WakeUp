<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 02.11.18
 * Time: 16:28
 */

namespace App\Tests;


use App\Entity\Account;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ApiTestCase extends KernelTestCase
{
    private static $staticClient;

    /**
     * @var Client
     */
    protected $client;

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

        self::bootKernel(['environment' => 'test']);
    }

    protected function tearDown()
    {
        // purposefully not calling parent class, which shuts down the kernel
    }

    protected function setUp(): void
    {
        $this->client = self::$staticClient;

        $this->purgeDatabase();
    }

    private function purgeDatabase(): void
    {
        $ORMpurge = new ORMPurger($this->getService('doctrine')->getManager());
        $ORMpurge->purge();
    }

    public function getService($id)
    {
        return self::$kernel->getContainer()->get($id);
    }
}