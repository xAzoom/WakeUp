<?php

namespace App\Controller\Api;

use App\Entity\Photo;
use App\Entity\Post;
use App\Exception\InvalidDataException;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractFOSRestController
{
    /**
     * @Route("/api/post", name="api_post_create", methods={"POST"})
     */
    public function postAction(Request $request, EntityManagerInterface $entityManager)
    {
        $post = new Post();
        $data = json_decode($request->getContent(), true);
        if($data === null) {
            throw new InvalidDataException(400, 'Invalid json message received');
        }

        $form = $this->createForm(PostType::class, $post);
        $form->submit($data);

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->view($form, Response::HTTP_BAD_REQUEST);
        }

        $photo = $entityManager->getRepository(Photo::class)->findOneBy(["id" => $post->getPhoto()]);
        $post->setPhotoLink($this->getParameter('images_host') . $photo->getLink());
        $post->setAuthor($this->getUser());
        $entityManager->persist($post);
        $entityManager->flush();

        return $this->view($post, Response::HTTP_CREATED, ["list"]);
    }

    /**
     * @Route(path="/api/post/{id}", name="api_post_get", methods={"GET"})
     */
    public function getAction(Post $post)
    {
        return $this->view($post, Response::HTTP_OK);
    }
}
