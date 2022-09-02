<?php

namespace Acme\SyliusExamplePlugin\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseProductController;
use Sylius\Component\Resource\ResourceActions;


class TstController extends BaseProductController
{
    public function go() : response
    {
 
        echo "<script>alert('TST');</script>";
        return $this->render('@AcmeSyliusExamplePlugin/dynamic_greeting.html.twig', ['greeting' => 'tere']);


    }
}