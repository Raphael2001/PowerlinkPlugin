<?php
if (! defined('ABSPATH')) {
    exit;
}

const BASE_URL= "https://api.powerlink.co.il/";

function remove_submenus()
{
    global $submenu;
    unset($submenu['themes.php'][10]); // Removes Menu
}
add_action('admin_menu', 'remove_submenus');
function powerlinkadminpage()
{
    add_menu_page('Power Link', 'Power Link', 'manage_options', 'members-slug', 'mainoptionPageContent');
    // add_submenu_page('members-slug', 'Client', 'Client', 'manage_options', 'add-members-slug', 'clientoptionPageContent');
}
     
add_action('admin_menu', 'powerlinkadminpage');
  

add_action('admin_menu', 'registerOptionPage');
function registerOptionPage()
{
    add_options_page('Power Link', 'Power Link', 'manage_options', 'powerlinkPluginSettings', 'mainoptionPageContent');
}
function mainoptionPageContent()
{
    ?>
    <h2>Power Link</h2>
    <p>These are the settings for your power link connection</p>



    <form method="POST" action="options.php">
        <?php
        settings_fields('powerlinkPluginSettings');

    $disabled =  get_option('pl-token')? "disabled" : ""; ?>
        <input type="text" class="form-control pl-token" value= "<?php echo get_option('pl-token'); ?>"
            id="pl-token" name = "pl-token" placeholder="טוקן" required >

        <?php
        
        if (get_option('pl-token')) {
            $wc_products= getallwcproducts();
            $pl_products = getallpowerlinkproducts();
            $wc_shipping_methods = get_active_shipping_methods();
            ?>
            <div class="row">
                <div class="col">
                    <table class="form-table " id="myTable" >
                        <tbody>
                            <tr valign="top" >
                                <td class="wc_emails_wrapper" colspan="2">
                                    <table class="wc_emails widefat" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th class="wc-product-name head">Woocommerce Product Name </th>
                                                <th class="pl-product-name head">PowerLink Product Name</th>
                                                <th class="wc-pl-product-quantity head">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        
                                        foreach ($wc_products as $wc_prod) {
                                            $settings = get_option("pl-product-".$wc_prod["Id"]); 
                                            if(!$settings)
                                            {
                                                $settings = array(); 
                                                $settings["PId"] = [];
                                                $settings['Pquantity'] =1;
                                            }

                                                ?>
                                                <tr>

                                                    <td class="wc-product-name">
                                                            <?php echo $wc_prod["Name"] ?>
                                                    </td>
                                                    <td class="pl-product-name chosen-select" >
                                                        <select class="form-control pl-product multi-select"
                                                        id="pl-product-<?php echo $wc_prod["Id"]?>" name = "pl-product-<?php echo $wc_prod['Id']?>[PId][]"style="width:150px;" required multiple >
                                                                        <?php
                                                                        foreach ($pl_products as $i=>$value) {

                                                                            ?>

                                                                                <option name = "pl-product-<?php echo $wc_prod['Id']?>[PId]" value = "<?php echo $value->productid?>" 
                                                                                 <?php echo in_array( $value->productid, $settings["PId"])?'selected' : ''?>><?php echo $value->name; ?></option>
                                                                            <?php
                                                                        } ?>
                                                        </select>
                                                    </td>
                                                    <td class="pl-wc-quantity">
                                                        <input type="text"id="pl-quantity-<?php echo $wc_prod["Id"]?>" name = "pl-product-<?php echo $wc_prod['Id']?>[Pquantity]" placeholder="כמות" class="form-control quantity" value= <?php
                                                            echo $settings['Pquantity'];
                                                         ?>
                                                       required>

                                                    </td>
                                                
                                            </tr>
                                            <?php
                                        } ?>
                                        </tbody>
                                        <tbody>
                                        <?php

                                        foreach ($wc_shipping_methods as $wc_method) {
                                            $settings = get_option("pl-product-".$wc_method["Id"]); ?>
                                            
                                            <tr>

                                                <td class="wc-method-name">
                                                    <?php echo $wc_method["Title"] ?>
                                                </td>
                                                <td class="pl-product-name">
                                                    <select class="form-control pl-product multi-select"
                                                        id="pl-product-<?php echo $wc_method["Id"]?>" name = "pl-product-<?php echo $wc_method['Id']?>[PId][]" style="width:150px;" required multiselect>
                                                            <?php
                                                                foreach ($pl_products as $i=>$value) {
                                                                    ?>
                                                                     <option name = "pl-product-<?php echo $wc_method['Id']?>[PId]" value = "<?php echo $value->productid?>" 
                                                                                 <?php echo in_array( $value->productid, $settings["PId"])?'selected' : ''?>><?php echo $value->name; ?></option>
                                                                    <?php
                                                                } ?>
                                                                                                    
                                                                                                
                                                    </select>
                                                </td>
                                                <td class="pl-wc-quantity">
                                                    <input type="text" class="form-control quantity" value= "<?php
                                                        if ($settings['Pquantity']) {
                                                            echo $settings['Pquantity'];
                                                        } else {
                                                            echo "1";
                                                        } ?>"
                                                            id="pl-quantity-<?php echo $wc_method["Id"]?>" name = "pl-product-<?php echo $wc_method['Id']?>[Pquantity]" placeholder="כמות" required>

                                                </td>
                                                                            
                                            </tr>
                                        <?php
                                        } ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">

                </div>
                
            </div>
            <?php
        }
        
    submit_button(__('שמור שינויים')); ?>
        <button type="reset" id ="reset_btn" onClick="delete_token();" >איפוס הגדרות</button>

    </form>

    <?php
}
add_action('admin_init', 'registerPluginSettings');
function registerPluginSettings()
{
    $wc_products= getallwcproducts();
    $wc_shipping_methods = get_active_shipping_methods();

    register_setting('powerlinkPluginSettings', 'pl-token');

    foreach ($wc_products as $wc_prod) {

        register_setting('powerlinkPluginSettings', 'pl-product-'.$wc_prod["Id"]);
    }
    foreach ($wc_shipping_methods as $wc_method) {
        register_setting('powerlinkPluginSettings', 'pl-product-'.$wc_method["Id"]);
    }
}

