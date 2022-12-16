<?php

namespace App\Tests\Transformer;

use App\Entity\Genre;
use App\Transformer\OmdbGenreTransformer;
use PHPUnit\Framework\TestCase;

class OmdbGenreTransformerTest extends TestCase
{
    public function testTransformWithStringReturnsGenreEntity(): void
    {
        $transformer = new OmdbGenreTransformer();
        $genre = $transformer->transform('Action');

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertSame('Action', $genre->getName());
    }

    public function testTransformWithArrayReturnsGenreEntity(): void
    {
        $transformer = new OmdbGenreTransformer();
        $genre = $transformer->transform([]);

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertEmpty($genre->getName());
    }

    public function testTransformWithIntReturnsGenreEntity(): void
    {
        $transformer = new OmdbGenreTransformer();
        $genre = $transformer->transform(1);

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertSame('1', $genre->getName());
    }
}
