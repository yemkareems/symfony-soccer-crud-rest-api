<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Team;
use App\Repository\TeamRepository;
use App\Entity\Player;
use App\Repository\PlayerRepository;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PlayerController extends AbstractController
{

    public function create(Request $request)
    {
        $data = [
            'firstName' => $request->request->get('firstName'),
            'lastName' => $request->request->get('lastName'),
            'imageURI' => $request->request->get('imageURI'),
            'teamId' => $request->request->get('teamId'),
        ];

        $validator = Validation::createValidator();
        $constraint = new Assert\Collection(array(
            // the keys correspond to the keys in the input array
            'firstName' => new Assert\Length(array('min' => 1)),
            'lastName' => new Assert\Length(array('min' => 1)),
            'imageURI' => new Assert\Url(),
	    'teamId' => new Assert\Positive()
        ));
        $violations = $validator->validate($data, $constraint);
        if ($violations->count() > 0) {
            return new JsonResponse(["error" => (string)$violations], 500);
        }
	$repository = $this->getDoctrine()->getRepository(Team::class);
            $teamId      = $request->request->get('teamId');
            $team   = $repository->findOneBy([
                'id' => $teamId,
            ]);
	if(!$team){
		 return new JsonResponse(["error" => "Team not found. Player creation failed!"], 500);
	}

        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $imageURI = $data['imageURI'];

        $player = new Player();
        $player
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setTeam($team)
            ->setImageURI($imageURI)
        ;

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($player);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(["error" => $e->getMessage()], 500);
        }
        return new JsonResponse(["success" => $firstName. " player has been created!"], 200);
    }

    public function delete(Request $request)
    {
        try {
            $repository = $this->getDoctrine()->getRepository(Player::class);
            $playerId      = $request->request->get('playerId');
            $player   = $repository->findOneBy([
                'id' => $playerId,
            ]);
	    if($player){
		    $playerName = $player->getFirstName();

		    $entityManager = $this->getDoctrine()->getManager();
		    $entityManager->remove($player);
		    $entityManager->flush();
            	    return new Response(sprintf('%s successfully removed.',$playerName));
	    } else {
		 return new JsonResponse(["error" => "Player not found and could not be deleted!"], 500);
	    }
 
        } catch (\Exception $e) {
            return new JsonResponse(["error" => "Player not deleted!"], 500);
        }
    }

    public function edit(Request $request)
    {
        try {
            $data = [];
            $validateData = [];


            if ($request->request->get('playerId')) {
                $repository  = $this->getDoctrine()->getRepository(Player::class); 
                $playerId    = $request->request->get('playerId');
                $player        = $repository->findOneBy([
                    'id' => $playerId,
                ]);

                if (!$player) {
                    return new JsonResponse(["error" => 'Player not exists'], 500);
                }
            } else {
                return new JsonResponse(["error" => 'Please set player id to edit'], 500);
            }

	    if ($request->request->get('teamId')) {
                $repository  = $this->getDoctrine()->getRepository(Team::class); 
                $teamId    = $request->request->get('teamId');
                $team        = $repository->findOneBy([
                    'id' => $teamId,
                ]);

                if (!$team) {
                    return new JsonResponse(["error" => 'Team does not exists'], 500);
                } else {
		    $player->setTeam($team);
		}
            } else {
                return new JsonResponse(["error" => 'Please set team id to player for edit'], 500);
            }



            if ($request->request->get('firstName')) {
                $data['firstName'] = $request->request->get('firstName');
                $validateData['firstName'] = new Assert\Length(array('min' => 1));
                $player->setFirstName($data['firstName']);
            }

            if ($request->request->get('lastName')) {
                $data['lastName'] = $request->request->get('lastName');
                $validateData['lastName'] = new Assert\Length(array('min' => 1));
                $player->setLastName($data['lastName']);
            }

	    if ($request->request->get('imageURI')) {
                $data['imageURI'] = $request->request->get('imageURI');
                $validateData['imageURI'] = new Assert\Url();
                $player->setImageURI($data['imageURI']);
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
        return new Response(sprintf('%s updated successfully!', $data['firstName']));
    }

}
