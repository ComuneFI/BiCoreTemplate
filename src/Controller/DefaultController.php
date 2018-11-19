<?php

namespace App\Controller;

use Cdf\BiCoreBundle\Controller\FiController;
use Cdf\BiCoreBundle\Utils\Tabella\ParametriTabella;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Cliente;
use App\Form\ClienteType;
use Cdf\BiCoreBundle\Utils\Tabella\Tabella;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Cdf\BiCoreBundle\Utils\Tabella\DatetimeTabella;

/**
 * Cliente controller.
 *
 */
class DefaultController
{

    public function index(Request $request, \Symfony\Component\Asset\Packages $assetsmanager)
    {
        return new Response("Benvenuto");
        /*        return $this->render(
          $template,
          array(
          'parametritabella' => $parametritabella,
          )
          );

         */
    }

}
