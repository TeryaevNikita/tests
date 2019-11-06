<?php


namespace App\Services\Set;


interface Set
{
    public function add($element): void;
    public function remove($element): void;
    public function contains($element): bool;
}
