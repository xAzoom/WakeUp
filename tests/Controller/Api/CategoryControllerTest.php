<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 16.01.19
 * Time: 20:14
 */

namespace App\Tests\Controller\Api;


use App\DataFixtures\AccountFixtures;
use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\PhotoFixtures;
use App\DataFixtures\PostFixtures;
use App\Entity\Post;
use App\Tests\ApiTestCase;
use App\Entity\Category;
use App\Tests\DatabasePrimer;
use Doctrine\ORM\Tools\SchemaTool;

class CategoryControllerTest extends ApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadFixture(CategoryFixtures::class);
        $this->loadFixture(AccountFixtures::class);
        $this->loadFixture(PhotoFixtures::class);
        $this->loadFixture(PostFixtures::class);
    }

    /**
     * @dataProvider showCategoryProvider
     */
    public function testShowCategory(string $type)
    {
        /** @var Category $category */
        $category = $this->fixtures[CategoryFixtures::class]->getReference('category_0');
        /** @var Post $post */
        $post = $this->fixtures[PostFixtures::class]->getReference('post_0');

        $response = $this->client->get('api/categories/' . $category->$type());

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, "id", $category->getId());
        $this->asserter()->assertResponsePropertyEquals($response, "name", $category->getName());
        $this->asserter()->assertResponsePropertyEquals($response, "slug", $category->getSlug());
        $this->asserter()->assertResponsePropertyEquals($response, "posts[0].id", $post->getId());
        $this->asserter()->assertResponsePropertyEquals($response, "posts[0].title", $post->getTitle());
        $this->asserter()->assertResponsePropertyEquals($response, "posts[0].photo_link", $this->getParameter('images_host') . $post->getPhoto()->getLink());
    }

    public function showCategoryProvider()
    {
        return [
            "showCategoryById" => ['getId'],
            "showCategoryBySlug" => ['getSlug']
        ];
    }
}