<?php


namespace App\Services\Set;


class HashableEqualsElement implements Hashable, Equals
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

    public function equals(object $obj): bool
    {
        return $this->id === $obj->id && $this->data === $obj->data;
    }
}
