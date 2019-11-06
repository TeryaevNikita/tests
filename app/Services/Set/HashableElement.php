<?php


namespace App\Services\Set;


class HashableElement implements Hashable
{
    public $id;

    public $data;

    public function __construct(int $id, $data)
    {
        $this->id = $id;
        $this->data = $data;
    }

    public function Hash(): string
    {
        return $this->id;
    }
}
