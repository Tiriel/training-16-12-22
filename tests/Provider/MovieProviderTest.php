<?php

namespace App\Tests\Provider;

use App\Consumer\OMDbApiConsumer;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Provider\GenreProvider;
use App\Provider\MovieProvider;
use App\Repository\MovieRepository;
use App\Transformer\OmdbMovieTransformer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

class MovieProviderTest extends TestCase
{
    private const MOVIE_ARRAY = [
        'Title' => 'Star Wars: Episode IV - A New Hope',
        'Year' => '1977',
        'Rated' => 'PG',
        'Released' => '25 May 1977',
        'Genre' => 'Action, Adventure, Fantasy',
        'Country' => 'United States',
        'Poster' => 'https://m.media-amazon.com/images/M/MV5BOTA5NjhiOTAtZWM0ZC00MWNhLThiMzEtZDFkOTk2OTU1ZDJkXkEyXkFqcGdeQXVyMTA4NDI1NTQx._V1_SX300.jpg',
        'imdbID' => 'tt0076759',
    ];

    private ?MovieProvider $provider = null;
    private ?Movie $movie = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass(); // TODO: Change the autogenerated stub
    }

    protected function assertPostConditions(): void
    {
        $this->assertInstanceOf(Movie::class, $this->movie);
    }

    public function testMovieProviderReturnsMovieEntity1(): void
    {
        $this->movie = $this->getMovieProvider()->getMovie(OMDbApiConsumer::MODE_TITLE, 'Star Wars');

        $this->assertSame('Star Wars: Episode IV - A New Hope', $this->movie->getTitle());
    }

    public function testMovieProviderReturnsMovieEntity(): void
    {
        $this->movie = $this->getMovieProvider()->getMovie(OMDbApiConsumer::MODE_TITLE, 'Star Wars');

        $this->assertSame('Star Wars: Episode IV - A New Hope', $this->movie->getTitle());
    }

    private function getMovieProvider()
    {
        return $this->provider ?? $this->provider = new MovieProvider(
            $this->getMockConsumer(),
            new OmdbMovieTransformer(),
            $this->getMockRepository(),
            $this->getMockGenreProvider(),
            $this->getMockSecurity()
        );

    }

    private function getMockConsumer(): OMDbApiConsumer|MockObject
    {
        $mock = $this->createMock(OMDbApiConsumer::class);
        $mock->expects($this->once())
            ->method('consume')
            ->willReturn(self::MOVIE_ARRAY)
        ;
        return $mock;
    }

    private function getMockTransformer(): OmdbMovieTransformer|MockObject
    {
        $mock = $this->createMock(OmdbMovieTransformer::class);
        $mock->expects($this->once())
            ->method('transform')
            ->willReturn(
                (new Movie())
                ->setTitle('Star Wars: Episode IV - A New Hope')
                ->setPoster('https://m.media-amazon.com/images/M/MV5BOTA5NjhiOTAtZWM0ZC00MWNhLThiMzEtZDFkOTk2OTU1ZDJkXkEyXkFqcGdeQXVyMTA4NDI1NTQx._V1_SX300.jpg')
                ->setCountry('United States')
                ->setReleasedAt(new \DateTimeImmutable('25 May 1977'))
                ->setRated('PG')
                ->setOmdbId('tt0076759')
                ->setPrice(5.0)
                ->addGenre((new Genre())->setName('Action'))
                ->addGenre((new Genre())->setName('Adventure'))
                ->addGenre((new Genre())->setName('Fantasy'))
            )
        ;

        return $mock;
    }

    private function getMockRepository(): MovieRepository|MockObject
    {
        $mock = $this->createMock(MovieRepository::class);
        $mock->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;
        $mock->expects($this->once())
            ->method('save')
        ;

        return $mock;
    }

    private function getMockGenreProvider(): GenreProvider|MockObject
    {
        return $this->createMock(GenreProvider::class);
    }

    private function getMockSecurity(): Security|MockObject
    {
        $mock = $this->createMock(Security::class);
        $mock->expects($this->once())->method('getUser')->willReturn(null);

        return $mock;
    }
}
