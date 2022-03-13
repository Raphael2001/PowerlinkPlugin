<?php
const BASE_URL_PowerLink= "https://api.powerlink.co.il/api/";
$today = date("DD/MM/YYYY");
// $seller_id = "4";
// $order_id="";

$client= array(
    "accountid"=>"0",
    "telephone"=>"",
    "emailaddress"=>"",
    "billingcity"=>"",
    "billingstreet"=>"",
    "firstname"=>"",
    "lastname"=>"",
    // "comment"=>""
);

function startsWith($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function create_acquisition($product, $client, $date, $code_coupon)
{
    global $client;
    // global $order_id;

    //global $seller_id;
    
    $url = BASE_URL_PowerLink .'record/AccountProduct';
    $data = array(
            "productid"=>"$product[productid]",
            "quantity"=>$product["quantity"],
            "price"=> $product["price"] * $product["quantity"],
            "accountid"=>"$client[accountid]",
            "pcfpurchasedate"=>$date,
            // "pcfseller"=> "4",
            // "pcfcouponname"=>$code_coupon,
            // "pcforderid"=>"$order_id",

      );
      
    $result = basic_post_with_curl($url, $data);
}

function get_product_from_pl($product_name)
{
    $url = BASE_URL_PowerLink .'query';
    $data = array(
          "objecttype" => "14",
          "sort_type" => "desc",
          "query"=> "statuscode = 1",
          "sort_by"=> "name",
          "page_number"=> "1",
          "page_size"=> "50",
    );
    $result= basic_post_with_curl($url, $data);
    $obj = json_decode($result);
    $objects =  $obj->data->Data;
    foreach ($objects as $object) {
        if (startsWith($object->name, $product_name)) {
            return $object;
        }
    }
}

function basic_post_with_curl($url, $data)
{
    $data_string = json_encode($data);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
    'Content-Type: application/json',
    'tokenid:' .get_option("pl-token"),
    'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($curl);

    curl_close($curl);
    return $result;
}

function basic_put_with_curl($url, $data)
{
    $data_string = json_encode($data);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
    'Content-Type: application/json',
    'tokenid:' .get_option("pl-token"),
    'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($curl);

    curl_close($curl);
    return $result;
}

function create_client()
{
    global $client;
    global $order_id;
    $data = array(
          "telephone1"=> "$client[telephone]",
          "emailaddress1"=> "$client[emailaddress]",
          "accountname"=>  "$client[firstname] $client[lastname]",
          "firstname"=>"$client[firstname]",
          "lastname"=>"$client[lastname]",
          "billingcity"=>"$client[billingcity]",
          "billingstreet"=>"$client[billingstreet]",
          "pcfaddressnote" =>"$client[comment]",
          "originatingleadcode"=>"1", // מקור הגעה : אינטרנט
        //   "statuscode"=>"45", // לקוח פעיל
        //   "pcflastorderid"=>"$order_id", // מספר הזמנה אחרונה
        );
    $url='https://api.powerlink.co.il/api/record/account';
    $data_string = json_encode($data);
    basic_post_with_curl($url, $data);
}

function get_client_id()
{
    global $client;
    $url = BASE_URL_PowerLink .'query';
    $data = array(
          "objecttype" => "1",
          "sort_type" => "desc",
          "query"=> "(telephone1 = $client[telephone])",
          "sort_by"=> "accountname",
          "page_number"=> "1",
          "page_size"=> "50"
    );
    $result= basic_post_with_curl($url, $data);
    $obj = json_decode($result);
    $object =  $obj->data->Data;
    return $object;
}

function update_client()
{
    global $client;
    $url=BASE_URL_PowerLink .'record/account/'.$client["accountid"];
    global $order_id;

   
    $data = array(
        "telephone1"=> "$client[telephone]",
        "emailaddress1"=> "$client[emailaddress]",
        "accountname"=>  "$client[firstname] $client[lastname]",
        "firstname"=>"$client[firstname]",
        "lastname"=>"$client[lastname]",
        // "pcfaddressnote" =>"$client[comment]",
        // "statuscode"=>"45", // לקוח פעיל
        // "pcflastorderid"=>"$order_id", // מספר הזמנה אחרונה

    );
    if ($client["billingcity"] != "") {
        $data["billingcity"] = $client["billingcity"];
    }
    if ($client["billingstreet"] != "") {
        $data["billingstreet"] = $client["billingstreet"];
    }

    basic_put_with_curl($url, $data);
}

function get_product_to_array($product_id, $price_per_one, $quantity)
{
    $product = array(
        "productid"=>$product_id,
        "price"=>$price_per_one,
        "quantity"=>$quantity, );
    return $product;
}


function check_comment_powerlink($comment, $label)
{
    // checks if the comment has content
    if ($comment!="") {
        $comment = $label. " " .$comment;
    } else {
        $comment="";
    }
    return $comment;
}
function get_comments_string_powerlink($comments)
{
    // go over the array and returns a string of the comments
    $receiver_comment = "";

    foreach ($comments as $comm) {
        if ($comm != "") {
            if ($receiver_comment != "") {
                $receiver_comment = $receiver_comment . ", ".$comm;
            } else {
                $receiver_comment  = $receiver_comment .$comm;
            }
        }
    }
    return $receiver_comment;
}

function get_code_coupon($order)
{
    foreach ($order->get_coupon_codes() as $coupon_code) {
        // Get the WC_Coupon object
        $coupon = new WC_Coupon($coupon_code);
        $code_coupon = $coupon->get_code();
        return $code_coupon;
    }
}



function on_order_complete()
{
    global $client;
    // global $order_id;
    $products = array();
    $order_id = get_the_ID();
    $order = wc_get_order($order_id);
    
    $purchase_date = $order->get_date_created();
    $purchase_date = date('Y-m-d', strtotime($purchase_date));
    
    // $apar = get_post_meta($order_id, "_billing_apar", true);
    // $apar = check_comment_powerlink($apar, "דירה");

    // $floor = get_post_meta($order_id, "_billing_floor", true);
    // $floor = check_comment_powerlink($floor, "קומה");

    // $leaving = get_post_meta($order_id, " _billing_leaving", true);
    // $leaving = check_comment_powerlink($leaving, "באין מענה להשאיר");

    // $comments=array($order->get_customer_note(), $apar,  $floor, $leaving);
    // $_comment = get_comments_string_powerlink($comments);

    $client["firstname"]= $order->get_billing_first_name();
    $client["lastname"]= $order->get_billing_last_name();
    $client["telephone"]= $order->get_billing_phone();
    $client["emailaddress"]= $order->get_billing_email();
    $client["billingstreet"]=$order->get_billing_address_1() . ' ' . $order->get_billing_address_2();
    $client["billingcity"]=$order->get_billing_city();
    // $client["comment"]=$_comment;
    
    $object = get_client_id();
    $code_coupon = "";
    $code_coupon = get_code_coupon($order);

    if ($object==null) {
        create_client();
        $object = get_client_id();
        $client["accountid"] = $object[0]->accountid;
    } else {
        $client["accountid"] = $object[0]->accountid;
        update_client();
    }


    foreach ($order->get_items() as $item_id => $item) {
        $product_id = $item->get_product_id();

        $settings = get_option("pl-product-".$product_id);
        $pl_quantity = $settings["Pquantity"];
        $pl_Id = $settings["PId"];

        $quantity = $item->get_quantity();
        $quantity =  $quantity * $pl_quantity;
        $total = $item->get_total();
        $price = $total/$quantity;
        $product = get_product_to_array($pl_Id, $price, $quantity);
        create_acquisition($product, $client, $purchase_date, $code_coupon);

        // $order->add_order_note($client["accountid"]);
    }

    foreach ($order->get_items('shipping') as $item_id => $item) {
        $shipping_method_instance_id = $item->get_instance_id(); // The instance ID
        $shipping_method_total       = $item->get_total();

        $settings = get_option("pl-product-"."Method".$shipping_method_instance_id);
        $pl_quantity = $settings["Pquantity"];
        $pl_Id = $settings["PId"];

        $quantity = 1;
        $quantity =  $quantity * $pl_quantity;
        $total = $shipping_method_total;
        $price = $total/$quantity;
        $product = get_product_to_array($pl_Id, $price, $quantity);
        create_acquisition($product, $client, $purchase_date, "");
    }
}

add_action('woocommerce_order_status_completed', 'on_order_complete', 10, 0);
