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
            'teamName' => new Assert\Length(array('min' => 1)),
            'teamLogo' => new Assert\Url()
        ));
        $violations = $validator->validate($data, $constraint);
        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], 500);
        }
        $teamName = $data['teamName'];
        $teamLogo = $data['teamLogo'];

        $team = new Team();
        $team
            ->setName($teamName)
            ->setLogo($teamLogo)
        ;

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($team);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }
        return new JsonResponse(["success" => $teamName. " has been created!"], 200);
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
		    $teamName = $team->getName();

		    $entityManager = $this->getDoctrine()->getManager();
		    $entityManager->remove($team);
		    $entityManager->flush();
            return new Response(sprintf('%s successfully removed.',$teamName));
	    } else {
		 return new JsonResponse(["error" => "Team not found and deleted!"], 500);
	    }
 
        } catch (\Exception $e) {
            return new JsonResponse(["error" => "Team not deleted as players associated with same!"], 500);
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
                    return new JsonResponse(["error" => 'Team not exists'], 500);
                }
            } else {
                return new JsonResponse(["error" => 'Please set team id to edit'], 500);
            }

            if ($request->request->get('teamName')) {
                $data['teamName'] = $request->request->get('teamName');
                $validateData['teamName'] = new Assert\Length(array('min' => 1));
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
                    return new JsonResponse(["error" => (string)$violations], 500);
                }
            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }
        return new Response(sprintf('%s updated successfully!', $data['teamName']));
    }

    public function list(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Team::class);
        $teams       = $repository->findAll();

        if (!$teams) {
            return new JsonResponse(["error" => 'Teams do not exists'], 500);
        }
	foreach($teams as $teamObj) {
        $data[] = [
            'teamName'=>$teamObj->getName(),
            'teamLogo' => $teamObj->getLogo(),
            'teamId' => $teamObj->getId()
        ];
	}
        return new JsonResponse($data, 200);
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

        return new JsonResponse($details, 200);

    }
}
