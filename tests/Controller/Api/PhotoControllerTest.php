<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 16.01.19
 * Time: 22:32
 */

namespace App\Tests\Controller\Api;

use App\Tests\ApiTestCase;

class PhotoControllerTest extends ApiTestCase
{
    private $file;

    protected function tearDown()
    {
        parent::tearDown();

        array_map('unlink', array_filter((array)glob($this->getParameter("images_host").'/*')));
        if (is_file($this->file.'.png')) {
            unlink($this->file.'.png');
        }
    }

    /**
     * @dataProvider uploadPhotoProvider
     */
    public function testUploadPhoto(string $onlyId, \Closure $isFile)
    {
        $this->file = tempnam(sys_get_temp_dir(), 'upl');
        imagepng(imagecreatetruecolor(200, 200), $this->file);
        rename($this->file, $this->file.'.png');

        $response = $this->client->post('api/photo/upload',
            [
                'headers' => $this->getAuthorizedHeaders(),
                'multipart' => [
                    [
                        'name' => 'photo[image]',
                        'contents' => fopen($this->file.'.png', 'r'),
                    ],
                    [
                        'name' => 'onlyId',
                        'contents' => $onlyId,
                    ],
                ],
            ]
        );

        $this->assertTrue($isFile(json_decode($response->getBody(), true), 'png'));
    }

    public function testUploadPhotoWithoutToken()
    {
        $data = [];

        $response = $this->client->post('api/post', [
            'multipart' => [],
        ]);

        $this->assertEquals(401, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, "message", "JWT Token not found");
    }

    public function uploadPhotoProvider()
    {
        return [
            "upload file with onlyId param" => [
                "true",
                function ($link, $format) {
                    return is_file($this->getParameter("images_host") . $link . '.' . $format);
                }
            ],
            "upload file without onlyId param" => [
                "false",
                function ($link, $format) {
                    return is_file($link);
                }
            ],
        ];
    }
}