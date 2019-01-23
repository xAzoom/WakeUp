<?php

namespace App\Controller\Api;

use App\Repository\Doctrine\CategoryRepository;
use App\Repository\Doctrine\PostRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractFOSRestController
{
    /**
     * @Route("api/homepage", name="homepage", methods={"GET"})
     * @View(serializerGroups={"default"})
     */
    public function getAction(PostRepository $postRepository, CategoryRepository $categoryRepository)
    {
        $post = $postRepository->findBy([], null, 50);
        $categories = $categoryRepository->findAll();

        $data = [
            "posts" => $post,
            "categories" => $categories,
        ];

        return $this->view($data, Response::HTTP_OK);
    }
}
