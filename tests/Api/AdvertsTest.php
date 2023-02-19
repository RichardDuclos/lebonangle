<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Advert;
use App\Entity\Category;
use App\Entity\Picture;
use App\Repository\AdvertRepository;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdvertsTest extends ApiTestCase
{
    private AdvertRepository $repository;
    private CategoryRepository $categoryRepository;


    public function testGetCollection(): void
    {
        $response = static::createClient()->request(Request::METHOD_GET, '/api/adverts');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Advert::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetItem(): void
    {
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Advert::class);
        $this->categoryRepository = static::getContainer()->get('doctrine')->getRepository(Category::class);
        $category = (new Category())
            ->setName('test');
        $this->categoryRepository->save($category, true);
        $fixture = (new Advert())
            ->setTitle('Advert test')
            ->setContent('content')
            ->setAuthor('author')
            ->setEmail('email')
            ->setPrice(12)
            ->setState('rejected')
            ->setPublishedAt(null)
            ->setCategory($category);
        $fixture->setCreatedAt(new DateTimeImmutable());

        $this->repository->save($fixture, true);

        $advertIri = $this->findIriBy(Advert::class, ['title' => 'Advert test']);
        $response = static::createClient()->request(Request::METHOD_GET, $advertIri);
        $this->assertMatchesResourceItemJsonSchema(Advert::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateItem(): void
    {
        // penser a executer la commande suivante :
        // docker compose up mailer
        $this->categoryRepository = static::getContainer()->get('doctrine')->getRepository(Category::class);
        $category = (new Category())
            ->setName('test');
        $picture = (new Picture())
            ->setPath('test');
        $this->categoryRepository->save($category, true);
        $categoryIri = $this->findIriBy(Category::class, ['name' => 'test']);
        $pictureIri = $this->findIriBy(Picture::class, ['path' => 'test']);
        $response = static::createClient()->request(Request::METHOD_POST, '/api/adverts', ['json' => [
            'title' => 'title',
            'content' => 'content',
            'author' => 'author',
            'price' => 0,
            'email' => 'test@email.com',
            'category' => $categoryIri,
            'pictures' => [
                $pictureIri
            ]
        ]]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertMatchesResourceItemJsonSchema(Advert::class);
    }

}
