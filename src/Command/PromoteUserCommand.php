<?php

namespace Softspring\UserBundle\Command;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Model\RolesAdminInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PromoteUserCommand extends Command
{
    protected static $defaultName = 'sfs:user:promote';

    protected UserManagerInterface $userManager;

    public function __construct(UserManagerInterface $userManager, string $name = null)
    {
        $this->userManager = $userManager;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument('identifier', InputArgument::REQUIRED, 'User identifier (username or email)');
        $this->addOption('super-admin', 's', InputOption::VALUE_NONE, 'User is super admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = $input->getArgument('identifier');
        $superAdmin = (bool) $input->getOption('super-admin');

        $user = $this->userManager->findUserByIdentifier($identifier);

        if (null === $user) {
            $output->writeln(sprintf('User %s not found', $identifier));

            return Command::FAILURE;
        }

        if (!$user instanceof RolesAdminInterface) {
            return Command::FAILURE;
        }

        $user->setAdmin(true);

        if ($superAdmin) {
            $user->setSuperAdmin(true);
        }

        $this->userManager->saveEntity($user);

        $output->writeln(sprintf('User %s has been promoted', $identifier));

        return Command::SUCCESS;
    }
}
