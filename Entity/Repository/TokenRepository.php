<?php

namespace Igdr\Bundle\TokenAuthBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class TokenRepository.
 */
class TokenRepository extends EntityRepository
{
    /**
     * @param int $expired
     *
     * @return array
     */
    public function cleanup($expired)
    {
        $query = $this->createQueryBuilder('ut')
                ->where('ut.lastUse < :expired')
                ->orWhere('ut.lastUse IS NULL AND ut.created < :expired')
                ->setParameter('expired', $expired);

        return $query->getQuery()->getResult();
    }
}
