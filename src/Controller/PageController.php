<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController extends AbstractController
{
    /**
     * @Route("/", name="page_index")
     * @Template()
     */
    public function indexAction() {
        return [];
    }
}
