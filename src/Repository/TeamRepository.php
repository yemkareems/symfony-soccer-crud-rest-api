<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository
{
    /**
     * TeamRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @return array Team
     */
    public function getTeams()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select(array('t.id', 't.name', 't.logo'))
            ->from('App\Entity\Team', 't')
            ->getQuery()->getResult();
    }

    /**
     * Get team
     *
     * @param $teamId
     * @param bool $createNewIfNull
     * @return Team|null
     */
    public function getTeam($teamId = null, $createNewIfNull = false){
        if($createNewIfNull && !$teamId){
            return new Team();
        }

        $team = $this->find($teamId);

        return $team;
    }

    /**
     * Get team detail with no.of players
     *
     * @param $teamId
     * @param bool $createNewIfNull
     * @return Team|null
     */
    public function getTeamDetail($teamId){
        $qb = $this->getEntityManager()->createQueryBuilder();
        return  $qb->select(array('t.id', 't.name', 't.logo', 'count(p.id) as noOfPlayers'))
            ->from('App\Entity\Team', 't')
            ->leftJoin('App\Entity\Player', 'p', Join::WITH, 't.id = p.team')
            ->where('t.id = :teamId')
            ->setParameter('teamId', $teamId)
            ->groupBy('t.id')
            ->getQuery()->getResult();
    }

   /**
     * Get all teams
     *
     * @param $limit
     * @param $offset
     * @return mixed
     */
    public function getTeamList($limit, $offset) {

	$qb = $this->getEntityManager()->createQueryBuilder();
	$teams = $qb->select(array('t.id as teamId', 't.name as teamName', 't.logo as teamLogo'))
            ->from('App\Entity\Team', 't')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()->getResult();
	return ["teams" => $teams];
    }

    /**
     * Get all team players
     *
     * @param $teamId
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function getTeamPlayers($teamId, $offset, $limit)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
	$team = $qb->select(array('t.id as teamId', 't.name as teamName', 't.logo as teamLogo', 'count(p.id) as noOfPlayers'))
            ->from('App\Entity\Team', 't')
            ->leftJoin('App\Entity\Player', 'p', Join::WITH, 't.id = p.team')
            ->where('t.id = :teamId')
            ->setParameter('teamId', $teamId)
            ->groupBy('t.id')
            ->getQuery()->getResult();
        $qb = $this->getEntityManager()->createQueryBuilder();
        $players = $qb->select(array('p.id','p.firstName', 'p.lastName', 'p.imageURI'))
            ->from('App\Entity\Player', 'p')
            ->leftJoin('App\Entity\Team', 't', Join::WITH, 'p.team = t.id')
            ->where('p.team = :teamId')
            ->setParameter('teamId', $teamId)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()->getResult();
	return ["team" => $team, "players" => $players];
    }

    /**
     * Save Team
     *
     * @param Team $team
     * @return Team
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Team $team){
        $entityManager = $this->getEntityManager();
        $entityManager->persist($team);
        $entityManager->flush();
        return $team;
    }

    /**
     * Delete Team
     *
     * @param Team $team
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Team $team){
        $entityManager = $this->getEntityManager();
        $entityManager->remove($team);
        $entityManager->flush();
    }
}
