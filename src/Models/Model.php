<?php

namespace Azulphp\Models;

use Azulphp\Collections\Arrayable;
use Azulphp\Collections\Collection;
use ReflectionClass;

class Model implements Arrayable
{
    /**
     * Convert a model to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties() as $property)
        {
            $result[$property->getName()] = $property->getValue($this);
        }

        return $result;
    }

    public function toArrayTree(): array
    {
        $result = [];
        $reflection = new ReflectionClass($this);

        foreach ($reflection->getProperties() as $property)
        {
            if ($property->getValue() instanceof Arrayable)
                $result[$property->getName()] = $property->getValue()->toArrayTree();
            else
                $result[$property->getName()] = $property->getValue($this);
        }

        return $result;

    }

    /**
     * Convert the model to Collection
     *
     * @return Collection<string, mixed>
     */
    public function toCollection(): Collection
    {
        return new Collection($this->toArray());
    }
}