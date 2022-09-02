<?php

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Request\Generic;

class Test implements Generic
{

    public function __construct()
    {
        echo '<script>alert("Modena TESTBBB __Construct");</script>';
    }

    
}