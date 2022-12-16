<?php

namespace App\Tests\Transformer;

use App\Entity\Movie;
use App\Transformer\OmdbMovieTransformer;
use PHPUnit\Framework\TestCase;

class OmdbMovieTransformerTest extends TestCase
{
    private static OmdbMovieTransformer $transformer;

    public static function setUpBeforeClass(): void
    {
        static::$transformer = new OmdbMovieTransformer();
    }

    /**
     * @dataProvider provideMovieData
     */
    public function testTransformWithArrayAndReleasedReturnsMovieEntity(array $data): void
    {
        $movie = static::$transformer->transform($data);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertSame('Star Wars', $movie->getTitle());
        $this->assertSame('1977', $movie->getReleasedAt()->format('Y'));
    }

    public function testTransformWithIncompleteArrayThrowsInvalidArgumentException(): void
    {
        $data = [
            'Title' => 'Star Wars',
            'Poster' => 'http://foo',
            'Country' => 'United States',
            'Released' => 'N/A',
            'Rated' => 'PG',
        ];
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Missing keys in \$value argument: Year, imdbID");

        $movie = static::$transformer->transform($data);
    }

    public function testTransformWithStringThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Argument \$value must be and array, string given");

        $movie = static::$transformer->transform('');
    }

    public function provideMovieData(): array
    {
        return [
            'Released' => [[
                'Title' => 'Star Wars',
                'Poster' => 'http://foo',
                'Country' => 'United States',
                'Released' => '25 May 1977',
                'Year' => '1977',
                'imdbID' => 'tt123456789',
                'Rated' => 'PG',
            ]],
            'Year' => [[
                'Title' => 'Star Wars',
                'Poster' => 'http://foo',
                'Country' => 'United States',
                'Released' => 'N/A',
                'Year' => '1977',
                'imdbID' => 'tt123456789',
                'Rated' => 'PG',
            ]]
        ];
    }
}
