<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 13.01.19
 * Time: 21:38
 */

namespace App\Tests\Controller\Api;


use App\DataFixtures\AccountFixtures;
use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\PhotoFixtures;
use App\DataFixtures\PostFixtures;
use App\Entity\Category;
use App\Entity\Photo;
use App\Entity\Post;
use App\Tests\ApiTestCase;

class PostControllerTest extends ApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadFixture(CategoryFixtures::class);
        $this->loadFixture(AccountFixtures::class);
        $this->loadFixture(PhotoFixtures::class);
    }

    public function testCreatePost()
    {
        /** @var Category $category */
        $category = $this->fixtures[CategoryFixtures::class]->getReference('category_0');
        /** @var Photo $photo */
        $photo = $this->fixtures[PhotoFixtures::class]->getReference('photo_0');
        $data = [
            'title' => 'Lorem ipsum dolor.',
            'content' => 'Lorem ipsum dolor sit amet, at sit nulla scaevola, sit.',
            'category' => $category->getId(),
            'photo' => $photo->getId(),
        ];

        $response = $this->client->post('api/post', [
            "body" => json_encode($data),
            "headers" => $this->getAuthorizedHeaders(),
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, "title", $data['title']);
        $this->asserter()->assertResponsePropertyEquals($response, "content", $data['content']);
        $this->asserter()->assertResponsePropertyEquals($response, "category.id", $category->getId());
        $this->asserter()->assertResponsePropertyEquals($response, "category.name", $category->getName());
        $this->asserter()->assertResponsePropertyEquals($response, "photo_link", $this->getParameter('images_host') . $photo->getLink());
    }

    public function testCreatePostWithoutToken()
    {
        $data = [];

        $response = $this->client->post('api/post', [
            "body" => json_encode($data),
        ]);

        $this->assertEquals(401, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, "message", "JWT Token not found");
    }

    /**
     * @dataProvider createPostValidationProvider
     */
    public function testCreatePostValidation($data, $expected)
    {
        $response = $this->client->post('api/post', [
            'body' => json_encode($data),
            'headers' => $this->getAuthorizedHeaders(),
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        foreach ($expected as $key => $value) {
            $this->asserter()->assertResponsePropertyEquals($response, $key, $value);
        }
    }

    public function testShowPost()
    {
        $this->loadFixture(PostFixtures::class);
        /** @var Post $post */
        $post = $this->fixtures[PostFixtures::class]->getReference("post_0");
        /** @var Photo $photo */
        $photo = $this->fixtures[PhotoFixtures::class]->getReference('photo_0');

        $response = $this->client->get('api/post/' . $post->getId());

        $this->assertEquals(200, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, "id", $post->getId());
        $this->asserter()->assertResponsePropertyEquals($response, "title", $post->getTitle());
        $this->asserter()->assertResponsePropertyEquals($response, "content", $post->getContent());
        $this->asserter()->assertResponsePropertyEquals($response, "category.id", $post->getCategory()->getId());
        $this->asserter()->assertResponsePropertyEquals($response, "category.name", $post->getCategory()->getName());
        $this->asserter()->assertResponsePropertyEquals($response, "photo_link", $this->getParameter('images_host') . $photo->getLink());
    }

    public function testInvalidJson()
    {
        $invalidBody = <<<EOF
{
    "title" : "Hello
    "content": "Lorem impsum .."
}
EOF;

        $response = $this->client->post('api/post', [
            'body' => $invalidBody,
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, ["code", "message"]);
        $this->asserter()->assertResponsePropertyEquals($response, "code", 400);
        $this->asserter()->assertResponsePropertyEquals($response, "message", "Invalid json message received");
    }

    public function createPostValidationProvider()
    {
        return [
            'empty data' => [
                [],
                [
                    'message' => "Validation Failed",
                    'errors.children.title.errors[0]' => 'Please enter a title.',
                    'errors.children.content.errors[0]' => 'Please enter a content.',
                    'errors.children.category.errors[0]' => 'You must choose a category.',
                    'errors.children.photo.errors[0]' => 'You must upload a photo.',
                ]
            ],
            'not exist category' => [
                [
                    'category' => 0,
                ],
                [
                    'message' => "Validation Failed",
                    'errors.children.category.errors[0]' => 'This value is not valid.',
                ]
            ],
            'non exist photo' => [
                [
                    'photo' => '1111-1111-1111-1111',
                ],
                [
                    'message' => "Validation Failed",
                    'errors.children.photo.errors[0]' => 'This value is not valid.',
                ]
            ],
            'too short title' => [
                [
                    'title' => 'ab',
                ],
                [
                    'message' => "Validation Failed",
                    'errors.children.title.errors[0]' => 'Your title must be at least 3 characters long.',
                ]
            ],
            'too long title' => [
                [
                    'title' => '1234567890123456789012345',
                ],
                [
                    'message' => "Validation Failed",
                    'errors.children.title.errors[0]' => 'Your title cannot be longer than 24 characters.',
                ]
            ],
            'too short content' => [
                [
                    'content' => 'ab',
                ],
                [
                    'message' => "Validation Failed",
                    'errors.children.content.errors[0]' => 'Your content must be at least 3 characters long.',
                ]
            ],
            'too long content' => [
                [
                    'content' => 'Lorem ipsum dolor sit amet, vituperata dissentiet sit an, ea vis scripta percipitur, his velit scribentur eu. Vel iusto soluta alterum ne, no legimus democritum duo. Mel dolorum officiis ei, per tritani appetere ne. Cu eam augue noluisse, nemore electram pri cu.',
                ],
                [
                    'message' => "Validation Failed",
                    'errors.children.content.errors[0]' => 'Your content cannot be longer than 250 characters.',
                ]
            ],
        ];
    }
}