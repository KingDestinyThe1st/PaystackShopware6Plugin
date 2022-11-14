<?php declare(strict_types=1);

namespace PaystackShopware6Plugin\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class PaymentLink
{
    private $initUrl;

    private $fields;

    private $fields_string;

    private $ch;

    private $link;

    private $email;

    private $amount;

    private $currency;

    private $callback_url;

    private $reference;

    private $authorization;


    public function __construct(EntityRepositoryInterface $paystackKeysRepository, EntityRepositoryInterface $paystackTransactionsRepository)
    {
        $this->paystackKeysRepository = $paystackKeysRepository;
        $this->paystackTransactionsRepository = $paystackTransactionsRepository;
        
    }

   public function saveReferenceTransactionId(Context $context, string $reference, string $transactionId):void
    {
        $this->paystackTransactionsRepository->create([
            [
                'reference' => $reference,
                'transactionId' => $transactionId,   
            ]
        ], $context);



    }


    /**
     * @param $email, $amount, $currency $reference, $authorization, $callback_url
     * @return string URL
     */
    public function returnPaymentLink($email, $amount, $currency, $reference, $authorization, $callback_url): string
    {

        

        $initUrl = "https://api.paystack.co/transaction/initialize";
        $fields = [
                    'email' => $email,
                    'amount' => $amount * 100,
                    'currency' => $currency,
                    'callback_url' => $callback_url,
                    'reference' => $reference,
                ];
        $fields_string = http_build_query($fields);
        //open connection
        $ch = curl_init();
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $initUrl);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $authorization",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

        //execute post
        $link = curl_exec($ch);
        $link = json_decode($link);

        if($link->status == true)
        {
            if(is_string($link->data->authorization_url))
            {
                
                return $link->data->authorization_url;

            }

        }

      return 'Error: Could Not Generate Payment Link';
        

    }


    public function wasPaymentSuccessful(Context $context, string $transactionId){

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('transactionId', $transactionId));
        $data = $this->paystackTransactionsRepository->search($criteria, $context)->getElements();
        $i = 0;
        
        foreach ($data as $dataSet) {


        //start code

        $curl = curl_init();  
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.paystack.co/transaction/verify/$dataSet->reference",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer sk_test_4822077a8eb36c205e3807b3aacd9711af768731",
            "Cache-Control: no-cache",
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if(!$err){

            $response = json_decode($response);

            if(is_object($response)){

                //return $response->status;

                if($response->status == true){
                    
                    if($response->data->status == 'success'){
                        return 'success';
                    }
                }

            }



        }

        //end code
        
            $i++;   

        }

        return 'fail';
            
    }


    
    public  function verifyPayment($reference)
    {

        
        $curl = curl_init();  
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer sk_test_4822077a8eb36c205e3807b3aacd9711af768731",
            "Cache-Control: no-cache",
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        //$response = json_decode($response);


        if($err){
            return "ERROR PROCESSING PAYMENT - ".$err;
        }

        if(!is_null($response)){

            return $response;
           

        }

       
        
    }


   


    public function checkPaymentSuccess(Context $context, string $transactionId)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('transactionId', $transactionId));
        $data = $this->paystackTransactionsRepository->search($criteria, $context)->getElements();
        $i = 0;
        
        foreach ($data as $dataSet) {
            
            if($this->verifyPayment($dataSet->getReference()) == $dataSet->getReference())
            {
                //return 'The Same';
                return $this->verifyPayment($dataSet->getReference());
                //break;
            }

            $i++;
           
        }

        //return $dataSet->reference;

       //return $i;    
    }




    /**
     * @description get secret key from database
     * @param Context $context
     */
    public function returnSecretKey(Context $context): ?string
    {
        $criteria = new Criteria();
        $data = $this->paystackKeysRepository->search($criteria, $context)->first();
        return $data->secretKey;
    }

    
    


  



}
