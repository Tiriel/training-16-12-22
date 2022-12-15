<?php

namespace App\Controller;

use App\Consumer\OMDbApiConsumer;
use App\Entity\Movie;
use App\Entity\User;
use App\Event\MovieEvent;
use App\Form\MovieType;
use App\Provider\MovieProvider;
use App\Repository\MovieRepository;
use App\Security\Voter\MovieVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Route('/movie', name: 'app_movie_')]
class MovieController extends AbstractController
{
    public function __construct(private EventDispatcherInterface $dispatcher) {}

    #[Route('', name: 'index')]
    public function index(MovieRepository $repository): Response
    {
        return $this->render('movie/index.html.twig', [
            'movies' => $repository->findAll(),
        ]);
    }

    #[Route('/{!id<\d+>?1}', name: 'details')]
    public function details(Movie $movie): Response
    {
        $this->denyAccessUnlessGranted(MovieVoter::VIEW, $movie);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/title/{title}', name: 'omdb')]
    public function omdb(string $title, MovieProvider $provider): Response
    {
        $movie = $provider->getMovie(OMDbApiConsumer::MODE_TITLE, $title);
        $this->denyAccessUnlessGranted(MovieVoter::VIEW, $movie);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/new', name:'app_movie_new', methods:['GET', 'POST'])]
    #[Route('/{id}/edit', name:'app_movie_edit', methods:['GET', 'POST'])]
    public function post(Request $request, MovieRepository $repository, ?int $id = null)
    {
        $movie = $id ? $repository->find($id) : new Movie();
        $form = $this->createForm(MovieType::class, $movie);

        if ($request->attributes->get('_route') === 'app_movie_edit') {
            $this->denyAccessUnlessGranted(MovieVoter::EDIT, $movie);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->getUser() instanceof User) {
                $movie->setAddedBy($this->getUser());
            }
            $repository->save($movie, true);

            return $this->redirectToRoute('app_movie_details', ['id' => $movie->getId()]);
        }

        return $this->render('movie/post.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    protected function denyAccessUnlessGranted(mixed $attribute, mixed $subject = null, string $message = 'Access Denied.'): void
    {
        if (!$this->isGranted($attribute, $subject)) {
            $exception = $this->createAccessDeniedException($message);
            $exception->setAttributes($attribute);
            $exception->setSubject($subject);
            if (\in_array($attribute, [MovieVoter::VIEW, MovieVoter::EDIT])
                && $subject instanceof Movie
                && $this->getUser()
            ) {
                $this->dispatcher->dispatch(new MovieEvent($subject, $exception), MovieEvent::UNDERAGE);
            }

            throw $exception;
        }
    }
}
