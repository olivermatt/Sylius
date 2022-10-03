<?php

class OrderRequestBuilder
{

   private $postInput;

   public $token;
   public $content;


   function __construct($Token,$POSTInput)
   {
      $this->token = $Token;
      $this->postInput = $POSTInput;
   }

    function GetRequest()
    {
   
       //// Do basic checks
       $firstName = $this->postInput['firstName']; 
       $lastName = $this->postInput['lastName']; 
       $phone = $this->postInput['phone']; 
       $email = $this->postInput['email']; 
       $productName = $this->postInput['productName']; 
       $productPrice = $this->postInput['productPrice']; 
       $selectedOption = $this->postInput['mdn_selected_banklink']; 
   
       $customer["firstName"] = $firstName;
       $customer["lastName"] = $lastName;
       $customer["phoneNumber"] = $phone;
       $customer["email"] = $email;
   
       $orderItems = [];
   
       $orderItems[] = [
         'description' => $productName,
         'amount' => $productPrice,
         'currency' => 'EUR',
         'quantity' => 1,
       ];
   
       $order["orderId"] = time();
       $order["selectedOption"] = $selectedOption;
       $order["totalAmount"] = $productPrice;
       $order["currency"] = "EUR";
       $order["orderItems"] = $orderItems;
       $order["customer"] = $customer;
       $order["timestamp"] = "2022-10-06T20:24:33.380Z";
       $order["returnUrl"] = $this->generateReturnUrl($firstName,$lastName,$email,$this->postInput['pid'],$this->postInput['promoCodeH']);
       $order["cancelUrl"] = $this->generateCancelUrl();
       $order["callbackUrl"] = $this->generateReturnUrl($firstName,$lastName,$email,$this->postInput['pid'],$this->postInput['promoCodeH']);
       return json_encode($order);
      }
      
      function generateCancelUrl()
      {

      }
   
      function generateReturnUrl()
      {
  
      }
   
}

?>