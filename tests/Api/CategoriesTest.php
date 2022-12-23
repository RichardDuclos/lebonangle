<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesTest extends ApiTestCase
{
    private CategoryRepository $repository;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request(Request::METHOD_GET, '/api/categories');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Category::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetItem(): void
    {
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Category::class);
        $fixture = new Category();
        $fixture->setName('categorie de test');

        $this->repository->save($fixture, true);

        $categoryIri = $this->findIriBy(Category::class, ['name' => 'categorie de test']);
        $response = static::createClient()->request(Request::METHOD_GET, $categoryIri);
        $this->assertMatchesResourceItemJsonSchema(Category::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
