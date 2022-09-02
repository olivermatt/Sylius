<?php

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Request\Generic;

class TestB extends Generic
{

    public function __construct()
    {
        echo '<script>alert("Modena TESTBBB __Construct");</script>';

        header( 'Location: https://google.com');
        exit;
    }

    
}