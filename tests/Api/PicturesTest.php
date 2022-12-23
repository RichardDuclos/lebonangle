<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Picture;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PicturesTest extends ApiTestCase
{
    public function testGetCollection(): void
    {
        $response = static::createClient()->request(Request::METHOD_GET, '/api/pictures');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Picture::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetItem(): void
    {
        $pictureIri = $this->findIriBy(Picture::class, ['filePath' => 'test']);
        $response = static::createClient()->request(Request::METHOD_GET, $pictureIri);
        $this->assertMatchesResourceItemJsonSchema(Picture::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
