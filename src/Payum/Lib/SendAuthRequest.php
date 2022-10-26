<?

namespace Acme\SyliusExamplePlugin\Payum\Lib;

class SendAuthRequest
{
    private $client_id;
    private $client_secret;
    private $environment;
    private $scope;

    function __construct($client_id, $client_secret, $environment, $scope)
    {
       $this->client_id = $client_id;
       $this->client_secret = $client_secret;
       $this->environment = $environment;
       $this->scope = $scope;
    }

    function execute()
    {
        if($this->environment == "LIVE") {
            $api_url = '';
        }
        else {
            $api_url = '';
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $this->client_id.':'.$this->client_secret);    
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'grant_type=client_credentials&scope='.$this->scope);
              
        $RawResp = curl_exec($curl);
        $DecodedResponse = json_decode($RawResp);  

        if(isset($DecodedResponse->access_token))
        {
            $accessToken = $DecodedResponse->access_token;
            curl_close($curl);    
            return $accessToken;   
        }
        else
        {
            curl_close($curl);
            return false;
        }
    
    }
}
