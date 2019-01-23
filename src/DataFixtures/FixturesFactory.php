<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 15.01.19
 * Time: 23:03
 */

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FixturesFactory
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var ReferenceRepository
     */
    private $referenceRepository;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        ContainerInterface $container
    )
    {
        $this->passwordEncoder = $passwordEncoder;

        $this->referenceRepository = new ReferenceRepository($entityManager);
        $this->container = $container;
    }

    public function createFixture(string $type): BaseFixtures
    {
        switch ($type) {
            case AccountFixtures::class:
                $fixture = new AccountFixtures($this->passwordEncoder);
                break;
            case PostFixtures::class:
                $fixture = new PostFixtures($this->container);
                break;
            case CategoryFixtures::class:
            case PhotoFixtures::class:
                $fixture = new $type();
                break;
            default:
                $fixture = null;
        }

        $fixture->setReferenceRepository($this->referenceRepository);

        return $fixture;
    }
}