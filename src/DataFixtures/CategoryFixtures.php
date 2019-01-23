<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CategoryFixtures extends BaseFixtures
{
    public function loadData(ObjectManager $manager)
    {
        $this->createMany(1, 'category', function ($i) {
            $category = new Category();
            $category->setName('category_'.$i);

            return $category;
        });
        $manager->flush();
    }
}
