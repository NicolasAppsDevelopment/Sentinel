<?php
namespace App\EventListener;


use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postRemove, method: 'postRemove', entity: Question::class)]
class QuestionChangedNotifier
{
    public function postRemove(Question $question, PostRemoveEventArgs $event): void
    {
        $ressourceFilename = $question->getRessourceFilename();
        if ($ressourceFilename && file_exists($ressourceFilename)) {
            unlink($ressourceFilename);
        }
    }
}
