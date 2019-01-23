<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 06.01.19
 * Time: 21:51
 */

namespace App\Service;


use App\Entity\Photo;

interface IPhotoManager
{
    public function uploadPhoto(Photo $photo, bool $onlyId);
}