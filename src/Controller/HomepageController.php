<?php

declare(strict_types=1);

namespace App\Controller\Shop;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class HomepageController
{
    /** @var Environment */
    private $twig;

    public function __construct(Environment $twig)
    {
        echo "<script>alert('homepage controller');</script>";
        $this->twig = $twig;
    }

    public function indexAction(): Response
    {
        return new Response($this->twig->render('@SyliusShop/Homepage/index.html.twig'));
    }

    public function customAction(): Response
    {
        return new Response($this->twig->render('custom.html.twig'));
    }
}