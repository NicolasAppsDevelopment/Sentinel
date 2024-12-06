<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function getUserLeaderboard(): array
    {
        $users = $this->createQueryBuilder('user')
            ->select('user.username', 'user.score')
            ->orderBy('user.score', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $rank = 1;
        foreach ($users as &$user) {
            $user['rank'] = $rank++;
        }

        return $users;
    }

    public function getUserRank(int $userId): ?int
    {
        $users = $this->createQueryBuilder('user')
            ->select('user.id', 'user.score')
            ->orderBy('user.score', 'DESC')
            ->getQuery()
            ->getResult();

        foreach ($users as $index => $user) {
            if ($user['id'] === $userId) {
                return $index + 1;
            }
        }

        return null;
    }
}
