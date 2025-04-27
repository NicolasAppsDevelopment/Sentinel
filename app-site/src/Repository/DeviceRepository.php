<?php

namespace App\Repository;

use App\Entity\Device;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Device>
 */
class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }

    public function findAllUnpairedAction(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.isPaired = false')
            ->andWhere('d.isCamera = false')
            ->getQuery()
            ->getResult();
    }

    public function findAllUnpairedCamera(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.isPaired = false')
            ->andWhere('d.isCamera = true')
            ->getQuery()
            ->getResult();
    }
}
