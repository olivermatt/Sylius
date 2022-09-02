<?php

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Action;


class ModenaAuth 
{

    public function __construct()
    {

        echo '<script>alert("Modena AUth Called");</script>';

    }

    function execute()
    {
        return true;
    }

}

