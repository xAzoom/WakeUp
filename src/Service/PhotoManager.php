<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 06.01.19
 * Time: 21:50
 */

namespace App\Service;


use App\Entity\Photo;
use App\Repository\FlySystem\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class PhotoManager implements IPhotoManager
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PhotoRepository
     */
    private $photoRepository;
    /**
     * @var string
     */
    private $images_host;

    public function __construct(EntityManagerInterface $entityManager, PhotoRepository $photoRepository, string $images_host)
    {
        $this->entityManager = $entityManager;
        $this->photoRepository = $photoRepository;
        $this->images_host = $images_host;
    }

    public function uploadPhoto(Photo $photo, bool $onlyId = false) : string
    {
        if (!$photo->getOwner()) {
            throw new \InvalidArgumentException('Photo must have owner.');
        }

        $uuid = Uuid::uuid4()->toString();

        $photo->setId($uuid);
        $photo->setFormat($photo->getImage()->getClientOriginalExtension());

        $this->photoRepository->save($photo);
        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        if($onlyId) {
            return $photo->getId();
        }
        return $this->images_host . $photo->getLink();
    }
}