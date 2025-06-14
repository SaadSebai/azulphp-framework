<?php

namespace Azulphp\Collections;

interface Arrayable
{
    public function toArray(): array;
    public function toArrayTree(): array;
}