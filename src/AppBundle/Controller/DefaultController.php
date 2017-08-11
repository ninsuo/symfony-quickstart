<?php

namespace AppBundle\Controller;

use BaseBundle\Base\BaseController;
use BaseBundle\Form\Type\CodeMirrorType;
use BaseBundle\Form\Type\MarkdownType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction(Request $request)
    {
//        $form = $this->createNamedFormBuilder('test')
//            ->add('cm', CodeMirrorType::class)
//            ->add('submit', SubmitType::class)
//            ->getForm();
//
//        $form->handleRequest($request);
//
//        $data = null;
//        if ($form->isSubmitted()) {
//            $data = $form->getData('cm');
//        }
//
//        return [
//            'form' => $form->createView(),
//            'data' => $data,
//        ];
    }
}
