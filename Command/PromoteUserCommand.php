<?php

namespace Softspring\UserBundle\Command;

use Softspring\UserBundle\Manager\UserManagerInterface;
use Softspring\UserBundle\Manipulator\UserManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PromoteUserCommand extends Command
{
    protected static $defaultName = 'sfs:user:promote';

    /**
     * @var UserManipulator
     */
    protected $userManager;

    /**
     * PromoteUserCommand constructor.
     *
     * @param UserManagerInterface $userManager
     * @param string|null          $name
     */
    public function __construct(UserManagerInterface $userManager, string $name = null)
    {
        $this->userManager = $userManager;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument('username', InputArgument::REQUIRED, 'Username');
        $this->addOption('super-admin', 's', InputOption::VALUE_NONE, 'User is super admin');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $superAdmin = $input->getOption('super-admin');

        $user = $this->userManager->findUserByUsername($username);

        if (!$user) {
            $output->writeln(sprintf('User %s not found', $username));
            return 1;
        }

        $user->setAdmin(true);

        if ($superAdmin) {
            $user->setSuperAdmin(true);
        }

        $this->userManager->saveEntity($user);

        $output->writeln(sprintf('User %s has been promoted', $username));

        return 0;
    }
}