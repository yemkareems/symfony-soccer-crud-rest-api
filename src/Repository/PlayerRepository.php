<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    /**
     * PlayerRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * Get player object
     *
     * @param $playerId
     * @param bool $createNewIfNull
     * @return Player|null
     */
    public function getPlayer($playerId, $createNewIfNull = false){
        if($createNewIfNull && !$playerId){
            return new Player();
        }
        $player = $this->find($playerId);
        if (!$player) {
            throw new HttpException(Response::HTTP_NOT_FOUND, "Player not found");
        }

        return $player;
    }

    /**
     * Get player detail
     *
     * @param $playerId
     * @return mixed
     */
    public function getPlayerDetail($playerId){
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select(array('p.id', 'p.firstName', 'p.lastName', 'p.imageURI', 't.name as teamName'))
            ->from('App\Entity\Player', 'p')
            ->leftJoin('App\Entity\Team', 't', Join::WITH, 'p.team = t.id')
            ->where('p.id = :playerId')
            ->setParameter('playerId', $playerId)
            ->getQuery()->getResult();
    }

    /**
     * Save player
     *
     * @param Player $player
     * @return Player
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Player $player){
        $entityManager = $this->getEntityManager();
        $entityManager->persist($player);
        $entityManager->flush();
        return $player;
    }

    /**
     * Delete a player
     *
     * @param Player $player
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Player $player){
        $entityManager = $this->getEntityManager();
        $entityManager->remove($player);
        $entityManager->flush();
    }
}
