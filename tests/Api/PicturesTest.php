<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Picture;
use App\Repository\PictureRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PicturesTest extends ApiTestCase
{
    private PictureRepository $repository;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request(Request::METHOD_GET, '/api/pictures');
        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceCollectionJsonSchema(Picture::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testGetItem(): void
    {
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Picture::class);
        $fixture = new Picture();
        $fixture->setFilePath('path de test');
        $this->repository->save($fixture, true);

        $pictureIri = $this->findIriBy(Picture::class, ['filePath' => 'path de test']);
        $response = static::createClient()->request(Request::METHOD_GET, $pictureIri);
        $this->assertMatchesResourceItemJsonSchema(Picture::class);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

}
