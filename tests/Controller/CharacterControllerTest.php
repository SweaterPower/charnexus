<?php

namespace App\Tests\Controller;

use App\Entity\Character;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CharacterControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $characterRepository;
    private string $path = '/character/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->characterRepository = $this->manager->getRepository(Character::class);

        foreach ($this->characterRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Character index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'character[name]' => 'Testing',
            'character[description]' => 'Testing',
            'character[status]' => 'Testing',
            'character[link]' => 'Testing',
            'character[created_at]' => 'Testing',
            'character[user]' => 'Testing',
            'character[campaign]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->characterRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Character();
        $fixture->setName('My Title');
        $fixture->setDescription('My Title');
        $fixture->setStatus('My Title');
        $fixture->setLink('My Title');
        $fixture->setCreated_at('My Title');
        $fixture->setUser('My Title');
        $fixture->setCampaign('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Character');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Character();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setStatus('Value');
        $fixture->setLink('Value');
        $fixture->setCreated_at('Value');
        $fixture->setUser('Value');
        $fixture->setCampaign('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'character[name]' => 'Something New',
            'character[description]' => 'Something New',
            'character[status]' => 'Something New',
            'character[link]' => 'Something New',
            'character[created_at]' => 'Something New',
            'character[user]' => 'Something New',
            'character[campaign]' => 'Something New',
        ]);

        self::assertResponseRedirects('/character/');

        $fixture = $this->characterRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getLink());
        self::assertSame('Something New', $fixture[0]->getCreated_at());
        self::assertSame('Something New', $fixture[0]->getUser());
        self::assertSame('Something New', $fixture[0]->getCampaign());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Character();
        $fixture->setName('Value');
        $fixture->setDescription('Value');
        $fixture->setStatus('Value');
        $fixture->setLink('Value');
        $fixture->setCreated_at('Value');
        $fixture->setUser('Value');
        $fixture->setCampaign('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/character/');
        self::assertSame(0, $this->characterRepository->count([]));
    }
}
