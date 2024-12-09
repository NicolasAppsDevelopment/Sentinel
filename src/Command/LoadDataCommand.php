<?php

namespace App\Command;

use App\Entity\Quiz;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Loads mock data into the database (Roles & Users), called with :
 * php bin/console app:load:data
 * In CMD
 */
#[AsCommand(
    name: 'app:load:data',
    description: 'Loads data into the database',
)]
/**
 * The Class managing the data loading command
 */
class LoadDataCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    /**
     * Arguments to be passed to specify data loading
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * The function loading the data
     * The parameters passed (options & arguments)*
     * @param InputInterface $input
     * The stream to write back to the console
     * @param OutputInterface $output
     * Success or failure code
     * @return int
     */

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        //Declare the console styling class
        $io = new SymfonyStyle($input, $output);
        //Get the argument passed by the user while typing the command and print it back to the CMD
        $arg1 = $input->getArgument('arg1');
        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        $process = Process::fromShellCommandline('php console doctrine:schema:drop --force --full-database');
        $process->setWorkingDirectory('./bin');

        try {
            $process->mustRun();
            echo $process->getOutput();
        }
        catch (ProcessFailedException $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }

        echo "Clear\n";
        $process = Process::fromShellCommandline('php console doctrine:schema:create');
        $process->setWorkingDirectory('./bin');

        //$process->setOptions(['create_new_console' => true]);
        try {
            $process->mustRun();
            echo $process->getOutput();
        }
        catch (ProcessFailedException $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }

        echo "Fill";


        //Get the entityManager to save the data
        $entityManager = $this->entityManager;

        //Create Users
        $user1 = (new User())->setUsername("user1")->setScore(0)->setEmail("user1@gmail.com")->setPassword('$2y$13$z1ShZWgo7xhVrKU7wQaEUeY82OaGb5k26gu9PnBziY7r5jCkzfiTC')->setCreationDate(new \DateTime()); #password : password1
        $user2 = (new User())->setUsername("user2")->setScore(0)->setEmail("user2@gmail.com")->setPassword('$2y$13$/YPssNKLbBaRmNo7bzZaUuRimBQgLCShiHHgk19sblr3JhieEy8Xi')->setCreationDate(new \DateTime()); #password : password2
        $user3 = (new User())->setUsername("user3")->setScore(0)->setEmail("user3@gmail.com")->setPassword('$2y$13$fHQfn9MbT.FALcee37Prj.YqNtSxOXwQPDFRP5D.thnvqujSFeiHm')->setCreationDate(new \DateTime()); #password : password3
        $user4 = (new User())->setUsername("user4")->setScore(0)->setEmail("user4@gmail.com")->setPassword('$2y$13$82db.kG1vdfbWwPdk3LUNOT4C3L6BC0IzQxuYH.ErNIvDdR/YgK/2')->setCreationDate(new \DateTime()); #password : password4
        $user5 = (new User())->setUsername("user5")->setScore(0)->setEmail("user5@gmail.com")->setPassword('$2y$13$H7VZUdfWgER3XU8whjSp1eba1f1SV.I0JlASfaQ.uEDKiDiW8LePa')->setCreationDate(new \DateTime()); #password : password5

        $quiz1= (new Quiz())->setAuthor($user1)->setCreatedDate(new \DateTime())->setTitle("User1's Quiz" )->setDescription("Nice Descritpion");
        $quiz2= (new Quiz())->setAuthor($user2)->setCreatedDate(new \DateTime())->setTitle("User2's Quiz" )->setDescription("Nice Descritpion");
        $quiz3= (new Quiz())->setAuthor($user3)->setCreatedDate(new \DateTime())->setTitle("User3's Quiz" )->setDescription("Nice Descritpion");
        $quiz4= (new Quiz())->setAuthor($user4)->setCreatedDate(new \DateTime())->setTitle("User4's Quiz" )->setDescription("Nice Descritpion");
        $quiz5= (new Quiz())->setAuthor($user5)->setCreatedDate(new \DateTime())->setTitle("User5's Quiz" )->setDescription("Nice Descritpion");

        //persist users
        $entityManager->persist($user1);
        $entityManager->persist($user2);
        $entityManager->persist($user3);
        $entityManager->persist($user4);
        $entityManager->persist($user5);

        //persist Qqizzes
        $entityManager->persist($quiz1);
        $entityManager->persist($quiz2);
        $entityManager->persist($quiz3);
        $entityManager->persist($quiz4);
        $entityManager->persist($quiz5);

        //Update the database
        $entityManager->flush();

        //Return success with a message
        $io->success('Import fini');
        return Command::SUCCESS;
    }
}
