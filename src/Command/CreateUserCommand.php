<?php

namespace Softspring\UserBundle\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Softspring\UserBundle\Manipulator\UserManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'sfs:user:create';

    protected UserManipulator $userManipulator;

    public function __construct(UserManipulator $userManipulator, string $name = null)
    {
        $this->userManipulator = $userManipulator;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument('username', InputArgument::REQUIRED, 'Username');
        $this->addArgument('email', InputArgument::REQUIRED, 'Email');
        $this->addArgument('password', InputArgument::REQUIRED, 'Password');
        $this->addOption('role', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Add user roles (comma separated)', []);
        $this->addOption('enabled', null, InputOption::VALUE_NONE, 'User enabled');
        $this->addOption('admin', 'a', InputOption::VALUE_NONE, 'User is admin');
        $this->addOption('super-admin', 's', InputOption::VALUE_NONE, 'User is super admin');
        $this->addOption('skip-existing', 'k', InputOption::VALUE_NONE, 'Skip if user exists');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');
        $roles = $input->getOption('role');
        $enabled = $input->getOption('enabled');
        $admin = $input->getOption('admin');
        $superAdmin = $input->getOption('super-admin');
        $skipExisting = $input->getOption('skip-existing');

        if ($superAdmin) {
            $admin = true;
        }

        try {
            $this->userManipulator->create($username, $email, $password, $roles, $enabled, $admin, $superAdmin);
        } catch (UniqueConstraintViolationException $e) {
            if (!$skipExisting) {
                $output->writeln(sprintf('<error>User %s exists</error>', $username));

                return Command::FAILURE;
            } else {
                $output->writeln(sprintf('<info>User %s exists, ignoring</info>', $username));
            }
        }

        return Command::SUCCESS;
    }
}
