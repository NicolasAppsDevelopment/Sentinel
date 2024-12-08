<?php
namespace App\EventListener;


use App\Entity\Question;
use App\Entity\Quiz;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Quiz::class)]
class QuizChangedNotifier
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    )
    {}

    public function postRemove(Quiz $quiz, PostRemoveEventArgs $event): void
    {
        $ressourceFilename = $this->parameterBag->get("uploads_directory") . '/' . $quiz->getIllustrationFilename();
        if ($ressourceFilename && is_file($ressourceFilename)) {
            unlink($ressourceFilename);
        }
    }
}
