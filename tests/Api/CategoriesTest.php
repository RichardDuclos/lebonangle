<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesTest extends ApiTestCase
{
    public function testGetCollection(): void
    {
        $response = static::createClient()->request(Request::METHOD_GET, '/api/categories');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Category::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetItem(): void
    {
        $categoryIri = $this->findIriBy(Category::class, ['name' => 'Catégorie 1']);
        $response = static::createClient()->request(Request::METHOD_GET, $categoryIri);
        $this->assertMatchesResourceItemJsonSchema(Category::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
