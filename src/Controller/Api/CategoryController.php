<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\Doctrine\PostRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class CategoryController extends AbstractFOSRestController
{
    /**
     * @Route("/api/categories/{category}", name="api_category", methods={"GET"})
     * @Entity("category", expr="repository.findByIdOrSlug(category)")
     * @View(serializerGroups={"category"})
     */
    public function getCategory(Category $category)
    {
        $data = [
            "id" => $category->getId(),
            "name" => $category->getName(),
            "slug" => $category->getSlug(),
            "posts" => $category->getPosts()->toArray(),
        ];

        return $this->view($data, Response::HTTP_OK);
    }
}
