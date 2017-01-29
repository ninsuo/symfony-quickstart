<?php

namespace BaseBundle\Services;

use BaseBundle\Base\BaseService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * base.doctrine.helper.
 */
class DoctrineHelper extends BaseService
{
    public function persistHandleDuplicates(FormInterface $form, $entity, $entry)
    {
        $em = $this
           ->get('doctrine')
           ->getManager()
        ;

        try {
            $em->persist($entity);
            $em->flush();
        } catch (UniqueConstraintViolationException $ex) {
            $form->addError(new FormError(
               $this->get('translator')->trans('base.form.duplicate_error', [
                   '%entry%' => $entry,
               ])
            ));

            return false;
        }

        return true;
    }
}
