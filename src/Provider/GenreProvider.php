<?php

namespace App\Provider;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use App\Transformer\OmdbGenreTransformer;

class GenreProvider
{
    public function __construct(
        private GenreRepository $repository,
        private OmdbGenreTransformer $transformer
    ) {}

    public function getGenre(string $genreName): Genre
    {
        return $this->repository->findOneBy(['name' => $genreName])
            ?? $this->transformer->transform($genreName);
    }

    public function getGenres(array $genreNames): \Generator
    {
        foreach ($genreNames as $genreName) {
            yield $this->getGenre($genreName);
        }
    }

    public function getGenresFromString(string $genres): \Generator
    {
        $genreNames = \explode(', ', $genres);

        return $this->getGenres($genreNames);
    }
}