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

    //TODO Ne pas reset la base et ajouter les lignes que si elles ne sont pas presentes (try & catch)
    //TODO Try Catch & Return error message &/or use styling to visualize the loading
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        //Declare the console styling class
        $io = new SymfonyStyle($input, $output);
        //Get the argument passed by the user while typing the command and print it back to the CMD
        $arg1 = $input->getArgument('arg1');
        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        //TODO Subfunction switch case ??

        //$process = new Process(['php bin/console doctrine:schema:drop --force', 'php bin/console doctrine:schema:create']);
        $process = Process::fromShellCommandline('php console doctrine:schema:drop --force --full-database');
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

        /*
        $establishment = (new Establishment())->setName('Lycée Pro')->setAddress('address')->setPostalCode(01000)->setCity('city');
        $establishment2 = (new Establishment())->setName('Lycée Générale')->setAddress('address2')->setPostalCode(01000)->setCity('city2');

        //Create Users & Roles
        $adminsys = (new User())->setEmail("adminsys@ocpeps.fr")
            ->setPassword('$2y$13$NLScDBQAnTFmmIxMdU9Lt.LzjZUaparqzfhst59zh9GphLaCB5WF.')
            ->addRole('ROLE_SUPER_ADMIN')
            ->setFirstname('didier')
            ->setLastname('admin')
            ->setType('admin');



        $student = (new Student())
            ->setPhone("234926")
            ->setEmail('student@student.fr')
            ->setFirstname('student')
            ->setLastname('student')
            ->setType('student')
            ->setPassword('$2y$13$FhjAzkL7xi/31y636f8VhuPqxHgROhUS9bzTHw0WqWSGOcxcHehFS') // mot de passe : eleve
            ->setEstablishment($establishment);

        $eleve2 = (new Student())->setEstablishment($establishment)->setEmail('eleve2@student.fr')->setType('student')->setPassword('$2y$13$FhjAzkL7xi/31y636f8VhuPqxHgROhUS9bzTHw0WqWSGOcxcHehFS')->setFirstname('eleve2')->setLastname('eleve2');


        $period1 = (new Periods())->setName("Semestre 1")->setEstablishment($establishment)->setBeginningDate(new \DateTimeImmutable("2023-9-01 08:00:00"))->setEndingDate(new \DateTimeImmutable("2023-12-20 18:00:00"));
        $period2 = (new Periods())->setName("Semestre 2")->setEstablishment($establishment)->setBeginningDate(new \DateTimeImmutable("2024-1-05 08:00:00"))->setEndingDate(new \DateTimeImmutable("2024-6-23 18:00:00"));

        $classe1 = (new Classes())->setEstablishment($establishment)->setTeacher($prof)->setName('6eme')->addStudent($student)->addStudent($eleve2)->addStudent($eleve3)->addStudent($eleve4)->addStudent($eleve5)->addStudent($eleve6);
        $classe2 = (new Classes())->setEstablishment($establishment)->setTeacher($prof)->setName('5eme')->addStudent($eleve7)->addStudent($eleve8)->addStudent($eleve9)->addStudent($eleve10)->addStudent($eleve11)->addStudent($eleve12);
        $classe3 = (new Classes())->setEstablishment($establishment)->setTeacher($coord)->setName('4eme');
        $classe4 = (new Classes())->setEstablishment($establishment)->setTeacher($prof)->setName('3eme');



        $musculation = (new Sport())->setName("Musculation");
        $relaxation = (new Sport())->setName("Relaxation");

        $educationLevel1 = (new EducationLevel())->setName("Education level 1")->setEstablishment($establishment)->addClass($classe1)->addClass($classe3)->addSport($natationDuree)->addSport($tennisTable)->addSport($football);
        $educationLevel2 = (new EducationLevel())->setName("Education level 2")->setEstablishment($establishment)->addClass($classe2)->addClass($classe4)->addSport($courseDuree)->addSport($badminton)->addSport($basketball);

        // Création des instances de CA
        $ca1 = (new CA())->setName('CA 1')->setDescription("L’élève réalise sa performance motrice maximale, mesurable à une échéance donnée");
        $ca2 = (new CA())->setName('CA 2')->setDescription("L’élève adapte son déplacement à des environnements variés et/ou incertains");
        $ca3 = (new CA())->setName('CA 3')->setDescription("L’élève réalise une prestation corporelle destinée à être vue et appréciée par autrui");
        $ca4 = (new CA())->setName('CA 4')->setDescription("L’élève conduit un affrontement interindividuel ou collectif pour gagner");
        $ca5 = (new CA())->setName('CA 5')->setDescription("L’élève réalise et oriente son activité physique pour développer ses ressources et s’entretenir");

        $swimmingmastery = (new CA())->setName('Savoir nager')->setDescription("Le savoir-nager correspond à une maîtrise du milieu aquatique. Il reconnaît la compétence à nager en sécurité, dans un établissement de bains ou un espace surveillé.");


        $ca4->addSport($judo);

        $ca5->addSport($courseDuree);
        $ca5->addSport($natationDuree);
        $ca5->addSport($step);
        $ca5->addSport($musculation);
        $ca5->addSport($relaxation);

        $facility = (new Facilities())->setEstablishment($establishment)->setName('instalation1')->setAddress('oui')->setType('a')->setTransportTime(10)->setEnvironment('exterieur')->setIsIndoors(false);
        $facility2 = (new Facilities())->setEstablishment($establishment)->setName('installation2')->setAddress('oui')->setType('a')->setTransportTime(10)->setEnvironment('interieur')->setIsIndoors(true);

        $expectancies = [];
        $swimmingmasteryexpectancies = [];

        //Swimming mastery



        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("A partir du bord de la piscine, entrer dans l'eau en chute arrière")->setCA($swimmingmastery);
        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("Se déplacer sur une distance de 3,5 mètres en direction d'un obstacle")->setCA($swimmingmastery);
        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("Franchir en immersion complète l'obstacle sur une distance de 1,5 mètre")->setCA($swimmingmastery);
        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("Se déplacer sur le ventre sur une distance de 15 mètres")->setCA($swimmingmastery);
        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("Au cours de ce déplacement, au signal sonore, réaliser un surplace vertical pendant 15 secondes puis reprendre le déplacement pour terminer la distance des 15 mètres")->setCA($swimmingmastery);
        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("Faire demi-tour sans reprise d'appuis et passer d'une position ventrale à une position dorsale")->setCA($swimmingmastery);
        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("Se déplacer sur le dos sur une distance de 15 mètres")->setCA($swimmingmastery);
        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("Au cours de ce déplacement, au signal sonore réaliser un surplace en position horizontale dorsale pendant 15 secondes, puis reprendre le déplacement pour terminer la distance des 15 mètres")->setCA($swimmingmastery);
        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("Se retourner sur le ventre pour franchir à nouveau l'obstacle en immersion complète")->setCA($swimmingmastery);
        $swimmingmasteryexpectancies[] = (new Expectancy())->setName("Se déplacer sur le ventre pour revenir au point de départ")->setCA($swimmingmastery);


        //END
        $expectancies[] = (new Expectancy())->setName('Produire et répartir stratégiquement ses efforts en mobilisant de façon optimale ses ressources pour gagner ou pour battre un record')->setCA($ca1);
        $expectancies[] = (new Expectancy())->setName('Connaître et mobiliser les techniques efficaces pour produire la meilleure performance possible.')->setCA($ca1);
        $expectancies[] = (new Expectancy())->setName('Analyser sa performance pour adapter son projet et progresser.')->setCA($ca1);
        $expectancies[] = (new Expectancy())->setName('Assumer des rôles sociaux pour organiser une épreuve de production de performance, un concours')->setCA($ca1);
        $expectancies[] = (new Expectancy())->setName('Assurer la prise en charge de sa préparation et de celle d\'un groupe, de façon autonome pour produire la meilleure performance possible.')->setCA($ca1);
        $expectancies[] = (new Expectancy())->setName('Connaître son niveau pour établir un projet de performance située culturellement.')->setCA($ca1);
        $expectancies[] = (new Expectancy())->setName('Anticiper et planifier son itinéraire pour concevoir et conduire dans sa totalité un projet de déplacement.')->setCA($ca2);
        $expectancies[] = (new Expectancy())->setName('Mobiliser des techniques efficientes pour adapter son déplacement aux caractéristiques du milieu.')->setCA($ca2);
        $expectancies[] = (new Expectancy())->setName('Analyser sa prestation pour comprendre les alternatives possibles et ajuster son projet en fonction de ses ressources et de celles du milieu.')->setCA($ca2);
        $expectancies[] = (new Expectancy())->setName('Assumer les rôles sociaux pour organiser la pratique des activités de pleine nature')->setCA($ca2);
        $expectancies[] = (new Expectancy())->setName('Se préparer et maintenir un engagement optimal permettant de garder sa lucidité tout au long de son parcours pour pouvoir réévaluer son itinéraire ou renoncer le cas échéant.')->setCA($ca2);
        $expectancies[] = (new Expectancy())->setName('Respecter et faire respecter la réglementation et les procédures d\'urgence pour les mettre en œuvre dans les différents environnements de pratique.')->setCA($ca2);
        $expectancies[] = (new Expectancy())->setName('Accomplir une prestation animée d\'une intention dans la perspective d\'être jugé et/ou apprécié.')->setCA($ca3);
        $expectancies[] = (new Expectancy())->setName('Mobiliser des techniques de plus en plus complexes pour rendre plus fluide la prestation et pour l\'enrichir de formes corporelles variées et maîtrisées.')->setCA($ca3);
        $expectancies[] = (new Expectancy())->setName('Composer et organiser dans le temps et l\'espace le déroulement des moments forts et faibles de sa prestation pour se produire devant des spectateurs/juges.')->setCA($ca3);
        $expectancies[] = (new Expectancy())->setName('Assumer les rôles inhérents à la pratique artistique et acrobatique notamment en exprimant et en écoutant des arguments sur la base de critères partagés, pour situer une prestation')->setCA($ca3);
        $expectancies[] = (new Expectancy())->setName('Se préparer et s\'engager pleinement pour présenter une prestation optimale et sécurisée à une échéance donnée')->setCA($ca3);
        $expectancies[] = (new Expectancy())->setName('S\'enrichir de la connaissance de productions de qualité issues du patrimoine culturel artistique et gymnique pour progresser dans sa propre pratique et aiguiser son regard de spectateur.')->setCA($ca3);
        $expectancies[] = (new Expectancy())->setName('Réaliser des choix tactiques et stratégiques pour faire basculer le rapport de force en sa faveur et marquer le point.')->setCA($ca4);
        $expectancies[] = (new Expectancy())->setName('Mobiliser des techniques d\'attaque efficaces pour se créer et exploiter des occasions de marquer ; résister et neutraliser individuellement ou collectivement l\'attaque adverse pour rééquilibrer le rapport de force.')->setCA($ca4);
        $expectancies[] = (new Expectancy())->setName('Analyser les forces et les faiblesses en présence par l\'exploitation de données objectives pour faire des choix tactiques et stratégiques adaptés à une prochaine confrontation.')->setCA($ca4);
        $expectancies[] = (new Expectancy())->setName('Respecter et faire respecter les règles partagées pour que le jeu puisse se dérouler sereinement ; assumer plusieurs rôles sociaux pour permettre le bon déroulement du jeu')->setCA($ca4);
        $expectancies[] = (new Expectancy())->setName('Savoir se préparer, s\'entraîner et récupérer pour faire preuve d\'autonomie.')->setCA($ca4);
        $expectancies[] = (new Expectancy())->setName('Porter un regard critique sur les excès et dérives de certaines pratiques sportives pour comprendre le sens des pratiques scolaires.')->setCA($ca4);
        $expectancies[] = (new Expectancy())->setName('Concevoir et mettre en œuvre un projet d\'entraînement pour répondre à un mobile personnel de développement.')->setCA($ca5);
        $expectancies[] = (new Expectancy())->setName('Éprouver différentes méthodes d\'entraînement et en identifier les principes pour les réutiliser dans sa séance.')->setCA($ca5);
        $expectancies[] = (new Expectancy())->setName('Systématiser un retour réflexif sur sa pratique pour réguler sa charge de travail en fonction d\'grades de l\'effort (fréquence cardiaque, ressenti musculaire et respiratoire, fatigue générale).')->setCA($ca5);
        $expectancies[] = (new Expectancy())->setName('Agir avec et pour les autres en vue de la réalisation du projet d\'entraînement en assurant spontanément les rôles sociaux.')->setCA($ca5);
        $expectancies[] = (new Expectancy())->setName('Construire une motricité efficiente et contrôlée pour évoluer dans des conditions de sécurité.')->setCA($ca5);
        $expectancies[] = (new Expectancy())->setName('Intégrer des conseils d\'entraînement, de diététique, d\'hygiène de vie pour se construire un mode de vie sain et une pratique raisonnée.')->setCA($ca5);


        $cours = (new Lesson())->setName('cours 1')->setSport($football)->setDate(new \DateTime())->setBeginningHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 8 hours')))->setEndingHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 10 hours')))->setFacility($facility)->setClass($classe1);
        $cours2 = (new Lesson())->setName('cours 2')->setSport($rugby)->setDate((new \DateTime())->setTimestamp(strtotime('- 1 days')))->setBeginningHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 10 hours')))->setEndingHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 12 hours')))->setFacility($facility)->setClass($classe2);
        $cours3 = (new Lesson())->setName('cours 3')->setSport($volleyball)->setDate(new \DateTime())->setBeginningHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 16 hours')))->setEndingHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 18 hours')))->setFacility($facility)->setClass($classe1);
        $cours4 = (new Lesson())->setName('cours 4')->setSport($volleyball)->setDate(new \DateTime())->setBeginningHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 10 hours')))->setEndingHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 12 hours')))->setFacility($facility)->setClass($classe1);
        $cours5 = (new Lesson())->setName('cours 5')->setSport($rugby)->setDate((new \DateTime())->setTimestamp(strtotime('- 2 days')))->setBeginningHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 10 hours')))->setEndingHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 12 hours')))->setFacility($facility)->setClass($classe2);
        $cours6 = (new Lesson())->setName('cours 6')->setSport($rugby)->setDate((new \DateTime())->setTimestamp(strtotime('+ 1 days')))->setBeginningHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 10 hours')))->setEndingHour((new \DateTime())->setTimestamp(strtotime('1 january 1970 12 hours')))->setFacility($facility)->setClass($classe2);





        $entityManager->persist($swimmingmastery);


        $entityManager->persist($establishment);

        $entityManager->persist($establishment2);

        $entityManager->persist($adminsys);
        $entityManager->persist($admin);
        $entityManager->persist($coord);
        $entityManager->persist($prof);

        $entityManager->persist($parent1);
        $entityManager->persist($parent2);
        $entityManager->persist($parent3);
        $entityManager->persist($parent4);

        $entityManager->persist($student);
        $entityManager->persist($eleve2);
        $entityManager->persist($eleve3);
        $entityManager->persist($eleve4);
        $entityManager->persist($eleve5);
        $entityManager->persist($eleve6);
        $entityManager->persist($eleve7);
        $entityManager->persist($eleve8);
        $entityManager->persist($eleve9);
        $entityManager->persist($eleve10);
        $entityManager->persist($eleve11);
        $entityManager->persist($eleve12);

        $entityManager->persist($gymnastique);
        $entityManager->persist($courseRelaisVitesse);
        $entityManager->persist($lancerDisque);
        $entityManager->persist($sautHauteur);
        $entityManager->persist($natationVitesse);
        $entityManager->persist($courseHaie);
        $entityManager->persist($lancerJavelot);
        $entityManager->persist($pentabond);
        $entityManager->persist($natationDistance);
        $entityManager->persist($escalade);
        $entityManager->persist($natationSauvetage);
        $entityManager->persist($courseOrientation);
        $entityManager->persist($acrosport);
        $entityManager->persist($aerobie);
        $entityManager->persist($gymnastiqueSol);
        $entityManager->persist($gymnastiqueSautCheval);
        $entityManager->persist($artCirque);
        $entityManager->persist($danse);
        $entityManager->persist($gymnastiqueRythmique);
        $entityManager->persist($basketball);
        $entityManager->persist($football);
        $entityManager->persist($handball);
        $entityManager->persist($rugby);
        $entityManager->persist($volleyball);
        $entityManager->persist($badminton);
        $entityManager->persist($tennisTable);
        $entityManager->persist($boxeFrancaise);
        $entityManager->persist($judo);
        $entityManager->persist($courseDuree);
        $entityManager->persist($natationDuree);
        $entityManager->persist($step);
        $entityManager->persist($musculation);
        $entityManager->persist($relaxation);

        $entityManager->persist($educationLevel1);
        $entityManager->persist($educationLevel2);

        $entityManager->persist($ca1);
        $entityManager->persist($ca2);
        $entityManager->persist($ca3);
        $entityManager->persist($ca4);
        $entityManager->persist($ca5);

        $entityManager->persist($period1);
        $entityManager->persist($period2);

        $entityManager->persist($classe1);
        $entityManager->persist($classe2);
        $entityManager->persist($classe3);
        $entityManager->persist($classe4);


        $entityManager->persist($facility);
        $entityManager->persist($facility2);

        $asgroup = new SportsAssociation();
        $asgroup->setName('Groupe AS 1 ')->setEstablishment($establishment)->setTeacher($prof);
        $asgroup->setSport($acrosport);
        $entityManager->persist($asgroup);

        $entityManager->persist($facility);

        $entityManager->persist($cours);
        $entityManager->persist($cours2);
        $entityManager->persist($cours3);
        $entityManager->persist($cours4);
        $entityManager->persist($cours5);
        $entityManager->persist($cours6);

        */


        //Update the database
        $entityManager->flush();

        //Return success with a message
        $io->success('Import fini');
        return Command::SUCCESS;
    }
}
