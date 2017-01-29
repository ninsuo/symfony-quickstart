<?php

namespace AdminBundle\Repository;

use BaseBundle\Entity\Setting;

/**
 * SettingRepository.
 *
 * Scalability: you may need to wrap this in a redis decorator.
 */
class SettingRepository extends \Doctrine\ORM\EntityRepository
{
    public function all()
    {
        $settings = [];
        $entities = $this->findAll();
        foreach ($entities as $entity) {
            $settings[$entity->getProperty()] = $entity->getValue();
        }

        return $entity;
    }

    public function get($property, $default = null)
    {
        $entity = $this->findOneByProperty($property);
        if ($entity) {
            return $entity->getValue();
        }

        return $default;
    }

    public function set($property, $value)
    {
        $entity = $this->findOneByName($property);
        if (!$entity) {
            $entity = new Setting();
            $entity->setProperty($property);
        }
        $entity->setValue($value);
        $this->_em->persist($entity);
        $this->_em->flush($entity);
    }

    public function remove($property)
    {
        $entity = $this->findOneByName($property);
        $this->_em->remove($entity);
        $this->_em->flush($entity);
    }
}
