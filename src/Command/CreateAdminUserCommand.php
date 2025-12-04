<?php

namespace App\Command;

use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin-user',
    description: 'Creates an admin user for the admin panel',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $phoneQuestion = new Question('Please enter the admin phone number: ');
        $phone = $helper->ask($input, $output, $phoneQuestion);

        $passwordQuestion = new Question('Please enter the admin password: ');
        $passwordQuestion->setHidden(true);
        $plainPassword = $helper->ask($input, $output, $passwordQuestion);

        $user = new AdminUser();
        $user->setPhone($phone);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln(sprintf('Admin user "%s" created successfully!', $phone));

        return Command::SUCCESS;
    }
}