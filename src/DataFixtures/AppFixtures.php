<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\User;
use DateTimeImmutable;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * @var Generator $faker
     */
    private Generator $faker;

    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {

        $roles = [
            'ROLE_USER',
            'ROLE_ADMIN',
        ];

        $users =
            [
                [

                    'password' => 'jeremy.B84*',
                    'telephone' => '0651758859',
                    'role' => 'ROLE_ADMIN',
                ],
                [

                    'password' => 'victor.L84*',
                    'telephone' => '0578598516',
                    'role' => 'ROLE_ADMIN',
                ],
                [

                    'password' => 'laure.C84*',
                    'telephone' => '0485976524',
                    'role' => 'ROLE_USER',
                ],
                [

                    'password' => 'elise.P84*',
                    'telephone' => '0685741954',
                    'role' => 'ROLE_USER',
                ],
            ];

        $userObjects = [];
        for ($u = 0; $u <=  count($users) - 1; $u++) {
            $user = new User;
            $user->setFirstName($this->faker->firstName());
            $user->setLastName($this->faker->lastName());
            $user->setTelephone($users[$u]['telephone']);
            $user->setEmail($this->faker->email());
            $user->setRoles([$users[$u]['role']]);
            $hashedPassword = $this->hasher->hashPassword($user, $users[$u]['password']);
            $user->setPassword($hashedPassword);
            $user->setCreatedAt(new DateTimeImmutable());
            $userObjects[] = $user;
            $manager->persist($user);
        }
        $manager->flush();
    }
}
