<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 02.11.18
 * Time: 16:10
 */

namespace App\Tests\Controller\Api;

use App\Tests\ApiTestCase;

class SecurityControllerTest extends ApiTestCase
{
    public function testRegisterAccount()
    {
        $data = [
            'username' => 'test2',
            'password' => 'testtest',
        ];

        $response = $this->client->post('api/register', [
            'body' => json_encode($data),
        ]);

        $this->assertEquals(201, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, "username", "test2");
    }

    /**
     * @dataProvider registerValidationProvider
     * @throws \Exception
     */
    public function testRegisterValidationErrors($username, $password, $expected)
    {
        $data = [
            'username' => $username,
            'password' => $password,
        ];

        $response = $this->client->post('api/register', [
            'body' => json_encode($data),
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertiesExist($response, array_keys($expected));
        foreach ($expected as $key => $value) {
            $this->asserter()->assertResponsePropertyEquals($response, $key, $value);
        }
    }

    public function testInvalidJson()
    {
        $invalidBody = <<<EOF
{
    "avatarNumber" : "2
    "tagLine": "I'm from a test!"
}
EOF;

        $response = $this->client->post('api/register', [
            'body' => $invalidBody,
        ]);

        $this->assertEquals(400, $response->getStatusCode());
        $this->asserter()->assertResponsePropertyEquals($response, "code", 400);
        $this->asserter()->assertResponsePropertyEquals($response, "message", "Invalid json message received");
    }

    public function registerValidationProvider()
    {
        return [
            ['', 'testtest', [
                "message" => "Validation Failed",
                "errors.children.username.errors[0]" => "Please enter a nickname.",
            ]],
            ['username', '', [
                "message" => "Validation Failed",
                "errors.children.password.errors[0]" => "Please enter a password.",
            ]],
            ['', '', [
                "message" => "Validation Failed",
                "errors.children.username.errors[0]" => "Please enter a nickname.",
                "errors.children.password.errors[0]" => "Please enter a password.",
            ]],
        ];
    }
}