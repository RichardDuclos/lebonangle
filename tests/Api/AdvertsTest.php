<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Advert;
use App\Entity\Category;
use App\Entity\Picture;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdvertsTest extends ApiTestCase
{
    public function testGetCollection(): void
    {
        $response = static::createClient()->request(Request::METHOD_GET, '/api/adverts');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Advert::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetItem(): void
    {
        $advertIri = $this->findIriBy(Advert::class, ['title' => 'test']);
        $response = static::createClient()->request(Request::METHOD_GET, $advertIri);
        $this->assertMatchesResourceItemJsonSchema(Advert::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateItem(): void
    {
        // penser a executer la commande suivante :
        // docker compose up mailer
        $categoryIri = $this->findIriBy(Category::class, ['name' => 'Catégorie 1']);
        $pictureIri = $this->findIriBy(Picture::class, ['filePath' => 'test']);
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
