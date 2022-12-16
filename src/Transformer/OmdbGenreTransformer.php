<?php

namespace App\Transformer;

use App\Entity\Genre;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbGenreTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if (empty($value)) {
            return new Genre();
        }
        $value = (string) $value;

        return (new Genre())->setName($value);
    }

    public function reverseTransform(mixed $value): mixed
    {
        throw new \RuntimeException("Method not implemented.");
    }
}