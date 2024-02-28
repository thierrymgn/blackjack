<?php

namespace App\DTO\Response;

class Success
{
    private array $content;
    private int $code;

    public function __construct(array $content, int $code)
    {
        $this->content = $content;
        $this->code = $code;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}