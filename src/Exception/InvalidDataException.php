<?php
/**
 * Created by PhpStorm.
 * User: azoom
 * Date: 13.01.19
 * Time: 01:42
 */

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class InvalidDataException extends HttpException
{
    public function __construct(int $statusCode, string $message = null, \Exception $previous = null, array $headers = array(), ?int $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}