<?php
namespace App\EventListener;


use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Question::class)]
class QuestionChangedNotifier
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    )
    {}

    public function postRemove(Question $question, PostRemoveEventArgs $event): void
    {
        $ressourceFilename = $this->parameterBag->get("uploads_directory") . '/' . $question->getRessourceFilename();
        if ($ressourceFilename && is_file($ressourceFilename)) {
            unlink($ressourceFilename);
        }
    }
}
