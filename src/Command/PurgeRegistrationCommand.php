<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'app:purge-registration',
    description: 'Add a short description for your command',
)]
class PurgeRegistrationCommand extends Command
{
    private $entityManager;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this
            ->setName('app:purge-registration')
            ->setDescription('Purge unverified users based on the provided options.')
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Number of days since users were last verified.')
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Delete unverified users.')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force deletion without confirmation.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $days = $input->getOption('days');
        $deleteOption = $input->getOption('delete');
        $forceOption = $input->getOption('force');

        if (null !== $days && !ctype_digit($days)) {
            $output->writeln('<error>The --days option must be a positive integer.</error>');

            return Command::FAILURE;
        }

        $unverifiedUsers = $this->userRepository->findUnverifiedUsersSince((int) $days);
        $output->table(
            ['ID', 'First Name', 'Last Name', 'Email', 'Days Elapsed'],
            array_map(
                function ($user) {
                    return [
                        $user->getId(),
                        $user->getFirstName(),
                        $user->getLastName(),
                        $user->getEmail(),
                        $user->getRegisteredAt()->diff(new \DateTime())->days,
                    ];
                },
                $unverifiedUsers
            )
        );

        if ($deleteOption) {
            $confirmationMessage = sprintf('Do you want to delete %d unverified users?', count($unverifiedUsers));

            if ($forceOption || $this->askForConfirmation($confirmationMessage, $input, $output)) {
                foreach ($unverifiedUsers as $user) {
                    $this->entityManager->remove($user);
                }

                $this->entityManager->flush();

                $output->writeln(sprintf('<info>%d users deleted successfully.</info>', count($unverifiedUsers)));
            } else {
                $output->writeln('<comment>Deletion aborted.</comment>');
            }
        }

        return Command::SUCCESS;
    }

    private function askForConfirmation($question, InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion($question.' (yes/no) ', false);

        return $helper->ask($input, $output, $question);
    }
}
