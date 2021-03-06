<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use App\products;
use App\User;
use Auth;
use Session;
use Illuminate\Support\Facades\DB;
class OrdersController extends Controller
{
    //
    public function __construct(){
        
    }
    function arrayToObject($e){
    if( gettype($e)!='array' ) return;
    foreach($e as $k=>$v){
        if( gettype($v)=='array' || getType($v)=='object' )
            $e[$k]=(object)($this->arrayToObject($v));
    }
    return (object)$e;
} 
    function show(){
    }
    function storev1(Request $request){
        //basic information of the bag
       $bag_number=$request->input('bag_number');
       $client_reference=$request->input('client_reference');
       $disposition=$request->input('disposition');
       $hawb=$request->input('hawb');
       $mawb=$request->input('mawb');
       //item lines information
       $description=$request->input('description');
       $qty=$request->input('qty');
       $price=$request->input('price');
       $weight=$request->input('weight');
       $parcel_number=$request->input('parcel_number');
       $taric_code=$request->input('taric_code');
       $ecommerce_url=$request->input('ecommerce_url');
       $ecommerce_reference=$request->input('ecommerce_reference');
       //delivery information
       $courier=$request->input('courier');
       $courier_service=$request->input('courier_service');
       $registered=$request->input('registered');
       $name=$request->input('name');
       $company=$request->input('company');
       $street=$request->input('street');
       $zip_code=$request->input('zip_code');
       $city=$request->input('city');
       $country_code=$request->input('country_code');
       $phone=$request->input('phone');
       
       $data=array();
       $appkey=$request->input('key');
       $header=array();
       $url='https://app-sandbox.viaeurope.com/api/v1/orders';
       
        $header = array(
        "Authorization: Token token=\"".$appkey."\"",
        "Content-Type: application/json",
        "Accept: application/json",
        );
        $data['order']['bag_number']=$bag_number;
        $data['order']['client_reference']=$client_reference;
        $data['order']['disposition']=$disposition;
        $data['order']['hawb']=$hawb;
        $data['order']['mawb']=$mawb;
        for($i=0;$i<count($description);$i++){
            $data['order']['line_items'][$i]['description']=$description[$i];
            $data['order']['line_items'][$i]['qty']=$qty[$i];
            $data['order']['line_items'][$i]['price']=$price[$i];
            $data['order']['line_items'][$i]['weight']=$weight[$i];
            $data['order']['line_items'][$i]['parcel_number']=$parcel_number[$i];
            $data['order']['line_items'][$i]['taric_code']=$taric_code[$i];
            $data['order']['line_items'][$i]['ecommerce_url']=$ecommerce_url[$i];
            $data['order']['line_items'][$i]['ecommerce_reference']=$ecommerce_reference[$i];
        }
        $data['order']['delivery']['courier']=$courier;
        $data['order']['delivery']['courier_service']=$courier_service;
        $data['order']['delivery']['registered']=$registered;
        $data['order']['delivery']['name']=$name;
        $data['order']['delivery']['company']=$company;
        $data['order']['delivery']['street']=$street;
        $data['order']['delivery']['zip_code']=$zip_code;
        $data['order']['delivery']['city']=$city;
        $data['order']['delivery']['country_code']=$country_code;
        $data['order']['delivery']['phone']=$phone;
        $json_data=json_encode($data);
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $str = curl_exec($ch);
        if(strpos($str,"errors")!=false){
        echo $str;
        exit;
        }
        else{
        $n=strripos($str,'}');
        $itemcode=substr($str,0,$n+1);
        $order=json_decode($itemcode);
        $key=$appkey;
        
        return view('v1/order', ['order' => $order,'key'=>$key]);
        }
    }
    function UpdateOrderv1(Request $request){
       $client_reference=$request->input('client_reference');
       $hawb=$request->input('hawb');
       $mawb=$request->input('mawb');   
       $data=array();
       $appkey=$request->input('key');
       $header=array();
       $url='https://app-sandbox.viaeurope.com/api/v1/orders/'.$client_reference;
        $header = array(
        "Authorization: Token token=\"".$appkey."\"",
        "Content-Type: application/json",
        "Accept: application/json",
        );
        $data['order']['hawb']=$hawb;
        $data['order']['mawb']=$mawb;
        $json_data=json_encode($data);
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $str = curl_exec($ch);
        if(strpos($str,"error")!=false||$str==""){
        echo $str;
        exit;
        }
        else{
        $order=json_decode($str);
        $key=$appkey;
        return view('v1/order', ['order' => $order,'key'=>$key]);
        }
        
    }
    function UpdateHAWBv1(Request $request){
       $hawb=$request->input('hawb');
       $weight=$request->input('weight');
       $units=$request->input('units');
       $data=array();
       $appkey=$request->input('key');
       $header=array();
       $url='https://app-sandbox.viaeurope.com/api/v1/hawbs/'.$hawb;
        $header = array(
        "Authorization: Token token=\"".$appkey."\"",
        "Content-Type: application/json",
        "Accept: application/json",
        );
        $data['hawb']['volumetric_weight']=$weight;
        $data['hawb']['number_of_units']=$units;
        $json_data=json_encode($data);
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $str = curl_exec($ch);
        var_dump($str);exit;
        if(strpos($str,"error")!=false||$str==""){
        echo $str;
        exit;
        }
        else{
        $hawb=json_decode($str);
        return view('v1/hawb', ['hawb' => $hawb]);
        }
        
    }
    
