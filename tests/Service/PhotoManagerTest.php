<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 20.01.19
 * Time: 21:10
 */

namespace App\Tests\Service;


use App\DataFixtures\AccountFixtures;
use App\Entity\Photo;
use App\Service\PhotoManager;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PhotoManagerTest extends KernelTestCase
{
    private $file;

    /** @var AccountFixtures */
    private $fixture;

    /** @var \Doctrine\ORM\EntityManager */
    private $entityManager;

    /** @var PhotoManager */
    private $photoManager;

    protected function setUp()
    {
        self::bootKernel(['environment' => 'test']);
        $this->entityManager = self::$container->get('doctrine')->getManager();
        $this->photoManager = self::$container->get(PhotoManager::class);
        $this->file = tempnam(sys_get_temp_dir(), 'upl');
        \imagepng(imagecreatetruecolor(10, 10), $this->file);

        $this->loadFixture(AccountFixtures::class);
    }

    protected function tearDown()
    {
        $ORMpurge = new ORMPurger(self::$container->get('doctrine')->getManager());
        $ORMpurge->purge();

        array_map('unlink', array_filter((array)glob(self::$container->getParameter("images_host") . '/*')));
        unlink($this->file);
    }

    /**
     * @dataProvider uploadPhotoProvider
     */
    public function testUploadPhoto(bool $onlyId, \Closure $isFile)
    {
        $file = new UploadedFile($this->file, 'new_file.png', 'image/png', null, true);
        $photo = new Photo();
        $photo->setImage($file);
        $photo->setOwner($this->fixture->getReference('account_0'));

        $img = $this->photoManager->uploadPhoto($photo, $onlyId);

        $this->assertTrue($isFile($img, 'png'));
        $photos = $this->entityManager->getRepository(Photo::class)->findBy(['id' => $photo->getId()]);
        $this->assertCount(1, $photos);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUploadWithoutOwner()
    {
        $file = new UploadedFile($this->file, 'new_file.png', 'image/png', null, true);
        $photo = new Photo();
        $photo->setImage($file);

        $img = $this->photoManager->uploadPhoto($photo, true);
    }

    public function uploadPhotoProvider()
    {
        return [
            "upload file with onlyId param" => [
                true,
                function ($link, $format) {
                    return is_file(self::$container->getParameter("images_host") . $link . '.' . $format);
                }
            ],
            "upload file without onlyId param" => [
                false,
                function ($link, $format) {
                    return is_file($link);
                }
            ],
        ];
    }

    private function loadFixture(string $className): void
    {
        $factory = self::$container->get('fixtures.factory');
        $this->fixture = $factory->createFixture($className);
        $this->fixture->load(self::$container->get('doctrine.orm.entity_manager'));
    }
}