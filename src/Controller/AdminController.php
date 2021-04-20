<?php

namespace App\Controller;

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
    public function index(UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $users_data = $userRepository->findAll();

        $users = $serializer->serialize(
            $users_data,
            'json', ['groups' => ['admin' /* if you add "user_detail" here you get circular reference */]]
        );

        // if($user === null || $user->getAdmin() === null) {
        //     return $this->json([
        //         "msg" => "Page non trouvée",
        //     ]);
        // }

        return $this->json([
            'users' => json_decode($users),
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
}
