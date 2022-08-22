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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/api/user", name="app_user_list", methods={"GET"})
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
     * @Route("/api/user/{id}", name="app_user_id", methods={"GET"})
     */
    public function user(UserRepository $userRepository, $id): Response
    {
        // Data recovery (Repository)
        $user = $userRepository->find($id);
        //Returns a JsonResponse that uses the serializer component if enabled, or json_encode
        $response = $this->json($user, 200, []);
        return $response;
    }
    /**
     * @Route("/api/user/create", name="app_user_create", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, UserPasswordHasherInterface $userHasher)
    {
        $json = $request->getContent();

        try {
            $user = $serializer->deserialize($json, User::class, 'json');

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }
            $hashedPassword = $userHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $em->persist($user);
            $em->flush();

            return $this->json($user, 201, []);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * @Route("/api/user/update/{id}", name="app_user_update", methods={"PUT"})
     */
    public function update(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, User $user)
    {
        // Data recovery (Repository)
        $json = $request->getContent();

        try {
            //OBJECT_TO_POPULATE injecte le nouveau $json dans le $user
            $serializer->deserialize($json, User::class, JsonEncoder::FORMAT, [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }
            // $userStdObj = json_decode($json);
            $entityManager = $doctrine->getManager();
            $entityManager->flush();

            return $this->json($user, 201, []);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    /**
     * @Route("/api/user/delete/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete($id, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $user = $userRepository->find($id);
        $em->remove($user);
        $em->flush();
        $response = $this->json($user, 200, []);
        return $response;
    }
}