    function storev2(Request $request){
        //basic information of the bag
       $bag_number=$request->input('bag_number');
       $client_reference=$request->input('client_reference');
       $disposition=$request->input('disposition');
       $shipment_client_reference=$request->input('shipment_client_reference');
       //item lines information
       $description=$request->input('description');
       $qty=$request->input('qty');
       $price=$request->input('price');
       $weight=$request->input('weight');
       $parcel_number=$request->input('parcel_number');
       $taric_code=$request->input('taric_code');
       //delivery information
       $courier=$request->input('courier');
       $courier_service=$request->input('courier_service');
       $registered=$request->input('registered');
       $name=$request->input('name');
       $company=$request->input('company');
       $street=$request->input('street');
       $zip_code=$request->input('zip_code');
       $city=$request->input('city');
       $country_code=$request->input('country_code');
       $phone=$request->input('phone');
       
       $data=array();
       $appkey=$request->input('key');
       $header=array();
       $url='https://app-sandbox.viaeurope.com/api/v2/orders/';
       
        $header = array(
        "Authorization: Token token=\"".$appkey."\"",
        "Content-Type: application/json",
        "Accept: application/json",
        );
        $data['order']['bag_number']=$bag_number;
        $data['order']['client_reference']=$client_reference;
        $data['order']['disposition']=$disposition;
        $data['order']['shipment_client_reference']=$shipment_client_reference;
        if(isset($description)){
        for($i=0;$i<count($description);$i++){
            $data['order']['line_items'][$i]['description']=$description[$i];
            $data['order']['line_items'][$i]['qty']=$qty[$i];
            $data['order']['line_items'][$i]['price']=$price[$i];
            $data['order']['line_items'][$i]['weight']=$weight[$i];
            $data['order']['line_items'][$i]['parcel_number']=$parcel_number[$i];
            $data['order']['line_items'][$i]['taric_code']=$taric_code[$i];
        }
        }
        else{
            for($i=0;$i<count($weight);$i++){
            $data['order']['weights_for_labels'][$i]['weight']=$weight[$i];
            $data['order']['weights_for_labels'][$i]['parcel_number']=$parcel_number[$i];
        }
        }
        $data['order']['courier']=$courier;
        $data['order']['courier_service']=$courier_service;
        $data['order']['registered']=$registered;
        $data['order']['name']=$name;
        $data['order']['street']=$street;
        $data['order']['zip_code']=$zip_code;
        $data['order']['city']=$city;
        $data['order']['country_code']=$country_code;
        $data['order']['phone']=$phone;
        $json_data=json_encode($data);
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $str = curl_exec($ch);
        echo $str;
//        if(strpos($str,"errors")!=false){
//        echo $str;
//        exit;
//        }
//        else{
//        $n=strripos($str,'}');
//        $itemcode=substr($str,0,$n+1);
//        $order=json_decode($itemcode);
//        $key=$appkey;
//        
//        return view('v1/order', ['order' => $order,'key'=>$key]);
//        }
    }
    function UpdateOrderv2(Request $request){
       $client_reference=$request->input('client_reference');  
       $data=array();
       $appkey=$request->input('key');
       $header=array();
       $url='https://app-sandbox.viaeurope.com/api/v2/orders/'.$client_reference;
        $header = array(
        "Authorization: Token token=\"".$appkey."\"",
        "Content-Type: application/json",
        "Accept: application/json",
        );
         //basic information of the bag
       $bag_number=$request->input('bag_number');
       $disposition=$request->input('disposition');
       $shipment_client_reference=$request->input('shipment_client_reference');
       //item lines information
       $description=$request->input('description');
       $qty=$request->input('qty');
       $price=$request->input('price');
       $weight=$request->input('weight');
       $parcel_number=$request->input('parcel_number');
       $taric_code=$request->input('taric_code');
        $data['order']['bag_number']=$bag_number;
        $data['order']['disposition']=$disposition;
        $data['order']['shipment_client_reference']=$shipment_client_reference;
        if(isset($description)){
        for($i=0;$i<count($description);$i++){
            $data['order']['line_items'][$i]['description']=$description[$i];
            $data['order']['line_items'][$i]['qty']=$qty[$i];
            $data['order']['line_items'][$i]['price']=$price[$i];
            $data['order']['line_items'][$i]['weight']=$weight[$i];
            $data['order']['line_items'][$i]['parcel_number']=$parcel_number[$i];
            $data['order']['line_items'][$i]['taric_code']=$taric_code[$i];
        }
        }
        $json_data=json_encode($data);
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $str = curl_exec($ch);
        echo $str;
    }
    
    function CancelOrderv2(Request $request){
       $client_reference=$request->input('client_reference');
       $data=array();
       $appkey=$request->input('key');
       $header=array();
       $url='https://app-sandbox.viaeurope.com/api/v2/orders/'.$client_reference.'/cancel';
       $header = array(
        "Authorization: Token token=\"".$appkey."\"",
        "Content-Type: application/json",
        "Accept: application/json",
        );
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $str = curl_exec($ch);
        return $str;
    }
    
    function GetLabelsv2(Request $request){
       $client_reference=$request->input('client_reference');
       $data=array();
       $appkey=$request->input('key');
       $header=array();
       $url='https://app-sandbox.viaeurope.com/api/v2/orders/'.$client_reference.'/labels';
       $header = array(
        "Authorization: Token token=\"".$appkey."\"",
        "Content-Type: application/json",
        "Accept: application/json",
        );
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $str = curl_exec($ch);
        return $str;
    }
    
    function WrapShipmentv2(Request $request){
       $shipment_client_reference=$request->input('shipment_client_reference');
       $data=array();
       $appkey=$request->input('key');
       $header=array();
       $url='https://app-sandbox.viaeurope.com/api/v2/shipments/'.$shipment_client_reference.'/wrap';
       $header = array(
        "Authorization: Token token=\"".$appkey."\"",
        "Content-Type: application/json",
        "Accept: application/json",
        );
        
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $str = curl_exec($ch);
        return $str;
    }
}
