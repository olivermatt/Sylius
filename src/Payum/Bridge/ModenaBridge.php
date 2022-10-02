<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Acme\SyliusExamplePlugin\Payum\Bridge;


final class ModenaBridge implements ModenaBridgeInterface
{
    /*** @var string|null */
    private $cacheDir;

    private $calldone;

    public $testvar;

    public function __construct(string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir;
    }

    public function setAuthorizationData(
        string $environment,
        string $signatureKey,
        string $posId,
        string $clientId,
        string $clientSecret
    ): void {
        /*
        OpenPayU_Configuration::setEnvironment($environment);

        //set POS ID and Second MD5 Key (from merchant admin panel)
        OpenPayU_Configuration::setMerchantPosId($posId);
        OpenPayU_Configuration::setSignatureKey($signatureKey);

        //set Oauth Client Id and Oauth Client Secret (from merchant admin panel)
        OpenPayU_Configuration::setOauthClientId($clientId);
        OpenPayU_Configuration::setOauthClientSecret($clientSecret);

        OpenPayU_Configuration::setOauthTokenCache(new OauthCacheFile($this->cacheDir));
        */
    }
    public function create(array $order): ?OpenPayU_Result
    {
        /** @var OpenPayU_Result|null $result */
        $result = OpenPayU_Order::create($order);

        return $result;
    }


    public function getDone()
    {
        return $this->calldone;
    }

    
    public function setDone($flag)
    {

        $this->calldone = flag;
        
    }

}
