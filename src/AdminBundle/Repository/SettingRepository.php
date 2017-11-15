<?php

namespace AdminBundle\Repository;

use AdminBundle\Entity\Setting;

/**
 * SettingRepository
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
        $entity = $this->findOneByProperty($property);
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
        $entity = $this->findOneByProperty($property);
        $this->_em->remove($entity);
        $this->_em->flush($entity);
    }
}
