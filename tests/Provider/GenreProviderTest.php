<?php

namespace App\Tests\Provider;

use App\Entity\Genre;
use App\Provider\GenreProvider;
use App\Repository\GenreRepository;
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

    public function testGenreProviderWithMocks()
    {
        $genreMock = $this->configureMock(Genre::class)
            ->onlyMethods(['getId', 'getName'])
            ->getMock();
        $genreMock->method('getId')->willReturn(1);
        $genreMock->method('getName')->willReturn('Action');

        $mockRepository = $this->configureMock(GenreRepository::class)
            ->onlyMethods(['findOneBy'])
            ->getMock();
        $mockRepository
            ->expects($this->exactly(2))
            ->method('findOneBy')
            ->willReturnCallback(function () use ($genreMock) {
                $args = func_get_args();
                if (isset($args[0]) && ($args[0] === ['name' => 'Action'])) {
                    return $genreMock;
                }
                return null;
            });

        $provider = new GenreProvider($mockRepository, new OmdbGenreTransformer());
        $genre1 = $provider->getGenre('Action');
        $genre2 = $provider->getGenre('Adventure');

        $this->assertSame(1, $genre1->getId());
        $this->assertNull($genre2->getId());
    }

    private function configureMock(string $className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes();
    }
}
