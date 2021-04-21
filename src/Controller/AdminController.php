<?php

namespace App\Controller;

use App\Repository\ProducerRequestRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/users", name="admin")
     */
    public function index(UserRepository $userRepository, ProducerRequestRepository $producerRequestRepository, SerializerInterface $serializer): Response
    {
        $users_data = $userRepository->findAll();
        $producer_requests_data = $producerRequestRepository->findAll();

        $users = $serializer->serialize(
            $users_data,
            'json', ['groups' => ['admin' /* if you add "user_detail" here you get circular reference */]]
        );

        $producer_requests = $serializer->serialize(
            $producer_requests_data,
            'json', ['groups' => ['producer_request','admin' /* if you add "user_detail" here you get circular reference */]]
        );

        // if($user === null || $user->getAdmin() === null) {
        //     return $this->json([
        //         "msg" => "Page non trouvée",
        //     ]);
        // }

        return $this->json([
            'users' => json_decode($users),
            'producerRequests' => json_decode($producer_requests),
        ]);
    }

    /**
     * @Route("/users/{id}/role/{spy}", name="admin_update_role")
     */
    public function updateRole(UserRepository $userRepository, $id, $spy) {
        $user = $userRepository->find($id);

        $IS_PRODUCER = 1;

        if(intval($spy) === $IS_PRODUCER) {
            $user->setRoles(['ROLE_USER', 'ROLE_PRODUCER']);
        } else {
            $user->setRoles(['ROLE_USER']);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->json([
            'message' => "success",
            'msg' => "Role utilisateur modifié avec success",
        ], 200);
    }

    /**
     * @Route("/users/{id}/state-change", name="admin_update_state")
     */
    public function desactivateUser(UserRepository $userRepository, $id) {
        $user = $userRepository->find($id);

        if($user->getIsActive() || $user->getIsActive() === null) {
            $user->setIsActive(false);
        } else{
            $user->setIsActive(true);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->json([
            'message' => "success",
        ], 200);
    }

    /**
     * @Route("/users/remove/producer-request/{producerRequestId}", name="admin_remove_producer_request")
     */
    public function removeProducerRequest(ProducerRequestRepository $producerRequestRepository, $producerRequestId) {
        $request = $producerRequestRepository->find($producerRequestId);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($request);
        $entityManager->flush();
        
        return $this->json([
            'message' => "success",
            'msg' => "La requête a été acceptée, l'utilisateur est maintenant Producteur",
        ], 200);
    }

    /**
     * @Route("/users/refuse/producer-request/{producerRequestId}", name="admin_refuse_producer_request")
     */
    public function refuseProducerRequest(ProducerRequestRepository $producerRequestRepository, $producerRequestId) {
        $request = $producerRequestRepository->find($producerRequestId);
        
        $request->setState('Refusée');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($request);
        $entityManager->flush();
        
        return $this->json([
            'message' => "success",
            'msg' => "La requête été refusée. L'utilisateur ne pourra plus envoyer de nouvelle requête",
        ], 200);
    }

    /**
     * @Route("/users/activate/producer-request/{producerRequestId}", name="admin_activate_producer_request")
     */
    public function activateProducerRequest(ProducerRequestRepository $producerRequestRepository, $producerRequestId) {
        $request = $producerRequestRepository->find($producerRequestId);
        
        $request->setState('En attente');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($request);
        $entityManager->flush();
        
        return $this->json([
            'message' => "success",
            'msg' => "La requête est à nouveau en attente de validation",
        ], 200);
    }
}
