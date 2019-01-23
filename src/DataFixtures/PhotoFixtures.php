<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 15.01.19
 * Time: 22:27
 */

namespace App\DataFixtures;


use App\Entity\Photo;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;

class PhotoFixtures extends BaseFixtures implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(1, 'photo', function ($i) {
            $photo = new Photo();
            $photo->setId(Uuid::uuid4()->toString());
            $photo->setFormat('png');
            $photo->setOwner($this->getReference('account_0'));

            return $photo;
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            AccountFixtures::class,
        ];
    }
}