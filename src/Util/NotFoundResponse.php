<?php


namespace App\Util;


use App\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Response;

class NotFoundResponse extends Response
{
    public function __construct(NotFoundException $e)
    {
        parent::__construct($e->getMessage(), Response::HTTP_NOT_FOUND);
    }
}