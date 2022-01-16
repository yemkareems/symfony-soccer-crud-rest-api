<?php

namespace App\Service;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TeamService
{

    const DEFAULT_OFFSET = 0;

    const DEFAULT_LIMIT = 5;

    /**
     * @var Team Repository Interface
     */
    private $teamRepository;

    private $validatorService;

    /**
     * TeamService constructor.
     * @param TeamRepository $teamRepository
     * @param ValidatorService $validatorService
     */
    public function __construct(TeamRepository $teamRepository, ValidatorService $validatorService)
    {
        $this->teamRepository = $teamRepository;
        $this->validatorService = $validatorService;
    }

    /**
     * Get all teams
     *
     * @return array|null
     */
    public function getAllTeams(): ?array
    {
        return $this->teamRepository->getTeams();
    }

    /**
     * Get team detail
     *
     * @param int $teamId
     * @return array|null
     */
    public function getTeamDetail(int $teamId): ?array
    {
        $team = $this->teamRepository->getTeamDetail($teamId);
        if (empty($team)) {
            throw new HttpException(Response::HTTP_NOT_FOUND, "Team (" . $teamId . ") not found");
        }
        return $team;
    }

    /**
     * Get players by team with offset & limit
     *
     * @param $teamId
     * @param $offset
     * @param $limit
     * @return array
     */
    public function getTeamPlayers($teamId, $offset = self::DEFAULT_OFFSET, $limit = self::DEFAULT_LIMIT)
    {
        return $this->teamRepository->getTeamPlayers($teamId, $offset, $limit);
    }

    /**
     * Delete Team
     *
     * @param int $teamId
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteTeam(int $teamId): void
    {
        $team = $this->teamRepository->getTeam($teamId);

        if (count($team->getPlayers())) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, "Team has players");
        }
        $this->teamRepository->delete($team);

    }

    /**
     * Add / Update Team
     *
     * @param $data
     * @param null $teamId
     * @return Team
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveTeam($data, $teamId = null): Team
    {
        $team = $this->teamRepository->getTeam($teamId, true);
        $team->setAttributes($data);

        if ($this->validatorService->Validator($team)) {
            $team = $this->teamRepository->save($team);
        }
        return $team;
    }


}