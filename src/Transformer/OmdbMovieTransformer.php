<?php

namespace App\Transformer;

use App\Entity\Movie;
use Symfony\Component\Form\DataTransformerInterface;

class OmdbMovieTransformer implements DataTransformerInterface
{
    public const KEYS = [
        'Title',
        'Poster',
        'Country',
        'Released',
        'Year',
        'imdbID',
        'Rated',
    ];

    public function transform($value): Movie
    {
        if (!\is_array($value)) {
            throw new \InvalidArgumentException(sprintf("Argument \$value must be and array, %s given", gettype($value)));
        }

        if (\count($missing = array_diff(self::KEYS, array_keys($value))) > 0) {
            throw new \InvalidArgumentException(sprintf("Missing keys in \$value argument: %s", implode(', ', $missing)));
        }

        $date = $value['Released'] === 'N/A' ? $value['Year'] : $value['Released'];

        return (new Movie())
            ->setTitle($value['Title'])
            ->setPoster($value['Poster'])
            ->setCountry($value['Country'])
            ->setReleasedAt(new \DateTimeImmutable($date))
            ->setOmdbId($value['imdbID'])
            ->setRated($value['Rated'])
            ->setPrice(5.0)
        ;
    }

    public function reverseTransform(mixed $value)
    {
        throw new \RuntimeException('Method not implemented.');
    }
}

