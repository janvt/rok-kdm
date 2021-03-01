<?php


namespace App\Service\User;


use App\Entity\Role;
use App\Entity\User;
use App\Exception\NotFoundException;
use App\Repository\UserRepository;

class UserService
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * @param $id
     * @return User
     * @throws NotFoundException
     */
    public function findUser($id): User
    {
        $user = $this->userRepo->find($id);
        if (!$user) {
            throw new NotFoundException();
        }

        return $user;
    }

    /**
     * @param $id
     * @return User
     * @throws NotFoundException
     */
    public function makeKingdomMember($id): User
    {
        return $this->userRepo->save(
            $this->findUser($id)->addRole(Role::ROLE_KINGDOM_MEMBER)
        );
    }
}