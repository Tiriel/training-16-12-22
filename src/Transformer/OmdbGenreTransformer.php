<?php

namespace App\Transformer;

use App\Entity\Genre;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbGenreTransformer implements DataTransformerInterface
{
    public function transform(mixed $value)
    {
        if (empty($value)) {
            return new Genre();
        }
        return (new Genre())->setName($value);
    }

    public function reverseTransform(mixed $value)
    {
        throw new \RuntimeException("Method not implemented.");
    }
}