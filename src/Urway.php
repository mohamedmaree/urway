<?php
namespace maree\urway;
use Carbon\Carbon;

class Urway {
    //$customerInfo = ['email' => 'customer@site.com']
    public static function checkout($amount = 0.0 , $customerInfo = []) {
            $random             = rand(1111111,9999999);
            $currency           = config('urway.currency');
            $merchantKey        = config('urway.merchantKey');
            $name               = config('urway.name');
            $password           = config('urway.password');

            $txn_details        = "$random|$name|$password|$merchantKey|$amount|$currency";
            $hash               = hash('sha256', $txn_details);

            $fields             = array(
                'trackid'       => $random,
                'terminalId'    => $name,
                'customerEmail' => $customerInfo['email'],
                'action'        => "1", //visa , master , mada
                'merchantIp'    => $_SERVER['SERVER_ADDR'],
                'password'      => $password,
                'currency'      => $currency,
                'country'       => config('urway.country'),
                'amount'        => $amount,
                'requestHash'   => $hash
            );
            $data               = json_encode($fields);
            if(config('urway.mode') == 'live'){
               $request_url = config('urway.live_url');
            }else{
               $request_url = config('urway.test_url');
            }
            $ch                 = curl_init($request_url); //test
            curl_setopt($ch     , CURLOPT_CUSTOMREQUEST     , "POST");
            curl_setopt($ch     , CURLOPT_POSTFIELDS        , $data);
            curl_setopt($ch     , CURLOPT_RETURNTRANSFER    , true);
            curl_setopt($ch     , CURLOPT_HTTPHEADER        , array(
                    'Content-Type: application/json'        ,
                    'Content-Length: ' . strlen($data))
            );
            curl_setopt($ch     , CURLOPT_TIMEOUT, 5);
            curl_setopt($ch     , CURLOPT_CONNECTTIMEOUT, 5);
            $result              = curl_exec($ch);
            curl_close($ch);
            $urldecode           = (json_decode($result,true));
            
            if(!isset($urldecode['payid'])){
                return $urldecode;
            }
            //targetUrl defined between website developers and urway technical team 
            if($urldecode['payid'] != NULL){
                $url = $urldecode['targetUrl']."?paymentid=".$urldecode['payid'];
                echo  '
                    <html>
                        <form name="myform" method="POST" action="'.$url.'">
                         <h1 style="color: black"> Transaction is processing.......  "  </h1>
                        </form>
                     <script type="text/javascript">document.myform.submit();</script>
                    </html>';
            }else{
                return $urldecode;
            }
    }


    public function checkoutStc($amount = 0.0 , $customerInfo = []){
            $random             = rand(1111111,9999999);
            $currency           = config('urway.currency');
            $merchantKey        = config('urway.merchantKey');
            $name               = config('urway.name');
            $password           = config('urway.password');

            $txn_details        = "$random|$name|$password|$merchantKey|$amount|$currency";
            $hash               = hash('sha256', $txn_details);

            $fields             = array(
                'trackid'       => $random,
                'terminalId'    => $name,
                'customerEmail' => $customerInfo['email'],
                'action'        => "13", //stc
                'merchantIp'    => $_SERVER['SERVER_ADDR'],
                'password'      => $password,
                'currency'      => $currency,
                'country'       => config('urway.country'),
                'amount'        => $amount,
                'requestHash'   => $hash
            );
            $data               = json_encode($fields);
            if(config('urway.mode') == 'live'){
               $request_url = config('urway.live_url');
            }else{
               $request_url = config('urway.test_url');
            }
            $ch                 = curl_init($request_url); //test
            curl_setopt($ch     , CURLOPT_CUSTOMREQUEST     , "POST");
            curl_setopt($ch     , CURLOPT_POSTFIELDS        , $data);
            curl_setopt($ch     , CURLOPT_RETURNTRANSFER    , true);
            curl_setopt($ch     , CURLOPT_HTTPHEADER        , array(
                    'Content-Type: application/json'        ,
                    'Content-Length: ' . strlen($data))
            );
            curl_setopt($ch     , CURLOPT_TIMEOUT, 5);
            curl_setopt($ch     , CURLOPT_CONNECTTIMEOUT, 5);
            $result              = curl_exec($ch);
            curl_close($ch);
            $urldecode           = (json_decode($result,true));
            
            if(!isset($urldecode['payid'])){
                return $urldecode;
            }
            //targetUrl defined between website developers and urway technical team 
            if($urldecode['payid'] != NULL){
                $url = $urldecode['targetUrl']."?paymentid=".$urldecode['payid'];
                echo  '
                    <html>
                        <form name="myform" method="POST" action="'.$url.'">
                         <h1 style="color: black"> Transaction is processing.......  "  </h1>
                        </form>
                     <script type="text/javascript">document.myform.submit();</script>
                    </html>';
            }else{
                return $urldecode;
            }
    }
    //request data = paramaters from _GET 
    public function paymentResponseStatus($request_data = []){
        #parameters from payment getaway
        $amount                     =  $request_data['amount'];
        $status                     =  $request_data['ResponseCode'];
        $result                     =  $request_data['Result'];
        $id                         =  $request_data['TranId'];
        $trackid                    =  $request_data['TrackId'];
        $responseHash               =  $request_data['responseHash'];
        
        $merchantKey                = config('urway.merchantKey');
        
        $requestHash                =  "$id|$merchantKey|$status|$amount";
        $hash                       =  hash('sha256', $requestHash);
        if($hash == $responseHash  && ($result == 'Successful' || $result == 'Success') ) {
            return response()->json(['key' => 'success' ,'msg' => 'checkout success' ,'result' => $result ,'data' => $request_data ]);
        }else{
            return response()->json(['key' => 'fail' , 'msg' => 'checkout failed','result' => $result ,'data' => $request_data ]);
        }
    }
   
}