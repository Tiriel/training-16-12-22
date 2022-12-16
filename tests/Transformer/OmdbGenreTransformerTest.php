<?php

namespace App\Tests\Transformer;

use App\Entity\Genre;
use App\Transformer\OmdbGenreTransformer;
use PHPUnit\Framework\TestCase;

class OmdbGenreTransformerTest extends TestCase
{
    public function testTransformWithStringReturnsGenreEntity()
    {
        $transformer = new OmdbGenreTransformer();
        $genre = $transformer->transform('Action');

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertSame('Action', $genre->getName());
    }

    public function testTransformWithoutStringThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $transformer = new OmdbGenreTransformer();
        $genre = $transformer->transform([]);
    }
}
