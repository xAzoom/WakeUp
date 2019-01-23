<?php

namespace App\Controller\Api;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Service\IPhotoManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractFOSRestController
{
    /**
     * @Route("/api/photo/upload", name="api_photo_upload", methods={"POST"})
     */
    public function postAction(Request $request, IPhotoManager $photoManager)
    {
        $photo = new Photo();

        /** @var FormInterface $form */
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return $this->view("Bad Request", Response::HTTP_BAD_REQUEST);
        }

        if (!$form->isValid()) {
            return $this->view($form);
        }

        $photo->setOwner($this->getUser());
        $photoLink = $photoManager->uploadPhoto($photo, $request->request->get("onlyId") === "true");

        return $this->view($photoLink, Response::HTTP_CREATED);
    }
}
