<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * Matches / exactly
     *
     * @Route("/", name="welcome")
     */
    public function index(Request $request)
    {
        $template = "Default/index.html.twig";
        return $this->render($template, array());
    }
}
