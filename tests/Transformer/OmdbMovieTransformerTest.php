<?php

namespace App\Tests\Transformer;

use App\Entity\Movie;
use App\Transformer\OmdbMovieTransformer;
use PHPUnit\Framework\TestCase;

class OmdbMovieTransformerTest extends TestCase
{
    public function testTransformWithArrayAndReleasedReturnsMovieEntity(): void
    {
        $data = [
            'Title' => 'Star Wars',
            'Poster' => 'http://foo',
            'Country' => 'United States',
            'Released' => '25 May 1977',
            'Year' => '1977',
            'imdbID' => 'tt123456789',
            'Rated' => 'PG',
        ];
        $transformer = new OmdbMovieTransformer();
        $movie = $transformer->transform($data);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertSame('Star Wars', $movie->getTitle());
    }

    public function testTransformWithArrayAndYearReturnsMovieEntity(): void
    {
        $data = [
            'Title' => 'Star Wars',
            'Poster' => 'http://foo',
            'Country' => 'United States',
            'Released' => 'N/A',
            'Year' => '1977',
            'imdbID' => 'tt123456789',
            'Rated' => 'PG',
        ];
        $transformer = new OmdbMovieTransformer();
        $movie = $transformer->transform($data);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertSame('1977', $movie->getReleasedAt()->format('Y'));
    }

    public function testTransformWithoutArrayReturnsEmptyMovieEntity(): void
    {
        $transformer = new OmdbMovieTransformer();
        $movie = $transformer->transform('');

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEmpty($movie->getTitle());
    }
}
