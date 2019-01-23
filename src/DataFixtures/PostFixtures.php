<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 16.01.19
 * Time: 00:04
 */

namespace App\DataFixtures;


use App\Entity\Post;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PostFixtures extends BaseFixtures implements DependentFixtureInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(1, 'post', function ($i) {
            $post = new Post();
            $post->setTitle('title_'.$i)
            ->setContent('content_'.$i)
            ->setCategory($this->getReference('category_0'))
            ->setPhoto($this->getReference('photo_0'))
            ->setPhotoLink($this->container->getParameter('images_host').$this->getReference('photo_0')->getLink())
            ->setAuthor($this->getReference('account_0'));
            return $post;
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            PhotoFixtures::class,
            CategoryFixtures::class,
            AccountFixtures::class,
        ];
    }
}