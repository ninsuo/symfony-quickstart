<?php

namespace AppBundle\Controller;

use AppBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use BaseBundle\Tools\Richtext;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $form = $this
           ->createNamedFormBuilder("test", Type\FormType::class)
           ->add('test', CKEditorType::class, [
               'required' => false,
               'label'    => 'Petit test',
               'config'   => Richtext::getCKEditorConfig(),
           ])
           ->add('submit', Type\SubmitType::class)
           ->getForm()
           ->handleRequest($request)
        ;

        $data = $form->getData();

        return [
            'form' => $form->createView(),
            'data' => $data,
        ];
    }
}
