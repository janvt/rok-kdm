<?php


namespace App\Exception;


use Throwable;

class NotFoundException extends \Exception
{
    public function __construct($what = "", $id = "", $code = 0, Throwable $previous = null)
    {
        $message = 'Not found';
        if ($what) {
            $message .= ': ' . $what . ($id ? '(' . $id . ')' : '');
        }

        parent::__construct($message, $code, $previous);
    }
}