<?php
namespace maree\urway;
use Carbon\Carbon;

class Urway {
    //$params= ['email' => 'customer@site.com','response_url' => route('your_callback_function')]
    public static function checkout($amount = 0.0 , $params = []) {
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
                'customerEmail' => $params['email'],
                'action'        => "1", //visa , master , mada
                'merchantIp'    => (getenv('SERVER_ADDR') == false)? $_SERVER['REMOTE_ADDR'] : getenv('SERVER_ADDR') ,
                'password'      => $password,
                'currency'      => $currency,
                'country'       => config('urway.country'),
                'amount'        => $amount,
                "udf1"          => isset($params['udf1'])? $params['udf1'] : '',
                "udf2"          => isset($params['response_url'])? $params['response_url'] : "https://urway.sa/urshop/scripts/response.php",//Response page URL
                "udf3"          => isset($params['udf3'])? $params['udf3'] : '',
                "udf4"          => isset($params['udf3'])? $params['udf4'] : '',
                "udf5"          => isset($params['udf5'])? $params['udf5'] : '',
                'requestHash'   => $hash
            );
            $data               = json_encode($fields);
            if(config('urway.mode') == 'live'){
               $request_url = config('urway.live_url');
            }else{
               $request_url = config('urway.test_url');
            }
            $ch                 = curl_init($request_url); 
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


    public static function checkoutStc($amount = 0.0 , $params = []){
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
                'customerEmail' => $params['email'],
                'action'        => "13", //stc
                'merchantIp'    => (getenv('SERVER_ADDR') == false)? $_SERVER['REMOTE_ADDR'] : getenv('SERVER_ADDR') ,
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
            $ch                 = curl_init($request_url);
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

    public static function checkoutResponseStatus(){
        #parameters from payment getaway
        if(!$_GET){
            return ['key' => 'fail' , 'msg' => 'no data back from urway' ,'data' => $_GET ];
        }
        $amount                     =  ($_GET['amount'])??'';
        $status                     =  ($_GET['ResponseCode'])??'';
        $result                     =  ($_GET['Result'])??'';
        $id                         =  ($_GET['TranId'])??'';
        $trackid                    =  ($_GET['TrackId'])??'';
        $responseHash               =  ($_GET['responseHash'])??'';
        $merchantKey                = config('urway.merchantKey');
        
        $requestHash                =  "$id|$merchantKey|$status|$amount";
        $hash                       =  hash('sha256', $requestHash);
        if($hash == $responseHash  && ($result == 'Successful' || $result == 'Success') ) {
            return ['key' => 'success' ,'msg' => 'checkout success' ,'result' => $result ,'data' => $_GET ];
        }else{
            return ['key' => 'fail' , 'msg' => 'checkout failed','result' => $result ,'data' => $_GET ];
        }
    }
   
}
