<?php


namespace App\Utils;


class HttpError
{
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $message;

    public function __construct(string $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

}