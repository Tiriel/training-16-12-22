<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieControllerTest extends WebTestCase
{
    private static KernelBrowser $client;

    public static function setUpBeforeClass(): void
    {
        static::$client = static::createClient();
        $repository = static::getContainer()->get('doctrine.orm.default_entity_manager')->getRepository(User::class);
        static::$client->loginUser($repository->findOneBy(['email' => 'john.doe@example.com']));
    }

    public function testMovieTitlePage(): void
    {
        $crawler = static::$client->request('GET', '/movie/title/Star Wars');
        //$title = $crawler->filter('h1')->innerText();
        $rated = $crawler->filter('main ul > li')->eq(3)->text();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'A New Hope');
        $this->assertStringContainsString('PG', $rated);
    }
}
