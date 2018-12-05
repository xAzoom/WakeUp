<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 02.11.18
 * Time: 16:10
 */

namespace App\Tests\Controller\Api;

use App\Entity\Account;
use App\Tests\ApiTestCase;

class SecurityControllerTest extends ApiTestCase
{
    public function testRegisterAccount()
    {
        $data = [
            'username' => 'test2',
            'password' => 'testtest',
        ];

        $respone = $this->client->post('api/register', [
            'body' => json_encode($data),
        ]);

        $this->assertEquals(201, $respone->getStatusCode());
    }

    public function testRegisterValidationErrors() {
        $data = [
            'username' => '',
            'password' => 'testtest',
        ];

        $respone = $this->client->post('api/register', [
            'body' => json_encode($data),
        ]);

        $this->assertEquals(400, $respone->getStatusCode());
    }
}