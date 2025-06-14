<?php

namespace Azulphp\Routing\Requests;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class FormDto
{
    public function __construct(public string $class)
    {
    }
}