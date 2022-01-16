<?php
// tests/Repository/TeamRepositoryTest.php
namespace App\Tests\Repository;

use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TeamRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchById()
    {
        $team = $this->entityManager
            ->getRepository(Team::class)
            ->findOneBy(['id' => 1])
        ;

        $this->assertSame(1, $team->getId());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}

