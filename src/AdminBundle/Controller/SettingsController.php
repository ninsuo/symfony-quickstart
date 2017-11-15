<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Setting;
use BaseBundle\Base\BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/settings")
 * @Security("has_role('ROLE_ADMIN')")
 */
class SettingsController extends BaseController
{
    /**
     * @Route("/", name="admin_settings")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $settings = [];
        $em = $this->getManager();
        $settingManager = $this->getManager('AdminBundle:Setting');
        $data = $settingManager->findAll();
        foreach ($data as $setting) {
            $settings[$setting->getProperty()] = $setting->getValue();
        }

        $properties = [
            // here you add all your setting properties as string
        ];

        $input = [];
        foreach ($properties as $property) {
            $input[$property] = isset($settings[$property]) ? $settings[$property] : null;
        }

        $form = $this
            ->createNamedFormBuilder("settings", Type\FormType::class, $input)
            // here you add all your settings
            ->add('submit', Type\SubmitType::class, [
                'label' => 'Save',
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            foreach ($properties as $property) {
                $setting = $settingManager->findOneByProperty($property) ?: (new Setting());
                $setting->setProperty($property);
                $setting->setValue($data[$property]);
                $em->persist($setting);
                $em->flush($setting);
            }

            $this->success('Changes have been saved.');

            return new RedirectResponse(
                $this->generateUrl('admin_settings')
            );
        }

        return [
            'form' => $form->createView(),
        ];
    }
}