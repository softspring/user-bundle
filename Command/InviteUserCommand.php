<?php

namespace Softspring\UserBundle\Command;

use Softspring\UserBundle\Manipulator\UserInvitationManipulator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InviteUserCommand extends Command
{
    protected static $defaultName = 'sfs:user:invite';

    /**
     * @var UserInvitationManipulator
     */
    protected $userInvitationManipulator;

    /**
     * CreateUserCommand constructor.
     *
     * @param UserInvitationManipulator $userInvitationManipulator
     * @param string|null     $name
     */
    public function __construct(UserInvitationManipulator $userInvitationManipulator, string $name = null)
    {
        $this->userInvitationManipulator = $userInvitationManipulator;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Email');
        $this->addArgument('username', InputArgument::OPTIONAL, 'Username');
        $this->addOption('role', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Add user roles (comma separated)', []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = $input->getArgument('email');
        $username = $input->getArgument('username');
        $roles = $input->getOption('role');

        $this->userInvitationManipulator->invite($email, $username, $roles);
        
        return 0;
    }
}