function getpowerlinkfields($recordnumber)
{
    $TOKENID = get_option('pl-token');

    $url = BASE_URL.'metadata/records/'.$recordnumber.'/fields';
    
   
    $result = basic_get_with_curl($url);
    $obj = json_decode($result);

    return $obj->data? $obj->data:false;
}

function getpowerlinkproduct($token, $pagenum)
{
    $url = BASE_URL .'api/query';
    $data = array(
        "objecttype" => "14",
        "sort_type" => "desc",
        "query"=> "(statuscode = 1)",
        "sort_by"=> "name",
        "page_number"=> "$pagenum",
        "page_size"=> "50",
  );

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
  'tokenid:' .$token,
  'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($curl);

    if (!curl_errno($curl)) {
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    }
   
    curl_close($curl);
    if ($http_code==200) {
        $obj = json_decode($result);
        return $obj;
    } else {
        return false;
    }
}

function getallpowerlinkproducts()
{
    $TOKENID = get_option('pl-token');
    $objects=array();
    $pagenum=1;
    $obj = getpowerlinkproduct($TOKENID, $pagenum);

    if ($obj == false) {
        return false;
    }
    $object =  $obj->data->Data;
    $objects =  $object;

    while ($obj->data->IsLastPage == false) {
        $pagenum ++;
        $obj = getpowerlinkproduct($TOKENID, $pagenum);
        $object =  $obj->data->Data;
        $objects = array_merge($objects, $object);
    }


    return $objects;
}

function getfieldvalues($recordnumber, $field)
{
    $TOKENID = get_option('pl-token');

    $url = BASE_URL.'metadata/records/'.$recordnumber.'/fields/'.$field.'/values';
    $result = basic_get_with_curl($url);
    $obj = json_decode($result);
    return $obj->data->values? $obj->data->values:false;
}

function getallwcproducts()
{
    $wc_products= array();
    $args = array(
            'post_type'=> 'product',
            'posts_per_page' => -1

    );


    $loop = new WP_Query($args);

    if ($loop->have_posts()): while ($loop->have_posts()): $loop->the_post();
    global $product;
    $productdetails = array(
            "Name"=> get_the_title(),
            "Id"=>get_the_ID()
        );
    array_push($wc_products, $productdetails);
    endwhile;
    endif;
    wp_reset_postdata();
    return $wc_products;
}

function get_active_shipping_methods()
{
    $active_methods   = array();

    // Get all your existing shipping zones IDS
    $zone_ids = array_keys(array('') + WC_Shipping_Zones::get_zones());

    // Loop through shipping Zones IDs
    foreach ($zone_ids as $zone_id) {
        // Get the shipping Zone object
        $shipping_zone = new WC_Shipping_Zone($zone_id);

        // Get all shipping method values for the shipping zone
        $shipping_methods = $shipping_zone->get_shipping_methods(true, 'values');

        // Loop through each shipping methods set for the current shipping zone
        foreach ($shipping_methods as $instance_id => $shipping_method) {
            $title = $shipping_method->get_title();
            $id = $shipping_method->get_instance_id();

            $shipping = array(
                'Title' => $title,
                'Id' => "Method".$id,
            );
            array_push($active_methods, $shipping);
        }
    }



    return $active_methods;
}
 

add_action('wp_ajax_delete_token', 'delete_token');

function delete_token()
{
    delete_option("pl-token");
}

function basic_get_with_curl($url)
{
    $TOKENID = get_option('pl-token');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt(
        $curl,
        CURLOPT_HTTPHEADER,
        array(
    'Content-Type: application/json',
    'tokenid:' .$TOKENID)
    );
    $result = curl_exec($curl);

    curl_close($curl);
    return $result;
}
