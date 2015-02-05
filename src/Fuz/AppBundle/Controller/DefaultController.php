<?php

namespace Fuz\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

   /**
    * @Route("/", name="home")
    * @Template()
    */
   public function indexAction()
   {
      return array ();
   }

  /**
    * @Route("/testa", name="testa")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testaAction()
   {
      return array ();
   }
   /**
    * @Route("/testb", name="testb")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testbAction()
   {
      return array ();
   }

   /**
    * @Route("/testc", name="testc")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testcAction()
   {
      return array ();
   }

   /**
    * @Route("/testd", name="testd")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testdAction()
   {
      return array ();
   }

   /**
    * @Route("/teste", name="teste")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testeAction()
   {
      return array ();
   }

   /**
    * @Route("/testf", name="testf")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testfAction()
   {
      return array ();
   }

   /**
    * @Route("/testg", name="testg")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testgAction()
   {
      return array ();
   }

   /**
    * @Route("/testh", name="testh")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testhAction()
   {
      return array ();
   }

   /**
    * @Route("/testi", name="testi")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testiAction()
   {
      return array ();
   }

   /**
    * @Route("/testj", name="testj")
    * @Template("FuzAppBundle:Default:index.html.twig")
    */
   public function testjAction()
   {
      return array ();
   }

}
