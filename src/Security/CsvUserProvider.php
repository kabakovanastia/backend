<?php

namespace App\Security;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CsvUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private UserService $userService)
    {
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userService->findUserByPhone($identifier);

        if (!$user) {
            throw new UserNotFoundException();
        }

        if (!$user instanceof User) {
             $newUser = new User($user->id, $user->phone, $user->name);
             $newUser->setRoles(['ROLE_USER']);
        }

        if (!$user instanceof User) {
            throw new \LogicException('The user provider must return an instance of App\Entity\User.');
        }
        if (empty($user->getRoles())) {
            $user->setRoles(['ROLE_USER']);
        }

        return $user;
    }


    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    // PasswordUpgraderInterface
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        $this->userService->updateUserPassword($user->id, $newHashedPassword);
    }
}