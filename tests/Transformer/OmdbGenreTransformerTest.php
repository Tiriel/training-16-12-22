<?php

namespace App\Tests\Transformer;

use App\Entity\Genre;
use App\Transformer\OmdbGenreTransformer;
use PHPUnit\Framework\TestCase;

class OmdbGenreTransformerTest extends TestCase
{
    private static OmdbGenreTransformer $transformer;

    public static function setUpBeforeClass(): void
    {
        static::$transformer = new OmdbGenreTransformer();
    }

    /**
     * @dataProvider provideGenreNames
     */
    public function testTransformWithStringReturnsGenreEntity(string $name)
    {
        $genre = static::$transformer->transform($name);

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertSame($name, $genre->getName());
    }

    public function testTransformWithoutStringThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $genre = static::$transformer->transform([]);
    }

    public function provideGenreNames(): \Generator
    {
        yield 'Action' => ['Action'];
        yield 'Adventure' => ['Adventure'];
        yield 'Fantasy' => ['Fantasy'];
    }
}
