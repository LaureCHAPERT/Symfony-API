<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use PhpParser\JsonDecoder;
use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user_list", methods={"GET"})
     */
    public function list(UserRepository $userRepository): Response
    {
        // Data recovery (Repository)
        $users = $userRepository->findAll();
        //Returns a JsonResponse that uses the serializer component if enabled, or json_encode
        $response = $this->json($users, 200, []);
        return $response;
    }
    /**
     * @Route("/user/{id}", name="app_user_list", methods={"GET"})
     */
    public function user(UserRepository $userRepository, $id): Response
    {
        // Data recovery (Repository)
        $user = $userRepository->find($id);
        //Returns a JsonResponse that uses the serializer component if enabled, or json_encode
        $response = $this->json($user, 200, []);
        return $response;
    }
}
