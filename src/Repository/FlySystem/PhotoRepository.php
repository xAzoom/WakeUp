<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 06.01.19
 * Time: 22:19
 */

namespace App\Repository\FlySystem;

use App\Entity\Photo;
use App\Repository\ImageRepositoryInterface;
use League\Flysystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;

class PhotoRepository implements ImageRepositoryInterface
{
    /** @var Filesystem */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function save(Photo $photo): void
    {
        /** @var UploadedFile $file */
        $file = $photo->getImage();

        $stream = fopen($file->getRealPath(), 'r+');
        $this->filesystem->writeStream($photo->getId().'.'.$photo->getFormat(), $stream);
        fclose($stream);
    }
}