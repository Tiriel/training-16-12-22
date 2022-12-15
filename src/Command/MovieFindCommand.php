<?php

namespace App\Command;

use App\Consumer\OMDbApiConsumer;
use App\Entity\Movie;
use App\Provider\MovieProvider;
use App\Repository\MovieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsCommand(
    name: 'app:movie:find',
    description: 'Find a movie by title or OMDb ID',
)]
class MovieFindCommand extends Command
{
    private ?SymfonyStyle $io = null;
    private ?string $type = null;
    private ?string $value = null;

    public function __construct(
        private MovieRepository $movieRepository,
        private MovieProvider $provider,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('value', InputArgument::OPTIONAL, 'The movie you are searching for.')
            ->addArgument('type', InputArgument::OPTIONAL, 'The type of search (title or id).')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->provider->setSymfonyStyle($this->io);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$this->value = $input->getArgument('value')) {
            $this->value = $this->io->ask('What is the title or id of the movie you\'re searching for?');
        }

        $this->type = strtolower($input->getArgument('type'));
        if (\in_array($this->type, ['title', 'id'])) {
            $this->type = substr($this->type, 0, 1);
        }
        if (!\in_array($this->type, ['t', 'i'])) {
            $this->type = $this->io->choice('What type of data are your searching on?', ['t' => 'title', 'i' => 'id'], 't');
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->title('Your search :');
        $this->io->text(sprintf("Searching for a movie with %s=%s", $this->type, $this->value));
        if (OMDbApiConsumer::MODE_ID === $this->type && $movie = $this->movieRepository->findOneBy(['imdbId' => $this->value])) {
            $this->displayResult($movie);

            return Command::SUCCESS;
        }

        try {
            $movie = $this->provider->getMovie($this->type, $this->value);
        } catch (NotFoundHttpException) {
            $this->io->error('Movie not found!');

            return Command::FAILURE;
        }

        $this->displayResult($movie);

        return Command::SUCCESS;
    }

    private function displayResult(Movie $movie): void
    {
        $this->io->section('Result :');
        $this->io->table(['id', 'imdbId', 'Title', 'Rated'],[
            [$movie->getId(), $movie->getOmdbId(), $movie->getTitle(), $movie->getRated()],
        ]);

        $this->io->success('Movie successfully found and imported!');
    }
}
