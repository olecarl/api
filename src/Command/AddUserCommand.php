<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use function Symfony\Component\String\u;

/**
 * A console command that creates users and stores them in the database.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console app:add-user
 *
 * To output detailed information, increase the command verbosity:
 *
 *     $ php bin/console app:add-user -vv
 *
 * See https://symfony.com/doc/current/console.html
 *
 * We use the default services.yaml configuration, so command classes are registered as services.
 * See https://symfony.com/doc/current/console/commands_as_services.html
 */
#[AsCommand(
    name: 'app:add-user',
    description: 'Creates users and stores them in the database',
    help: self::HELP,
)]
final class AddUserCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly Validator $validator,
        private readonly UserRepository $users,
    ) {
        parent::__construct();
    }

    /**
     * This optional method is the first one executed for a command and is useful
     * to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * This method is executed after initialize() and before __invoke(). Its purpose
     * is to check if some options/arguments are missing and interactively ask the user
     * for those values.
     *
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        /** @var string|null $email */
        $email = $input->getArgument('email');

        /** @var string|null $password */
        $password = $input->getArgument('password');

        if (null !== $email & null !== $password) {
            return;
        }

        $this->io->title('Add User Command Interactive Wizard');
        $this->io->text([
            'If you prefer to not use this interactive wizard, provide the',
            'arguments required by this command as follows:',
            '',
            ' $ php bin/console app:add-user email password',
            '',
            'Now we\'ll ask you for the value of all the missing command arguments.',
        ]);

        // Ask for the email if it's not defined
        if (null !== $email) {
            $this->io->text(' > <info>Email</info>: '.$email);
        } else {
            $email = $this->io->ask('Email', null, $this->validator->validateEmail(...));
            $input->setArgument('email', $email);
        }

        // Ask for the password if it's not defined
        if (null !== $password) {
            $this->io->text(' > <info>Password</info>: '.u('*')->repeat(u($password)->length()));
        } else {
            $password = $this->io->askHidden('Password (your type will be hidden)', $this->validator->validatePassword(...));
            $input->setArgument('password', $password);
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     *
     * Commands can optionally define arguments and/or options (mandatory and optional)
     *
     * @see https://symfony.com/doc/current/console/input.html
     */
    public function __invoke(
        #[Argument('The email of the new user')] string $email,
        #[Argument('The plain password of the new user', 'password')] string $plainPassword,
        #[Option('If set, the user is created as an administrator', 'admin')] bool $isAdmin = false,
    ): int {
        $stopwatch = new Stopwatch();
        $stopwatch->start('add-user-command');

        // make sure to validate the user data is correct
        $this->validateUserData($email, $plainPassword);

        // create the user and hash its password
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($isAdmin ? [User::ROLE_ADMIN, User::ROLE_USER] : [User::ROLE_USER]);

        // See https://symfony.com/doc/5.4/security.html#registering-the-user-hashing-passwords
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->io->success(\sprintf('%s was successfully created: %s', $isAdmin ? 'Administrator user' : 'User', $user->getEmail()));

        $event = $stopwatch->stop('add-user-command');

        if ($this->io->isVerbose()) {
            $this->io->comment(\sprintf('New user database id: %d / Elapsed time: %.2f ms / Consumed memory: %.2f MB', $user->getId(), $event->getDuration(), $event->getMemory() / (1024 ** 2)));
        }

        return Command::SUCCESS;
    }

    private function validateUserData(string $email, string $plainPassword): void
    {
        // validate password and email if is not this input means interactive.
        $this->validator->validateEmail($email);
        $this->validator->validatePassword($plainPassword);

        // check if a user with the same email already exists.
        $existingEmail = $this->users->findOneBy(['email' => $email]);

        if (null !== $existingEmail) {
            throw new RuntimeException(\sprintf('There is already a user registered with the "%s" email.', $email));
        }
    }

    /**
     * The command help is usually included in the #[AsCommand] attribute, but when
     * it's too long, it's better to define a separate constant to maintain the
     * code readability.
     */
    public const HELP = <<<'HELP'
        The <info>%command.name%</info> command creates new users and saves them in the database:

          <info>php %command.full_name%</info> <comment>email password</comment>

        By default the command creates regular users. To create administrator users,
        add the <comment>--admin</comment> option:

          <info>php %command.full_name%</info> email password <comment>--admin</comment>

        If you omit any of the three required arguments, the command will ask you to
        provide the missing values:

          # command will ask you for the email
          <info>php %command.full_name%</info> <comment>email</comment>

          # command will ask you for the email and password
          <info>php %command.full_name%</info> <comment>password</comment>
    HELP;
}
