<?php

namespace App\Tests\Provider;

use App\Entity\Genre;
use App\Provider\GenreProvider;
use App\Transformer\OmdbGenreTransformer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GenreProviderTest extends KernelTestCase
{
    private static GenreProvider $provider;

    public static function setUpBeforeClass(): void
    {
        $kernel = self::bootKernel();
        static::$provider = new GenreProvider(
            $kernel->getContainer()->get('doctrine.orm.default_entity_manager')->getRepository(Genre::class),
            new OmdbGenreTransformer()
        );
        static::ensureKernelShutdown();
    }

    /**
     * @group integration
     */
    public function testGenreProviderReturnsGenreEntityFromDatabase(): void
    {
        $genre = static::$provider->getGenre('Action');

        $this->assertInstanceOf(Genre::class, $genre);
        $this->assertNotEmpty($genre->getId());
    }
}
