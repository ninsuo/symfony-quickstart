<?php

namespace Fuz\QuickStartBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Fuz\QuickStartBundle\Entity\CaptchaSessionHit;

class CaptchaSessionHitRepository extends EntityRepository
{

    public function deleteExpired($strategy, \DateTime $expiry)
    {
        $query = $this->_em->createQuery("
            DELETE Fuz\QuickStartBundle\Entity\CaptchaSessionHit csh
            WHERE csh.strategy = :strategy
            AND csh.creationTm < :expiry
        ");

        $params = array(
            'strategy' => $strategy,
            'expiry'   => $expiry,
        );

        $query->execute($params);
    }

    public function record($sessionId, $strategy)
    {
        $entity = $this->findOneBy(array(
            'sessionId' => $sessionId,
            'strategy'  => $strategy,
        ));

        if (!$entity) {
            $new = new CaptchaSessionHit();
            $new->setSessionId($sessionId);
            $new->setStrategy($strategy);
            $new->setHits(1);
            $this->_em->persist($new);
            $this->_em->flush($new);
        } else {
            $entity->setHits($entity->getHits() + 1);
            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }
    }

    public function getHits($sessionId, $strategy)
    {
        $entity = $this->findOneBy(array(
            'strategy'  => $strategy,
            'sessionId' => $sessionId,
        ));

        return $entity ? $entity->getHits() : 0;
    }

    public function resetHits($sessionId, $strategy)
    {
        $entity = $this->findOneBy(array(
            'sessionId' => $sessionId,
            'strategy'  => $strategy,
        ));

        if ($entity) {
            $entity->setHits(0);
            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }
    }

}
