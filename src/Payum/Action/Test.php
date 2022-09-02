<?php


namespace Acme\SyliusExamplePlugin\Payum\Action;




class Test
{

    public function __construct()
    {

        echo '<script>alert("Modena TEST Called");</script>';

    }

    function execute()
    {
        echo '<script>alert("ModenaTEST Execute Called");</script>';

    }

}