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
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
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

    /**
     * @throws \Exception
     */
    public function findUnverifiedUsersSince(int $days = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.isVerified = :isVerified')
            ->setParameter('isVerified', false);

        if (null !== $days) {
            $date = new \DateTimeImmutable();
            $date = $date->sub(new \DateInterval('P'.$days.'D'));
            $qb->andWhere('u.registeredAt <= :registeredAt')
                ->setParameter('registeredAt', $date);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Delete unverified users since a given number of days (optional).
     *
     * @param int|null $days Number of days since users were last verified
     *
     * @throws \Exception
     */
    public function deleteUnverifiedUsersSince(int $days = null): void
    {
        $unverifiedUsers = $this->findUnverifiedUsersSince($days);

        foreach ($unverifiedUsers as $user) {
            $this->getEntityManager()->remove($user);
        }

        $this->getEntityManager()->flush();
    }
}
