<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TeamController extends AbstractController
{

    public function create(Request $request)
    {
        $data = [
            'teamName' => $request->request->get('teamName'),
            'teamLogo' => $request->request->get('teamLogo')
        ];

        $validator = Validation::createValidator();
        $constraint = new Assert\Collection(array(
            // the keys correspond to the keys in the input array
            'teamName' => new Assert\Length(array('min' => 1, 'max' => 255)),
            'teamLogo' => new Assert\Url()
        ));
        $violations = $validator->validate($data, $constraint);
        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $teamName = $data['teamName'];
        $teamLogo = $data['teamLogo'];

        $team = new Team();
        $team
            ->setName($teamName)
            ->setLogo($teamLogo)
        ;

        try {
            $repository = $this->getDoctrine()->getRepository(Team::class);
            $team   = $repository->save($team);
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new JsonResponse(["success" => $teamName. " has been created!"], Response::HTTP_OK);
    }

    public function delete(Request $request)
    {
        try {
            $repository = $this->getDoctrine()->getRepository(Team::class);
            $teamId      = $request->request->get('teamId');
            $team   = $repository->findOneBy([
                'id' => $teamId,
            ]);
	    if($team){

	        $repository->delete($team);
		    return new JsonResponse(["success" => $team->getName()." successfully removed.!"], Response::HTTP_OK);
	    } else {
 		    return new JsonResponse(["error" => "Team not found and deleted!"], Response::HTTP_NOT_FOUND);
	    }
 
        } catch (\Exception $e) {
            return new JsonResponse(["error" => "Team not deleted as players associated with same!"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function edit(Request $request)
    {
        try {
            $data = [];
            $validateData = [];


            if ($request->request->get('teamId')) {
                $repository  = $this->getDoctrine()->getRepository(Team::class); 
                $teamId      = $request->request->get('teamId');
                $team        = $repository->findOneBy([
                    'id' => $teamId,
                ]);

                if (!$team) {
                    return new JsonResponse(["error" => 'Team not exists'], Response::HTTP_NOT_FOUND);
                }
            } else {
                return new JsonResponse(["error" => 'Please set team id to edit'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($request->request->get('teamName')) {
                $data['teamName'] = $request->request->get('teamName');
                $validateData['teamName'] = new Assert\Length(array('min' => 1, 'max' => 255));
                $team->setName($data['teamName']);
            }

            if ($request->request->get('teamLogo')) {
                $data['teamLogo'] = $request->request->get('teamLogo');
                $validateData['teamLogo'] = new Assert\Url();
                $team->setLogo($data['teamLogo']);
            }

            if (!empty($validateData)) {
                $validator  = Validation::createValidator();
                $constraint = new Assert\Collection($validateData);

                $violations = $validator->validate($data, $constraint);
                if ($violations->count() > 0) {
                    return new JsonResponse(["error" => (string)$violations], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }


            $repository->save($team);
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
	    return new JsonResponse(["success" => $data['teamName']. " updated successfully!"], Response::HTTP_OK);

    }

    public function listTeams(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Team::class);
	$offset = $request->get('offset', Team::DEFAULT_OFFSET);
        $limit = $request->get('limit', Team::DEFAULT_LIMIT);

        if ($limit < 0) {
            $limit = Team::DEFAULT_LIMIT;
        }

        if ($offset < 0) {
            $offset = Team::DEFAULT_OFFSET;
        }
        $teams       = $repository->getTeamList($limit, $offset);

        if (!$teams['teams']) {
            return new JsonResponse(["error" => 'No Teams Exist'], Response::HTTP_NOT_FOUND);
        }
	
        return new JsonResponse($teams, Response::HTTP_OK);
    }

    public function getTeamDetail(int $teamId, Request $request){
	    $teamId = urldecode($teamId);

        $repository = $this->getDoctrine()->getRepository(Team::class);
        $teamObj       = $repository->getTeam(
             $teamId
        );

	    if (!$teamObj) {
            return new JsonResponse(["error" => 'Team does not exists'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            "teamId"=> $teamObj->getId(),
            "teamName"=> $teamObj->getName(),
            "teamLogo"=> $teamObj->getLogo(),
            ],
            Response::HTTP_OK);

    }

    public function listPlayers(int $teamId, Request $request){
	    $teamId = urldecode($teamId);

        $offset = $request->get('offset', Team::DEFAULT_OFFSET);
        $limit = $request->get('limit', Team::DEFAULT_LIMIT);

        //Resetting to zero values if negative
        if ($limit < 0) {
            $limit = Team::DEFAULT_LIMIT;
        }

        //Resetting to zero values if negative
        if ($offset < 0) {
            $offset = Team::DEFAULT_OFFSET;
        }

        $repository = $this->getDoctrine()->getRepository(Team::class);
        $details       = $repository->getTeamPlayers(
             $teamId, $offset, $limit
        );

	if (!$details['team']) {
            return new JsonResponse(["error" => 'Team does not exists'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($details, Response::HTTP_OK);

    }
}
