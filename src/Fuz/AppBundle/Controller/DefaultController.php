<?php

namespace Fuz\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fuz\QuickStartBundle\Base\BaseController;

class DefaultController extends BaseController
{

   /**
    * @Route("/", name="home")
    * @Template()
    */
   public function indexAction()
   {
      return array ();
   }

}
