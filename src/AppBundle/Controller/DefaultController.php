<?php

namespace AppBundle\Controller;

use BaseBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()
           ->add('test', \BaseBundle\Form\Type\MarkdownType::class)
           ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class)
           ->getForm()
           ->handleRequest($request)
        ;

        return [
            'form' => $form->createView(),
            'data' => $form->get('test')->getData(),
        ];
    }
}
