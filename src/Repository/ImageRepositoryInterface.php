<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 06.01.19
 * Time: 22:17
 */

namespace App\Repository;


use App\Entity\Photo;

interface ImageRepositoryInterface
{
    public function save(Photo $photo): void;
}