<?php

namespace Fuz\QuickStartBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Fuz\QuickStartBundle\Entity\CaptchaIpLimit;

class CaptchaIpLimitRepository extends EntityRepository
{

    public function deleteExpired(\DateTime $expiry)
    {
        $query = $this->_em->createQuery("
            DELETE Fuz\QuickStartBundle\Entity\CaptchaIpLimit cil
            WHERE cil.updateTm < :expiry
        ");

        $params = array(
            'expiry' => $expiry,
        );

        $query->execute($params);
    }

    public function record($ip, $limit)
    {
        $entity = $this->findOneByIp($ip);
        if (!$entity) {
            $new = new CaptchaIpLimit();
            $new->setIp($ip);
            $new->setLimit($limit);
            $this->_em->persist($new);
            $this->_em->flush($new);
        }
    }

    public function increaseLimit($ip, $toAdd)
    {
        $entity = $this->findOneByIp($ip);
        if ($entity) {
            $entity->setLimit($entity->getLimit() + $toAdd);
            $this->_em->persist($entity);
            $this->_em->flush($entity);
        }
    }

}
