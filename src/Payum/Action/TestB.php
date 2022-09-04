<?php

namespace Acme\SyliusExamplePlugin\Payum\Action;

use Payum\Core\Request\Generic;

class TestB extends Generic
{

    public function __construct($url)
    {
        echo '<script>alert("Modena TESTBBB __Construct");</script>';


        $url2 =  'https://webhook.site/8c83605f-3347-4ad0-9b50-778dfc65dd89';

        header( 'Location: '.$url.'?done=1');
        exit;
    }

    
}