<?php

define('INVENTORY_FILE_NAME',        'yh-products.csv');
define('REPORTS_FILE_NAME',          'yuba-harvest-reports.csv');
define('VENDORS_FILE_NAME',          'vendor-list.pdf');
define('VENDOR_FILE_NAME_CSV',       'vendor-list.csv');

define('LOGIN',                      1);
define('LOGIN_SUBMIT',               2);
define('INVENTORY',                  3);
define('CLASSES',                    4);
define('VENDORS',                    5);
define('REFRESH',                    6);
define('LOGOUT',                     7);
define('INVENTORY_SELECTION',        8);
define('USER_BLANK',                 9);
define('INVALID_CREDENTIALS',       10);
define('LOGIN_VALID',               11);
define('EDIT_ITEM',                 12);
define('EDIT_ITEM_SUBMIT',          13);
define('NEW_CLASS',                 14);
define('NEW_CLASS_SUBMIT',          15);
define('NEW_CLASS_CANCEL',          16);
define('CLASS_BLANK',               17);
define('CLASS_EXISTS',              18);
define('NEW_CLASS_VALID',           19);
define('NEW_VENDOR',                20);
define('NEW_VENDOR_SUBMIT',         21);
define('NEW_VENDOR_CANCEL',         22);
define('EDIT_VENDOR',               24);
define('COMPANY_BLANK',             25);
define('COMPANY_EXISTS',            26);
define('NEW_VENDOR_VALID',          27);
define('EDIT_VENDOR_SUBMIT',        28);
define('EDIT_VENDOR_CANCEL',        29);
define('ASSIGN_ITEM_NO',            30);
define('EDIT_VENDOR_VALID',         31);

define('CLASS_NOT_SELECTED',        32);
define('VENDOR_NOT_SELECTED',       33);
define('ASSIGN_VALID',              34);

define('NUMBER_FIELD',              36);
define('CLASS_FIELD',               37);
define('VENDOR_FIELD',              38);
define('DESCRIPTION_FIELD',         39);
define('EDIT_CLASS',                40);
define('EDIT_CLASS_SUBMIT',         41);
define('EDIT_CLASS_CANCEL',         42);
define('EDIT_CLASS_DELETE',         43);
define('EDIT_CLASS_VALID',          45);
define('CONFIRM_CLASS_DELETE',      46);
define('CONFIRM_CLASS_CANCEL',      48);
define('CATEGORY_FIELD',            49);
define('EXPORT_INVENTORY',          50);
define('VENDOR_NAME',               51);
define('VENDOR_SORT',               52);
define('NEW_VENDOR_EXISTS',         53);
define('INVALID_VENDOR',            54);
define('EDIT_VENDOR_BLANK',         55);
define('COMPANY_NOT_FOUND',         56);
define('REPORTS',                   57);
define('DOWNLOAD_PAYMENTS',         58);
define('RESET_PAYMENTS',            59);
define('EXPORT_PAYMENTS',           60);
define('GENERATE_PAYMENT',          61);
define('CONFIRM_DELETE_VENDOR',     62);
define('DELETE_VENDOR_CONFIRMED',   63);
define('MAP_SKU',                   64);
define('EDIT_SKU',                  65);
define('NEW_MAPPING',               66);
define('NEW_MAPPING_SUBMIT',        67);
define('NEW_MAPPING_CANCEL',        68);
define('UPDATE_MAPPING',            69);

define('EXPORT_WINE',               70);
define('REPORTS_CONTINUE',          71);
define('EXPORT_VENDORS',            72);
define('LOGIN_INVENTORY',           73);
define('LOGIN_REPORTS',             74);
define('EXPORT_VENDORS_CSV',        75);
define('EDIT_ITEM_DELETE',          76);



define('GROUPS_FILE', 'groups.csv');
define('GROUPS_FILE1', 'groups1.csv');
define('NAME_PAD',                  60);

define('DOWNLOAD_DIR',      'download');
define('MAX_VENDOR_CHARACTERS',      8);

/*
  used in Model.php VendorSelectSegment to define the number of segements to return a list of
  vendors in
  this is called by edit_item.php which displays VENDOR_SEGMENTS horizontally 
  for example if there are 45 vendors and 2 segments, the first segment will be the 
  first 25 vendors and the second segment will the remaining 20 vendors
*/
define('VENDOR_SEGMENTS',            2);

// defines for printing pdf payment report
define('TEXT_PTS',          8);
define('TITLE_PTS',         9);
define('LEFT_MARGIN',       10);

define('LINE_HEIGHT',       12);
define('ITEM_HEIGHT',       14);
define('HALF_LINE_HEIGHT',  10);
define('QTY_XCOOR',         210);
define('PRICE_XCOOR',        50);
define('TOTAL_XCOOR',        50);


// has to do with password requirements
define('SPECIAL_CHARACTERS',              '@#$%&*()+-={}?.');
define('MIN_PASSWORD_LENGTH',             10);
define('MAX_PASSWORD_LENGTH',             20);

define('MIN_PASSWORD_NUMBERS',             2);
define('MAX_PASSWORD_NUMBERS',             2);

define('MIN_PASSWORD_SPECIAL',             2);
define('MAX_PASSWORD_SPECIAL',             2);

define('MIN_GENERATE_LENGTH',              6);
define('MAX_GENERATE_LENGTH',              6);



function ExecuteAutoFill($auto_fill) {
    $city = $state = $zip = '';
    switch ($auto_fill) {
    default:
    case 0:
      break;
    case 1:
      $city  = 'Oregon House';
      $state = 'CA';
      $zip   = '95962';
      break; 
     case 2:
      $city  = 'Marysville';
      $state = 'CA';
      $zip   = '95901';
      break;
     case 3:
      $city  = 'Yuba City';
      $state = 'CA';
      $zip   = '95991';
      break;
  }
  return array($city,$state,$zip);
}
/* 
  auto fill
  called by new_vendor.php and edit_vendor.php to auto some items
*/
function AutoFill() {
   $stream = '';
      $stream .= '<select name="AutoFill" OnChange="document.form1.submit();" style="width:100px;">';
      $stream .= '<option value="0">Select Fill</option>';
     

     $stream .= "<option value=\"1\">Oregon House</option>";
     $stream .= "<option value=\"2\">Marysville</option>";
     $stream .= "<option value=\"3\">Yuba City</option>";
      
      $stream .= '</select>';
      return $stream;

}
/*
  load the groups list from the file ../groups.csv
  Wine Sales,Wine
  Kitchen,Food,Lunch and Dinner,Pastries/Breads,Kitchen
  Retail Sales,Home & Bath,Olive Oil,Art,Jams/Spreads/Sauses,Cheese,Beverages,Meats/Seitan,Beer,Olives/Oils/Vinegards,Produce,
  Other,Yuba Harvest
  returns a list
  list[0] => Wine Sales, Wine, Wine Bottles
  list[1] => Kitchen,Food,Lunch and Dinner,Pastries/Breads,Kitchen
  list[2] => Retail Sales,Home & Bath,Olive Oil,Art,Jams/Spreads/Sauses,Cheese,Beverages,Meats/Seitan,Beer,Olives/Oils/Vinegards,Produce,Crafts
  list[3] => Other,Yuba Harvest
  Wine:
  Wine Sales
  Wine Bottles
  Kitchen:
  Food
  Lunch and Dinner
  Pastries/Breads/Kithen
  ...
*/
/*
function LoadGroupList() {
  $list = array(); $count = 0;
  $data = file_get_contents(GROUPS_FILE);
  $lines = explode("\n",$data);
  for ($i = 0; $i < count($lines); $i++) array_push($list,$lines[$i]);
  
  return $list;
}
*/


function LoadGroupList() {
  $trace = false;
  $data = file_get_contents(GROUPS_FILE);
  $entries = array(); $count = 0;
  $tok = strtok($data, "\n");
  while ($tok !== false) {
    array_push($entries,$tok);
    $tok = strtok("\n");
  }
  //Dump("LoadGroupList entries tok",$entries);
  $list = array();
  $toks = '';
  for ($i = 0; $i < count($entries); $i++) {
    $token = $entries[$i];
    $pos = strpos($token,':');
    if ($pos > 0) {
      $token1 = chopstr($token) . ',';

      if ($toks != '') {
        $toks = chopstr($toks);
        array_push($list,$toks);
        $toks = '';
      }
  
    }
    else $toks .= $token . ',';
  }
  if ($toks != '') {
        $toks = chopstr($toks);
        array_push($list,$toks);
      }
  if ($trace) Dump("LoadGroupList entries list",$list);
  return $list;
}


/*
given:  RET-KIMHAWT-103
  return: RET
*/
function ExtractClass($sku) {
  return substr($sku,0,3);

}
/*
  given:  RET-KIMHAWT-103
  return: KIMHAWT
*/
function ExtractVendor($sku) {
    $vendor = substr($sku,4);
    $vendor = substr($vendor,0,8);
    return $vendor;
}
/*
  given:  RET-KIMHAWT-103
  return: 103
*/
function ExtractNumber($sku) {
  return substr($sku,12);
}
/*
  download payments report, called by model DownloadSales
  Array
(
    [0] => stdClass Object
        (
            [device] => stdClass Object
                (
                    [name] => Sirius iPad
                )

            [net_total_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 1550
                )

            [created_at] => 2014-09-01T16:36:46Z
            [total_collected_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 1550
                )

            [payment_url] => https://squareup.com/dashboard/sales/transactions/9MSXTH7AZJFH3
            [refunded_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 0
                )

            [additive_tax] => Array
                (
                )

            [processing_fee_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 0
                )

            [refunds] => Array
                (
                )

            [merchant_id] => CNSF9XCJV4DZV
            [inclusive_tax_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 0
                )

            [id] => 9MSXTH7AZJFH3
            [additive_tax_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 0
                )

            [itemizations] => Array
                (
                    [0] => stdClass Object
                        (
                            [name] => Coffee - 12 oz.
                            [quantity] => 2.00000000
                            [item_variation_name] => Regular Price
                            [item_detail] => stdClass Object
                                (
                                    [category_name] => Beverages
                                    [sku] => 
                                    [item_id] => F1DB82B1-9C09-47BC-8AAA-4EE6DBF098E7
                                    [item_variation_id] => A0F3EC4E-A677-402D-A3AC-90E9F7E28623
                                )

                            [total_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 350
                                )

                            [single_quantity_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 175
                                )

                            [gross_sales_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 350
                                )

                            [discount_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 0
                                )

                            [net_sales_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 350
                                )

                            [taxes] => Array
                                (
                                )

                            [discounts] => Array
                                (
                                )

                            [modifiers] => Array
                                (
                                )

                        )

                    [1] => stdClass Object
                        (
                            [name] => Croissant - Almond
                            [quantity] => 3.00000000
                            [item_variation_name] => Regular Price
                            [item_detail] => stdClass Object
                                (
                                    [category_name] => Pastries/Breads
                                    [sku] => 
                                    [item_id] => 9723054C-9722-4AA1-8006-34AE28F0B054
                                    [item_variation_id] => C30EDE35-F89B-4C69-B33D-DCE8CD0E3BD4
                                )

                            [total_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 900
                                )

                            [single_quantity_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 300
                                )

                            [gross_sales_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 900
                                )

                            [discount_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 0
                                )

                            [net_sales_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 900
                                )

                            [taxes] => Array
                                (
                                )

                            [discounts] => Array
                                (
                                )

                            [modifiers] => Array
                                (
                                )

                        )

                    [2] => stdClass Object
                        (
                            [name] => Croissant - Chocolate
                            [quantity] => 1.00000000
                            [item_variation_name] => Regular Price
                            [item_detail] => stdClass Object
                                (
                                    [category_name] => Pastries/Breads
                                    [sku] => 
                                    [item_id] => 16B2B56D-8E8D-4637-BA6F-29BB07759579
                                    [item_variation_id] => 04EC516F-94E2-476D-BFC2-B52F3FC968CC
                                )

                            [total_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 300
                                )

                            [single_quantity_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 300
                                )

                            [gross_sales_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 300
                                )

                            [discount_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 0
                                )

                            [net_sales_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 300
                                )

                            [taxes] => Array
                                (
                                )

                            [discounts] => Array
                                (
                                )

                            [modifiers] => Array
                                (
                                )

                        )

                )

            [tender] => Array
                (
                    [0] => stdClass Object
                        (
                            [type] => CASH
                            [name] => Cash
                            [total_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 1550
                                )

                            [tendered_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 2000
                                )

                            [change_back_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 450
                                )

                        )

                )

            [tax_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 0
                )

            [swedish_rounding_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 0
                )

            [creator_id] => CNSF9XCJV4DZV
            [discount_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 0
                )

            [tip_money] => stdClass Object
                (
                    [currency_code] => USD
                    [amount] => 0
                )

            [inclusive_tax] => Array
                (
                )

            [receipt_url] => https://squareup.com/receipt/preview/9MSXTH7AZJFH3
        )

*/


function InPayments($hash) {
   $db = new Database();
   $db->query("SELECT * FROM payments where hash='$hash'");
   if ($db->numRows() != 0) return true;
   return false;
}


/* 
  parse zulu time
        012345678901234567890
                   HH MM SS
  given 2014-09-25T00:11:49Z
  return date:  9/25/2014
         hours: 00:11
*/
function parseZulu($zulu_time) {
  $trace = false;

  
  $year = substr($zulu_time,0,4);
  $mon  = substr($zulu_time,5,2);
  $day  = substr($zulu_time,8,2);
  $hour = substr($zulu_time,11,2);
  $min  = substr($zulu_time,14,2);
  $sec  = substr($zulu_time,17,2);
  
  
  if ($trace) WM("zulu_time:$zulu_time year:$year mon:$mon day:$day hour:$hour min:$min");
  $time = strtotime("$mon/$day/$year") + $hour * 3600 + $min * 60;





  //$mdy = $mon . '/' . $day . '/'. $year;
  //$time = strtotime($mdy);
  $time -= 7 * 3600;



  $mdy = date('m/d/Y',$time);
  $hr = date('G',$time);
  //$mn  = date('i',$time);
  //$sc  = date('s',$time);


  if ($trace) WM("mdy:$mdy hr:$hr mn:$mn sec:$sec");
  return array($mdy,$hr,$min,$sec);

}
/*
 query Square to get payments 
 https://connect.squareup.com/v1/me/payments?begin_time=2013-01-15T00:00:00Z&end_time=2013-01-31T00:00:00Z
 to get from PST to Zulu time, add 7 hours (8 hours)
 called by DownloadPaymentsReport
 typical url

 https://connect.squareup.com/v1/me/payments?begin_time=2014-12-22T07:00:00Z&end_time=2014-12-23T06:59:59Z&limit=200
*/
function getSquarePaymentsDayof($date) {
   $trace = false;
  $begin_time = date('Y-m-d',strtotime($date));
  $et = strtotime($date);
  $et += 86400;

  $end_time   = date('Y-m-d',$et);
  //$begin_time .= 'T00:07:00Z';
  //$end_time   .= 'T23:06:59Z';
  //$begin_time .= 'T07:00:00Z';
  //$end_time   .= 'T06:59:59Z';
  $begin_time .= 'T08:00:00Z';
  $end_time   .= 'T07:59:59Z';  // makes end time 24 hrs - 1 minute


  $url     = "https://connect.squareup.com/v1/me/payments?begin_time=$begin_time&end_time=$end_time&limit=200";

  if ($trace) WM("getSquareSales URL:$url");
 

  $headers = array('Accept: application/json', 'Authorization: Bearer SOB5xLqH56SB6vClDT6A1g');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  $response = curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));//, CURLOPT_VERBOSE => 1));
  $json = curl_exec($ch);
  curl_close($ch);
  return $json;
}
/*
check for json error
ex: json:{"type":"bad_request","message":"Invalid value for parameter `end_time`"}

*/
function JsonErrorCheck($json) {
  if (strpos($json,'bad_request') != 0) {
    $msg_pos = strpos($json,'message":"')+10;
     
    $msg = substr($json,$msg_pos);
    $msg = str_replace('"}','',$msg);
    return $msg;
  }
  return '';
}
/*
  returns
  true  - hash is in payments
  false - hash is not in payments
*/
function InHashInPayments($hash) {
  $db = new Database();
  $db->query("SELECT * FROM payments where hash='$hash'");
  if ($db->numRows() == 0) return false;
  return true;
}
/*
  for some sales, the class part of SKU value is incorrect.  For example
       IS                 SHOULD BE
  ALC-BANGORRA-104    WIN-BANGORRRA-

  Grant Eddie - Cabernet Sauvignon - 2009  WIN-GRANTED*-107
  Grant Eddie - Port - 2012                WIN-GRANTED*-106
  Grant Eddie - White Pearl - 2011         WIN-GRANTED*-110
  Grant Eddie - Syrah - 2009               WIN-GRANTED*-109

  Renaissance - Mediterranean Red - 2005  WIN-RENAISSA-112
  Renaissance - Semillon - 2013           WIN-RENAISSA-114

  Renaissance - Mediterranean Red - 2006  WIN-RENAISSA-113
  Renaissance - Syrah - 2002              WIN-RENAISSA-121

  Bangor Ranch - Sangiovese - 2012        WIN-BANGORRA-105

 

*/
function RemapSKU($name,$sku) {
  $name = trim($name);
  if ($name == 'Grant Eddie - Cabernet Sauvignon - 2009' && $sku == '') $sku = 'WIN-GRANTED*-107';
  if ($name == 'Grant Eddie - Port - 2012' && $sku == '')        $sku = 'WIN-GRANTED*-106';
  if ($name == 'Grant Eddie - White Pearl - 2011' && $sku == '') $sku = 'WIN-GRANTED*-106';
  if ($name == 'Grant Eddie - White Pearl - 2011' && $sku == '') $sku = 'WIN-GRANTED*-110';
  if ($name == 'Grant Eddie - Syrah - 2009' && $sku == '')       $sku = 'WIN-GRANTED*-109';
  if ($name == 'Renaissance - Mediterranean Red - 2005' && $sku == '') $sku = 'WIN-RENAISSA-112';
  if ($name == 'Renaissance - Mediterranean Red - 2006' && $sku == '') $sku = 'WIN-RENAISSA-113';
  if ($name == 'Renaissance - Syrah - 2002' && $sku == '') $sku = 'WIN-RENAISSA-121';

  if ($name == 'Bangor Ranch - Sangiovese - 2012' && $sku == '') $sku = 'WIN-BANGORRA-105';
  //if ($name == '' && $sku == '') $sku = '';


  return $sku;
}
/*
  get rid of empty Custom Amount
  if the name is Custom Amount return false
  otherwise return true
*/
function FilterName($name) {
  $name = trim($name);
  if ($name == 'Custom Amount') return false;
  return true;
}
/*
 called by Controller DOWNLOAD_PAYMENTS
 returns status error message if json_error or blank if not

*/
 function DownloadPaymentsReport($begin_date,$end_date) {
  $trace = false;
  if ($trace) WM("DownLoadPaymentsReport begin_date:$begin_date");
  if ($trace) WM("DownLoadPaymentsReport   end_date:$end_date");
  $begin_date_time = strtotime($begin_date);
  $end_date_time   = strtotime($end_date);
  $begin_date = date('m/d/Y',$begin_date_time);
  $end_date = date('m/d/Y',$end_date_time);
  if ($trace) WM("DownLoadPaymentsReport begin_date:$begin_date end_date:$end_date");

  $db = new Database();

  
  $numDays = abs($begin_date_time - $end_date_time)/60/60/24;
  $this_day = $begin_date_time;

  $numDays++;
   
  $write_json = false;
  $filename = "json.txt";
  if ($write_json) {
    WM("DownLoadPaymentsReport begin_date:$begin_date end_date:$end_date");
    $fh = fopen($filename,'w');
    fclose($fh);
    for ($day = 0; $day < $numDays; $day++) {

      $bd = date('m/d/Y',$this_day);
    

    
      $json_data = getSquarePaymentsDayof($bd);
      $fh = fopen($filename,'a+');
      fwrite($fh,$json_data);
      fclose($fh);

    }
    return;
  }

  if ($trace) WM("DownLoadPaymentsReport numDays:$numDays begin_date_time:$begin_date_time end_date_time:$end_date_time");
  $max_outer = '';
  for ($day = 0; $day < $numDays; $day++) {
    
    $bd = date('m/d/Y',$this_day);
    

    
    $json = getSquarePaymentsDayof($bd);
    $json_error = JsonErrorCheck($json);
    if ($json_error != '') {
      return $json_error;
    }
    //$json_data = str_replace('},',"},\n",$json);
    //$fh = fopen($filename,'a+');
    //fwrite($fh,$json_data);
    //fclose($fh);
    if ($trace) Dump("getSquarePaymentsDayof obj",$obj);

    $obj = json_decode($json);
    if ($trace) WM("DownLoadPaymentsReport day:$day this_day:".date('m/d/Y',$this_day));
    
    $outer_count = 1;

    foreach ($obj as $key) {
      
      $created_at = $zulu_time = $key->created_at;
    
    
      list ($mdy_text,$hour,$min,$sec) = parseZulu($zulu_time);
    
      $mdy = strtotime($mdy_text);
      $arr = objectToArray($key->itemizations);

      $inner_count = 1;
      for ($i = 0; $i < count($arr); $i++) {
        error_reporting(0);
        $sku = $net = '';
      
        $name      = $arr[$i]['name'];
        $quantity  = round($arr[$i]['quantity']);
        $id        = $arr[$i]['item_detail']['item_id'];
        $category  = $arr[$i]['item_detail']['category_name'];
        $sku       = $arr[$i]['item_detail']['sku'];
        $item_cost = $arr[$i]['single_quantity_money']['amount'];
        $gross     = $arr[$i]['gross_sales_money']['amount'];
        $discount  = $arr[$i]['discount_money']['amount'];
        $net       = $arr[$i]['net_sales_money']['amount'];

        $taxes     = $arr[$i]['taxes'];
        $sales_tax = 0;
        if (count($taxes) != 0) $sales_tax = $taxes[0]['rate'] * 10000;


/*
                          [net_sales_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 350
                                )

                            [taxes] => Array
                                (
                                )

*/

        //$sku = RemapSKU($name,$sku);
        error_reporting(E_ALL);

        // remove extraneous commas, as they really foul up the cvs export
        $name      = str_replace(',',' ',$name);
        $category  = str_replace(',',' ',$category);

        $product_name = addslashes($name);
        $category_key = addslashes(trim($category));
        $category_key = addslashes($category_key);
        $class        = substr($sku,0,3);
        $hash         = hash('sha256', $name.$id.$sku.$net.$mdy.$hour.$min);
        if (!InHashInPayments($hash)) {
          if (FilterName($name)) {


            if ($trace) WM("$outer_count $inner_count $mdy_text name:$name quantity:$quantity id:$id sku:$sku net:$net ");
            $db->query("INSERT INTO payments (created_at,mdy,hour,min,sec,product_name,quantity,item_cost,id,category_key,sku,class,gross,discount,net,hash,sales_tax) VALUES(
            '$created_at',
            '$mdy',
            '$hour',
            '$min',
            '$sec',
            '$product_name',
            '$quantity',
            '$item_cost',
            '$id',
            '$category_key',
            '$sku',
            '$class',
            '$gross',
            '$discount',
            '$net',
            '$hash',
            '$sales_tax'

            )");
            $inner_count++;
          }
        }
      }
      $outer_count++;
      if ($max_outer < $outer_count) $max_outer = $outer_count;
    }
    
    $this_day += 86400;

    if ($trace) WM("");
  }
  return "there were at most $max_outer sales in one day";
}
  
/*
  determine if valid vendor field
  a valid vendor field has only letter and/or a *
  and it must start with a letter
*/
function ValidVendor($vendor) {
  if ($vendor == '') return false;
  $ch = substr($vendor, 0, 1);
  if ($ch == '*') return false;
  if (!(preg_match('/[A-Z]/',$vendor) || preg_match('/\*/',$vendor))) return false;
  return true;

}
/*
  get last field
*/
function getLastField($field) {
  $field = strtolower($field);
  $stream = '';
  $end = strlen($field) - 1;
  for ($i = 0; $i < strlen($field); $i++) {
    $ch = substr($field, $end--, 1);
    if ($ch == ' ') break;
    $stream .= $ch;
  }
  $stream = strrev($stream);
  return $stream;
}

function getFirstLast($name) {
  $first = $last = '';
  $tokens = array(); $count = 0;
  $tok = strtok($name, " ");
  while ($tok !== false) {
    $tokens[$count++] = $tok;
    $tok = strtok(" ");
  }
  if ($count == 0) return array($first,$last);
  if ($count == 1) return array($first,$name);

  for ($i = 0; $i < $count-1; $i++) {
    $first .= $tokens[$i] . ' ';
  }
  $last = $tokens[$count-1];
  $first = chopstr($first);
  return array($first,$last);

}
/*
  split sku into three component fields
*/
function SplitSKU($sku) {
  $tokens = array(); $count = 0;
  $tok = strtok($sku, '-');
  $string = '';
  while ($tok !== false) {
    $tokens[$count++] = $tok;
    $tok = strtok('-');
  }
  if ($count < 3) return array('','','');
  return array($tokens[0],$tokens[1],$tokens[2]);
}
/*
  build SKU from class, vendor and item_number
*/
function  buildSKU($class,$vendor,$item_number) {
    
    $value = "$class-$vendor-$item_number";
    return $value;
}    

/*
  put data to square database
  item_id      - item id
  variation_id - sku 

  [3] => stdClass Object
        (
            [visibility] => PUBLIC
            [available_online] => 
            [available_for_pickup] => 1
            [id] => 05710c72-90a1-42e2-a82a-51a45ba3909d  <----- Item ID
            [name] => Beth - Sutter Buttes Barn
            [category_id] => 8A8B98C9-5E8E-4F9B-BD23-E6460B36D039
            [category] => stdClass Object
                (
                    [id] => 8A8B98C9-5E8E-4F9B-BD23-E6460B36D039
                    [name] => Art
                )

            [variations] => Array
                (
                    [0] => stdClass Object
                        (
                            [pricing_type] => FIXED_PRICING
                            [track_inventory] => 
                            [inventory_alert_type] => NONE
                            [id] => 89f7fc4d-de5b-45c9-a22c-02b82c91b5cb  <---- Variation ID
                            [name] => Regular
                            [price_money] => stdClass Object
                                (
                                    [currency_code] => USD
                                    [amount] => 25000
                                )

                            [sku] => SBB
                            [ordinal] => 1
                            [item_id] => 05710c72-90a1-42e2-a82a-51a45ba3909d
                            [inventory_alert_threshold] => 0
                        )

                )

        )
  Update Variation 
  PUT /v1/{merchant_id}/items/{item_id}/variations/{variation_id}
  
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
  curl_setopt($ch, CURLOPT_PUT, 1);
  ?>

  are "useless" without;

   
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-HTTP-Method-Override: PUT'));

  curl_setopt($ch, CURLOPT_POSTFIELDS, $json)
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


  curl -trace -verbose  -X PUT -H 'Authorization: Bearer SOB5xLqH56SB6vClDT6A1g' 
  -H 'Content-Type: application/json' 
  -H 'Accept: application/json'  
  -d '{"sku":"ART-BETHJO-103"}' 
  https://connect.squareup.com/v1/me/items/05710c72-90a1-42e2-a82a-51a45ba3909d/variations/89f7fc4d-de5b-45c9-a22c-02b82c91b5cb

  curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_URL => $url, 
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => $json,
    CURLINFO_HEADER_OUT => 1
    ));

*/
function putSquareVariations($item_id,$variation_id,$field,$value) {
  $trace = false;
  $json = "{\"$field\":\"$value\"}";
  $url     = 'https://connect.squareup.com/v1/me/items/'.$item_id.'/variations/'.$variation_id;
  $headers = array('Accept: application/json', 'Content-Type: application/json', 'Authorization: Bearer SOB5xLqH56SB6vClDT6A1g');

  $ch = curl_init();
  

  curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_URL => $url, 
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_CUSTOMREQUEST => 'PUT',
    CURLOPT_POSTFIELDS => $json,
    CURLINFO_HEADER_OUT => 1
    ));


  $response = curl_exec($ch);
  


  curl_close($ch);
  return $response;

}

/*
  object to array
*/
function objectToArray($d) {
    if (is_object($d)) {
      // Gets the properties of the given object
      // with get_object_vars function
      $d = get_object_vars($d);
    }
 
    if (is_array($d)) {
      /*
      * Return array converted to object
      * Using __FUNCTION__ (Magic constant)
      * for recursive call
      */
      return array_map(__FUNCTION__, $d);
    }
    else {
      // Return array
      return $d;
    }
  }

/*
  retrive item info, called by Model DumpItemInfo
*/
function RetrieveItemInfo($item_id) {
  

  $url     = "https://connect.squareup.com/v1/me/items/$item_id";

  

  $headers = array('Accept: application/json', 'Authorization: Bearer SOB5xLqH56SB6vClDT6A1g');
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

  curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));//, CURLOPT_VERBOSE => 1));


  $json = curl_exec($ch);
  curl_close($ch);

  return $json;
}
/* 
  get inventory data from square database 
  called by Refresh in Model.php
  What is sent to server

  GET /v1/me/items HTTP/1.1

  Host: connect.squareup.com

  Accept: application/json

  Authorization: Bearer SOB5xLqH56SB6vClDT6A1g

*/
function getSquareData() {

  $url     = 'https://connect.squareup.com/v1/me/items';
  $headers = array('Accept: application/json', 'Authorization: Bearer SOB5xLqH56SB6vClDT6A1g');

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  //curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
  curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_URL => $url));//, CURLOPT_VERBOSE => 1));

  

  $json = curl_exec($ch);

  //$sent_request = curl_getinfo($ch, CURLINFO_HEADER_OUT);
  //WriteMsgRaw("sent_request:$sent_request");

  curl_close($ch);

  return $json;
}
/*
  return the field formated for being an SKU
  a) six letters are returned
  b) everything is uppercase
  c) if the length is not six, * are appeneded

*/
function getVendorField($field) {

  $tokens = array(); $count = 0;
  $tok = strtok($field, ' ');
  $string = '';
  while ($tok !== false) {
    $string .= $tok;
    $tok = strtok(' ');
  }

  $temp = substr($string,0,MAX_VENDOR_CHARACTERS);
  $temp = strtoupper($temp);

  $stream = '';
  for ($i = 0; $i < strlen($temp); $i++) {
    $ch = substr($temp, $i, 1);
    $ord = ord($ch);
    if ($ord < 65 || $ord > 90) $ch = '';
    $stream .= $ch;
  }

  $stream = pad($stream, MAX_VENDOR_CHARACTERS, '*');

  return $stream;
}

/* invert input */
function Invert($is,$one,$two) {
  if ($is == $one) return $two;
  if ($is == $two) return $one;
  return $is;
}
function wordTokens($str) {
  $tokens = "";
  $count = 0;
  $tok = strtok($str, " ");

  while ($tok !== false) {
    $tokens[$count++] = $tok;
    $tok = strtok(" ");
  }
  return array($tokens,$count);
}

/*
  make a array
*/
function MakeArray($str) {
  $tokens = array(); $count = 0;
  $tok = strtok($str, " ");
  while ($tok !== false) {
    $tokens[$count++] = $tok;
    $tok = strtok(" ");
  }
  return $tokens;
}

/*
 * return true if look matches word for length
 */
function MatchLen($look,$len,$word) {
  $look   = strtolower($look);
  $word   = strtolower($word);
  $look   = str_replace("-"," ",$look);
  $word   = str_replace("-"," ",$word);
  $i = 0;
  while ($len-- > 0) {
    $ch1 = substr($look, $i, 1);
    $ch2 = substr($word, $i, 1);
    $i++;
    if ($ch1 != $ch2) return false;
  }
  return true;
}

function wordSearchMatch($words1,$words2) {
  if (count($words1) == 0 || count($words2) == 0) return false;
  for ($i = 0; $i < count($words1); $i++) for ($j = 0; $j < count($words2); $j++) 
    if (MatchLen($words1[$i],strlen($words1[$i]),$words2[$j])) return true;
  return false;
}
/*
 * determine if words1 list matches any word in words2 list
 * need only match words2 to the length of each word in words1 list
 */
function wordsMatch($words1,$count1,$words2,$count2) {
  if ($count1 == 0 || $count2 == 0) return false;
  for ($i = 0; $i < $count1; $i++) for ($j = 0; $j < $count2; $j++) {

    if (MatchLen($words1[$i],strlen($words1[$i]),$words2[$j])) return true;

  }
  return false;
}


/* 
  put leading 0's before num
  num - the number to put zeros before
  leading the count of the number of zeros
*/
function FormatZeros($num,$leading) {

  $len = strlen($num);
  $ldiff = $leading - $len;
  if ($ldiff < 0) $ldiff = $leading;

  $lz = '';
  for ($i = 0; $i < $ldiff; $i++) $lz .= '0';


  return $lz . $num;
}
function getFileType($type_number) {
  if ($type_number == 1) return ".gif";
  if ($type_number == 2) return ".jpg";
  if ($type_number == 3) return ".png";
  if ($type_number == 6) return ".bmp";
  return '';
}
/*
 * normalize the book image so that is exactly $image_width pixels wide
 */
function NormalizeImageWidth($targetpath,$image_size) {
  list($width, $height, $type, $attr) = getimagesize($targetpath);

  /*
    scale the image so that the max width or height is image_size
  */

  $scalew = $image_size  / $width;
  $scaleh = $image_size  / $height;

  $scale = $scalew;
  if ($scaleh < $scalew) $scale = $scaleh;

  $new_width = $width * $scale;
  $new_height = $height * $scale;



  $new_image = imagecreatetruecolor($new_width,$new_height);
  $image = "";

  $fp = $targetpath;

  switch ($type) {
    case 1: // .gif
      $image = imagecreatefromgif($targetpath);
      imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
      imagegif($new_image, $fp, 100);
      break;
    case 2: // .jpg
      $image = imagecreatefromjpeg($targetpath);
      imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
      imagejpeg($new_image, $fp, 100);
      break;
    case 3: // .png
      $image = imagecreatefrompng($targetpath);
      imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
      imagepng($new_image, $fp, 1);
      break;
  }
  return array($new_width, $new_height);
}

/*
 * empty call stack for this user
*/
function ResetCallStack() {
  $uid = $_SESSION['UID'];
  $db = new Database_lets();
  $db->query("DELETE FROM call_stack WHERE UID='$uid'");

}


/*
    if amount is negative, return as positive number (take 1's complement)
    sign 0 - amount is positive
    sign 1 - amount is negative
*/
function getSignedValue($amount) {
  if ($amount >= 0) return array($amount,0);
  $amount = -$amount;
  return array ($amount,1);
}


/*
  determine if character is a-z or A-Z
*/
function is_char($chr) { 
 
  return ereg("[a-zA-Z]",$chr);
} 



  
$DaysInMonth = array (31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
$MonthLabels = array ("January", "Feburary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
$MonthAbbr = array ("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
$Weekdays = array ("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
$WeekdaysAbbr = array ("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat");


function getMonthName($month) {
  global $MonthLabels;
  return $MonthLabels[$month-1];
}
function getWeekDay($day) {
  global $WeekdaysAbbr;
  return $WeekdaysAbbr[$day];
}

//Takes a password and returns the salted hash
//$password - the password to hash
//returns - the hash of the password (128 hex characters)
function HashPassword($password) {
  $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); //get 256 random bits in hex

  $hash = hash("sha256", $salt . $password); //prepend the salt, then hash
  //store the salt and hash in the same string, so only 1 DB column is needed

  $final = $salt . $hash;
  return $final;
}
//Validates a password
//returns true if hash is the correct hash for that password
//$hash - the hash created by HashPassword (stored in your DB)
//$password - the password to verify
//returns - true if the password is valid, false otherwise.
function ValidatePassword($password, $correctHash) {
  $salt = substr($correctHash, 0, 64); //get the salt from the front of the hash
  $validHash = substr($correctHash, 64, 64); //the SHA256
  $testHash = hash("sha256", $salt . $password); //hash the password being tested
  //if the hashes are exactly the same, the password is valid
  return $testHash === $validHash;
}

// remove all files in dir with extension ext
function RemoveFiles($dir,$ext) {
  error_reporting(0);
  foreach(glob($dir."/*".$ext) as $file) {
    unlink($file);
  }
  error_reporting(E_ALL);
}

function ValueInList($value,$list,$field) {

  if ($list == '') {
    return false;
  }
  for ($i = 0; $i < count($list); $i++) {
   
    if ($value == $list[$i]["$field"]) {
  
      return true;
    }
  }

  return false;
}
// determine if value is in list
function FindInList($list,$value) {

  for ($i = 0; $i < count($list); $i++) if ($value == $list[$i]) return true;
  return false;
}
// determine if value is in list
function inList($value,$list,$count) {
  if ($list == '') return false;
  if ($count == 0) return false;
  for ($i = 0; $i < $count; $i++) if ($value == $list[$i]) return true;
  return false;
}

/*
  given amount as cents, return as cents with decimal point
*/
function formatGivenCents($amt) {
 $trace = false;
 list ($dollars, $cents) = getDollarsCents($amt);

 

 if ($cents == '00') return $dollars;
 return "$dollars.$cents";
}
function FormatAmountCVS($amount) {
  $sign = 1;
  if ($amount < 0) {
    $sign = 0;
    $amount = -$amount;
  }
  list ($dollars, $cents) = getDollarsCents($amount);
  $dollars = formatComma($dollars);
  $value = $dollars . '.' . $cents;
  if ($sign == 0) $value = '-'.$value;
  return $value;
}
/*MY_ACCOUNT_SEND
 * format amount in cents into a right aligned dollar.cents string
* the string returns an 12 character string
* $ 9,999,999.99
*/
function FormatAmount($total,$size) {

  /* $total is in cents, turn into dollars and cents */
  list ($dollars, $cents) = getDollarsCents($total);

  $dollars = formatComma($dollars);

  $dpad = padright($dollars,$size,' ');

  $amt = $dpad . '.' . $cents;

  return $amt;
}

/*
  Convert amount to cents. If input string has no decimal point
  return multiplied by 100
  else strip decimal point and return
  44.9
  449 44
*/
function ConvertToCents($amt) {
  $dp = stripos($amt,'.');
  $value = str_replace('.','',$amt);
  $value = $amt * 100;
  return $value;
}
/*MY_ACCOUNT_SEND
 * format amount into a dollar.cents string
* the string returns an 12 character string
* $ 9,999,999.99
* except if cents is zero, return just the dollars
*/
function FormatAmountOnly($total) {
  
  
  /* $total is in cents, turn into dollars and cents */
  list ($dollars, $cents) = getDollarsCents($total);
   
  $dollars = formatComma($dollars);

  if ($cents != '00') $amt = $dollars . '.' . $cents;
  else $amt = $dollars;
 
  return $amt;
}
/* 
  given price as cents, return array of dollars, cents
  cents less than 10 are prepended by a 0
*/
function getDollarsCents($price) {
  $price_dollars = intval($price/100);
  $price_cents = $price - ($price_dollars*100);
  if ($price_cents < 10) $price_cents = '0' . $price_cents;
  return array($price_dollars,$price_cents);
}
/* format number string with commas */
function formatComma($str) {
  $count = 0; $value = ' ';
  $str = strrev($str);
  for ($i = 0; $i < strlen($str); $i++) {
    $ch = substr($str, $i, 1);
    if ($count == 3) {
      $value .= ',';
      $count = 0;
    }
    $value .= $ch;
    $count++;
  }
  $value = strrev($value);
  
  return trim($value);
}

/*
 * transform problem codes
*/
function tinymce_substitutions($value) {
  $value = str_replace("&ndash;",'-',$value);
  $value = str_replace("&ldquo;",'"',$value);
  $value = str_replace("&rdquo;",'"',$value);
  $value = str_replace("&acute;","'",$value);
  $value = str_replace("&quot;",'"',$value);
  $value = str_replace("&apos;","'",$value);
  $value = str_replace("&amp;","&",$value);
  $value = str_replace("&lt;","<",$value);
  $value = str_replace("&gt;",">",$value);
  $value = str_replace("&fac14;","1/4",$value);
  $value = str_replace("&fac12;","1/2",$value);
  $value = str_replace("&fac34;","3/4",$value);
  $retvalue = '';
  for ($i = 0; $i < strlen($value); $i++) {
    $ch = substr($value, $i, 1);
    $hex = dechex(ord($ch));
    if ($hex == "b4") $ch = "'";
    $retvalue .= $ch;
  }
  return $retvalue;
}
/*
  change codes to ascii 20 to 7f
*/
function ToAscii($str) {
  $stream = '';
  for ($i = 0; $i < strlen($str); $i++) {
    $ch = substr($str, $i, 1);
    $dec = ord($ch);
    if ($dec > 127 || $dec < 32) $ch = '';
    $stream .= $ch;
  }
  return $stream;
}
/*
 * change high order characters to 0-127 range
* 145 to '
* 146 to '
* 132 to "
* 147 to "
* 148 to "
* 150 to -
* 151 to -
*/
function transform($ch,$dec) {
  if ($dec == 130) $ch =  "'";
  if ($dec == 145) $ch =  "'";
  if ($dec == 146) $ch =  "'";
  if ($dec == 132) $ch =  '"';
  if ($dec == 147) $ch =  '"';
  if ($dec == 148) $ch =  '"';
  if ($dec == 150) $ch =  '-';
  if ($dec == 151) $ch =  '-';
  if ($dec == 226) $ch =  "'";
  //if ($dec >= 128) return "";

  return $ch;
}
/*
 * cleancodes replaces certain codes as defined by transform
* this fixes the problem importing text copied from word documents that substitute high order
* ascii codes for quotes, hypen, and single quotes.
*/
function cleancodes($str) {
  $str = stripslashes($str);
  $str = tinymce_substitutions($str);
  $out = "";
  for ($i = 0; $i < strlen($str); $i++) {
    $ch = substr($str, $i, 1);
    $dec = ord($ch);
    $ch = transform($ch,$dec);
    if ($ch != "") $out .= $ch;
  }
  return $out;
}
function transformCRLF($ch,$dec) {
  if ($dec == 10) $ch =  "'";
  if ($dec == 13) $ch =  "'";

  return $ch;
}
/*
 *     $byte = substr($data,$k,1);
$hex = dechex(ord($byte));
*/
function cleanCRLF($str) {
  $str = stripslashes($str);
  $out = "";
  for ($i = 0; $i < strlen($str); $i++) {
    $ch = substr($str, $i, 1);
    $dec = ord($ch);
    $ch = transformCRLF($ch,$dec);
    $hex = dechex(ord($ch));
    if ($hex == "a") $ch = '';
    if ($ch != '') $out .= $ch;
  }
  return $out;
}

function padTwoZero($str) {
  if (strlen($str) == 1) return '00'.$str;
  if (strlen($str) == 2) return '0'.$str;
  return $str;
}
/*
  add ch to str for len
*/
function pad($str,$len,$ch) {
  $str = substr(trim($str),0,$len);
  $pad = $len - strlen($str);
  $sl = strlen($str);
  while ($pad-->0) $str .= $ch;
  return $str;
}
function padright($str,$len,$ch) {
  $padstr = '';
  $str = trim($str);
  $str = substr($str, 0, $len);
  $pad = $len - strlen($str);
  while ($pad-->0) $padstr .= $ch;
  return $padstr .$str;
}

function Dump($msg,$data) {
  $dump = print_r($data,true);
  WriteMsg($msg);
  WriteMsgRaw($dump); WriteMsg("");
}



// given date in form of mm/dd/yyyy + don't care or mm-dd-yyyy + don't care
// return month day and year as array
function DateToMDY($file) {
  $month = "";
  $day = "";
  $year = "";
  $hcount = 0;
  for ($i = 0; $i < strlen($file); $i++) {
    $ch = substr($file, $i, 1);
    if ($ch == ".") break; // stop on period, since ".pdf" may follow
    if ($ch == "-" || $ch == "/") $hcount++;
    else {
      switch ($hcount) {
        case 0:
          $month .= $ch;
          break;
        case 1:
          $day .= $ch;
          break;
        case 2:
          $year .= $ch;
          break;
      }
    }
  }
  $monthStr = MonthToString($month);
  return array($month,$day,$year);
}
function fixQuotes($str) {
  $out = "";
  for ($i = 0; $i < strlen($str); $i++) {
    $ch = substr($str, $i, 1);
    $hex = ReturnHexByte($ch);
    if ($hex == 92) $out .= "'";
    else $out .= $ch;
  }
  return $out;
}
function removeNonAscii($str) {
  $out = "";
  for ($i = 0; $i < strlen($str); $i++) {
    $ch = substr($str, $i, 1);
    $ord = ord($ch);
    if (!($ord == 13 || $ord == 10)) {
      if ($ord > 127) $ch = "";
      if ($ord < 32) $ch = "";
    }
    $out .= $ch;
  }
  return $out;
}
function MakeClean($out) {
  $out = fixQuotes($out);
  $out = removeNonAscii($out);
  return $out;
}

function Button($id, $str) {
  echo "<div id=\"$id\"  >$str</div>";
}
function DivID($id, $str) {
  echo "<div id=\"$id\">$str</div>";
}

/* count digits in str */
function CountDigits($str) {
  $count = 0;
  for ($i = 0; $i < strlen($str); $i++) {
    $ch = substr($str, $i, 1);
    if (isDigit($ch)) $count++;
  }
  return $count;
}

/*
 *                               0123456789
 * a valid zip code is xxxxx  or xxxxx-xxxx
 */
function ValidZipCode($zip) {
  if (strlen($zip) == 5) {
    if (AllDigits($zip)) return true;
  }
  if (strlen($zip) == 9) {
    $left   = substr($zip,0,5);
    $hyphen = substr($zip,5,1);
    $right  = substr($zip,6,4);
    if (!AllDigits($left)) return false;
    if (!AllDigits($right)) return false;
    if ($hyphen != '-') return false;
    return true;
  }
  return false;
}
/*
 * a valid phone number has only digits, spaces and hyphen
*/
function ValidPhoneNumber($phone) {
  if (strlen($phone) != 12) return false;
  for ($i = 0; $i < strlen($phone); $i++) {
    $ch = substr($phone, $i, 1);
    $ok = false;
    if (isDigit($ch)) $ok = true;
    if ($ch == '-') $ok = true;
    if ($ch == ' ') $ok = true;
    
    if (!$ok) return false;
  }
  return true;
}
function AllDigits($str) {
  for ($i = 0; $i < strlen($str); $i++) {
    $ch = substr($str, $i, 1);
    if (!isdigit($ch)) return false;
  }
  return true;
}

function set_bitflag() {
  $val = 0;
  foreach(func_get_args() as $flag) $val = $val | $flag;
  return $val;
}
function is_bitflag_set($val, $flag) {
  if ($val == "") return 0;
  $rv  = (($val & $flag) === $flag);
  return (int) (($val & $flag) === $flag);
}

/* send mail with attachement
 * filename - filenae
* path - path to filename
* mailto - recepient email address
* from_mail - sender's email address
* from_name - sender' name
* replyto - who to reply to (sender)
* subject - subject
* body - body
*/
function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {

  $content = ""; $name = "";
  if ($filename != "") {
    $file = $path.$filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $name = basename($file);
  }
  $uid = md5(uniqid(time()));
  $header = "From: ".$from_name." <".$from_mail.">\r\n";
  $header .= "Reply-To: ".$replyto."\r\n";
  $header .= "MIME-Version: 1.0\r\n";
  $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
  $header .= "This is a multi-part message in MIME format.\r\n";
  $header .= "--".$uid."\r\n";
  //$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
  $header .= "Content-Type:text/html; charset=us-ascii\r\n";
  $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
  $header .= $message."\r\n\r\n";
  if ($filename != "") {
    $header .= "--".$uid."\r\n";
     
    $header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
  }
  $header .= $content."\r\n\r\n";
  $header .= "--".$uid."--";
  if (mail($mailto, $subject, "", $header)) {
    return 1;
  }
  return 0;
}

// return only that which is before a dot
function stripDot($Str) {
  $Rev = strrev($Str);
  $NewStr = "";
  $save = false;
  for ($i = 0; $i < strlen($Rev); $i++) {
    $ch = substr($Rev, $i, 1);
    if ($save) $NewStr .= $ch;
    if ($ch == ".") $save = true;
  }
  $NewStr = strrev($NewStr);
  return $NewStr;
}
// return only that which is after a dot
function getDot($Str) {
  $Rev = strrev($Str);
  $NewStr = "";
  for ($i = 0; $i < strlen($Rev); $i++) {
    $ch = substr($Rev, $i, 1);
    if ($ch == ".") {
      break;
    }
    $NewStr .= $ch;
  }
  $NewStr = strrev($NewStr);
  return $NewStr;
}

//$_SERVER['REMOTE_ADDR']
function getIP() {
  $IPAddr = $_SERVER['REMOTE_ADDR'];
  $IPN = ip_address_to_number($IPAddr);
  return $IPN;
}
function number_to_ip_address($IPNumber) {
  $IP = "";
  $IP3 = ($IPNumber/(256*256*256)) & 0xff;
  $IP2 = ($IPNumber/(256*256)) & 0xff;
  $IP1 = ($IPNumber/(256)) & 0xff;
  $IP0 = $IPNumber & 0xff;
  $IP .= $IP3 . "." . $IP2 . "." . $IP1 . "." . $IP0;
  return $IP;
}
function ip_address_to_number($IPaddress) {
  //$ips = split ("\.", "$IPaddress");
  $ips = explode(".", $IPaddress);
  return ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
}
function isLeap($Year) {
  if ($Year % 4) {
    return 0;
  }
  if (!($Year % 100)) {
    if ($Year % 400) {
      return 0;
    }
  }
  return 1;
}
// give the month, day and year return Julian Date
function getJulianDate($Month,$Day,$Year) {
  return mktime(0,0,0,$Month,$Day,$Year);
}

function removePunctuation($word) {
  $punc = array('`','`','!','#','$','%','^','&','*','(',')','+','-','=','{','}','[',']',':',';','"','<','>','.','?','/',',');
  $count = sizeof($punc);
  for ($i = 0; $i < $count; $i++) {
    $word = str_replace($punc[$i],"",$word);
  }
  return $word;
}

function ValidEmailAddress($Email) {
  error_reporting(0);
  $VEA = eregi ("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $Email);
  error_reporting(E_ALL);
  return $VEA;
}

function OnlyChars($String) {
  $Outstr = "";
  for ($i = 0; $i < strlen($String); $i++) {
    $ch = substr($String, $i, 1);
    if ($ch >= "a" && $ch <= "z") $Outstr .= $ch;
    if ($ch >= "A" && $ch <= "Z") $Outstr .= $ch;
  }
  return $Outstr;
}
function OnlyDigits($String) {
  $Outstr = "";
  for ($i = 0; $i < strlen($String); $i++) {
    $ch = substr($String, $i, 1);
    if (isdigit($ch)) $Outstr .= $ch;
  }
  return $Outstr;
}
function TimeAsDate($Time) {
  $TheDate = getdate($Time);
  $Seconds = $TheDate["seconds"];
  $Minutes = $TheDate["minutes"];
  $Hours = $TheDate["hours"];
  $MDay = $TheDate["mday"];
  $Month = $TheDate["month"];
  $Year = $TheDate["year"];
  if ($Seconds < 10) $Seconds = "0$Seconds";
  if ($Minutes < 10) $Minutes = "0$Minutes";
  if ($Hours < 10) $Hours = "0$Hours";
  return array($Month,$MDay,$Year,$Hours,$Minutes,$Seconds);
}
function TimeToMDYHMS($Time) {
  list ($Month,$MDay,$Year,$Hours,$Minutes,$Seconds) = TimeAsDate($Time);
  return "$Month $MDay, $Year $Hours:$Minutes:$Seconds";
}

function TimeToMDY($Time) {
  if ($Time == 0) return "";
  $T = getdate($Time);
  $Month = $T["mon"];
  $MDay = $T["mday"];
  $Year = $T["year"];
  return "$Month/$MDay/$Year";
}

function DateFromTime($Time) {
  if ($Time == "") return "";
  $T = getdate($Time);
  $Mon = $T["mon"];
  $Year = $T["year"];
  $MDay = $T["mday"];
  return "$Mon/$MDay/$Year";
}
function PrependZeros($Number) {
  if ($Number >= 1000) return $Number;
  if ($Number >= 100 && $Number < 1000) return "0$Number";
  if ($Number >= 10 && $Number < 100) return "00$Number";
  if ($Number >= 0 && $Number < 10) return "000$Number";
}
function chopstr($Str) {
  if (strlen($Str) == 0) return "";
  $OutStr = "";
  $Len = strlen($Str) - 1;
  for ($i = 0; $i < $Len; $i++) $OutStr .= substr($Str, $i, 1);
  return $OutStr;
}



function CallPage($Page) {
  $Adr = $Page;
  echo "<meta http-equiv=\"refresh\" content=\"0;url=$Adr\">";
}
function GotoPage($Page) {
   
  $Adr = $Page;
  echo "<meta http-equiv=\"refresh\" content=\"0;url=$Adr\">";
  die;
}

$DaysInMonth = array (31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
$MonthLabels = array ("January", "Feburary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
$MonthAbbr = array ("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
$Weekdays = array ("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
$WeekdaysWed = array ("Sunday", "Monday", "Tuesday", "Wed", "Thursday", "Friday", "Saturday");

function Ahref($Class, $Off, $Page, $Desc) {
  $HTTP = $Page;
  $PageAddr = "<div class=\"$Class\"><a href = \"$HTTP\"><img src=\"images/$Off\" alt=\"\" /><span>$Desc</span></a></div>\n";
  return "$PageAddr";
}

function isdigit($ch) {
  $ordch = ord($ch);
  if ($ordch >= 48 && $ordch <= 57) {
    return 1;
  }
  return 0;
}
function DaysInMonthYear($Month, $Year) {
  global $DaysInMonth;
  $DIM = $DaysInMonth[$Month];
  if ($Month == 1 && isLeapYear($Year) == 1) $DIM += 1;
  return $DIM;
}
function browser_detection($which_test ) {
  // initialize variables
  $browser_name = '';
  $browser_number = '';
  // get userAgent string
  $browser_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
  //pack browser array
  // values [0] = user agent identifier, lowercase, [1] = dom browser, [2] = shorthand for browser,
  $a_browser_types[] = array ('opera', true, 'op' );
  $a_browser_types[] = array ('msie', true, 'ie' );
  $a_browser_types[] = array ('konqueror', true, 'konq' );
  $a_browser_types[] = array ('safari', true, 'saf' );
  $a_browser_types[] = array ('gecko', true, 'moz' );
  $a_browser_types[] = array ('mozilla/4', false, 'ns4' );
  # this will set a default 'unknown' value
  $a_browser_types[] = array ('other', false, 'other' );
  $i_count = count($a_browser_types);
  for ($i = 0; $i < $i_count; $i++)
  {
    $s_browser = $a_browser_types[$i][0];
    $b_dom = $a_browser_types[$i][1];
    $browser_name = $a_browser_types[$i][2];
    // if the string identifier is found in the string
    if (stristr($browser_user_agent, $s_browser))
    {
      // we are in this case actually searching for the 'rv' string, not the gecko string
      // this test will fail on Galeon, since it has no rv number. You can change this to
      // searching for 'gecko' if you want, that will return the release date of the browser
      if ($browser_name == 'moz' )
      {
        $s_browser = 'rv';
      }
      $browser_number = browser_version($browser_user_agent, $s_browser );
      break;
    }
  }
  // which variable to return
  if ($which_test == 'browser' )
  {
    return $browser_name;
  }
  elseif ($which_test == 'number' )
  {
    # this will be null for default other category, so make sure to test for null
    return $browser_number;
  }
  /* this returns both values, then you only have to call the function once, and get
   the information from the variable you have put it into when you called the function */
  elseif ($which_test == 'full' )
  {
    $a_browser_info = array ( $browser_name, $browser_number );
    return $a_browser_info;
  }
}
// function returns browser number or gecko rv number
// this function is called by above function, no need to mess with it unless you want to add more features
function browser_version($browser_user_agent, $search_string )
{
  $string_length = 8;// this is the maximum  length to search for a version number
  //initialize browser number, will return '' if not found
  $browser_number = '';
  // which parameter is calling it determines what is returned
  $start_pos = strpos($browser_user_agent, $search_string );
  // start the substring slice 1 space after the search string
  $start_pos += strlen($search_string ) + 1;
  // slice out the largest piece that is numeric, going down to zero, if zero, function returns ''.
  for ($i = $string_length; $i > 0 ; $i-- )
  {
    // is numeric makes sure that the whole substring is a number
    if (is_numeric(substr($browser_user_agent, $start_pos, $i ) ) )
    {
      $browser_number = substr($browser_user_agent, $start_pos, $i );
      break;
    }
  }
  return $browser_number;
}

function OpenDiv($class) {
  echo "<div class=\"$class\">";
}
function Div($class, $str) {
  echo "<div class = \"$class\">$str</div>\n";
}



function CloseDiv() {
  echo "</div>\n";
}


function myErrorHandler($errno, $errstr, $errfile, $errline) {
  switch ($errno) {
    case E_USER_ERROR:
    case E_USER_WARNING:
    case E_USER_NOTICE:
    default: {
      Message("Unable to Send Mail");
    }
  }
}
function SendMail($to, $from, $bodydata, $subject) {
  $body = <<<EOF
  $bodydata
EOF;
  $headers  = "From: $from\r\n";
  $headers .= "Content-type: text/html\r\n";
  return mail($to, $subject, $body, $headers);
}

$mingl = MIN_GENERATE_LENGTH;
$maxgl = MAX_GENERATE_LENGTH;
$minps = MIN_PASSWORD_SPECIAL;
$maxps = MAX_PASSWORD_SPECIAL;
$minpn = MIN_PASSWORD_NUMBERS;
$maxpn = MAX_PASSWORD_NUMBERS;
// Configuration definitions
$CONFIG['security']['password_generator'] = array (
    "C" => array ('characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 'minimum' => $mingl, 'maximum' => $maxgl),
    "S" => array ('characters' => "@#$%&*()+-={}?.", 'minimum' => $minps, 'maximum' => $maxps),
    "N" => array ('characters' => '1234567890', 'minimum' => $minpn, 'maximum' => $maxpn)
);
function GeneratePassword() {
  // Create the meta-password
  $sMetaPassword = "";
  global $CONFIG;
  $ahPasswordGenerator = $CONFIG['security']['password_generator'];
  foreach ($ahPasswordGenerator as $cToken => $ahPasswordSeed)
    $sMetaPassword .= str_repeat($cToken, rand($ahPasswordSeed['minimum'], $ahPasswordSeed['maximum']));
  $sMetaPassword = str_shuffle($sMetaPassword);
  // Create the real password
  $arBuffer = array();
  for ($i = 0; $i < strlen($sMetaPassword); $i ++)
    $arBuffer[] = $ahPasswordGenerator[(string)$sMetaPassword[$i]]['characters'][rand(0, strlen($ahPasswordGenerator[$sMetaPassword[$i]]['characters']) - 1)];
  return strtoupper(implode("", $arBuffer));
}

function mystriposStart($Start, $Str, $Pattern, $IgnoreCaseFlag, $WholeWordFlag) {
  $TestStr = $Str;
  $TestPattern = $Pattern;
  if ($IgnoreCaseFlag) {
    $TestPattern = strtolower($Pattern);
    $TestStr = strtolower($Str);
  }
  $PatternLength = strlen($Pattern);
  $MatchCount = 0;
  for ($i = $Start; $i < strlen($Str); $i++) {
    $ch = substr($TestStr, $i, 1);
    $pm = substr($TestPattern, $MatchCount, 1);
     
    if ($ch == $pm) {
      $MatchCount += 1;
       
      if ($MatchCount == $PatternLength) {
        if (!$WholeWordFlag) {
          return $i - strlen($Pattern) + 1;
        }
        else {
          $ch = substr($TestStr, $i+1, 1);
          if ($ch == " ") {
            return $i - strlen($Pattern) + 1;
          }
        }
      }
    }
    else { $MatchCount = 0;
    }
  }
  return -1;
}
function mystripos($Str, $Pattern) {
  return mystriposiw($Str,$Pattern,true,false);
}
function mystriposiw($Str, $Pattern, $IgnoreCaseFlag, $WholeWordFlag) {
  $TestStr = $Str;
  $TestPattern = $Pattern;
  if ($IgnoreCaseFlag) {
    $TestPattern = strtolower($Pattern);
    $TestStr = strtolower($Str);
  }
  $PatternLength = strlen($Pattern);
  $MatchCount = 0;
  for ($i = 0; $i < strlen($Str); $i++) {
    $ch = substr($TestStr, $i, 1);
    $pm = substr($TestPattern, $MatchCount, 1);
    if ($ch == $pm) {
      $MatchCount += 1;
      if ($MatchCount == $PatternLength) {
        if (!$WholeWordFlag) {
          return $i - strlen($Pattern) + 1;
        }
        else {
          $ch = substr($TestStr, $i+1, 1);
          if ($ch == " ") {
            return $i - strlen($Pattern) + 1;
          }
        }
      }
    }
    else { $MatchCount = 0;
    }
  }
  return -1;
}

function WriteHex($data) {
  $count = strlen($data);
  WriteMsg("1226 WriteHex count=$count");
  WriteMsg("1227 WriteHex data=$data");
  $bytecount = 0;
  for ($k = 0; $k < $count; $k++) {
    $byte = substr($data,$k,1);
    $hex = dechex(ord($byte));
    WriteMsg("data[$bytecount]:$hex byte:$byte");
    $bytecount++;
  }
}
function GetHex($data) {
  $count = strlen($data);
  $Bit = $count-1;
  $data = "";
  for ($k = 0; $k < $count; $k++) {
    $byte = substr($data,$k,1);
    $hex = dechex(ord($byte));
    if ($hex == 0) $hex = "00";
    $data .= $hex;
    $Bit--;
  }
  return $data;
}
function ReturnHexByte($byte) {
  $ord = ord($byte);
  error_reporting(0);
  $unpack = unpack('n',$byte);
  $u1 = $unpack[1];
  error_reporting(E_ALL);
  $hex = dechex(ord($byte));
  if ($hex == 0) $hex = "00";
  if ($hex == 1) $hex = "01";
  if ($hex == 2) $hex = "02";
  if ($hex == 3) $hex = "03";
  if ($hex == 4) $hex = "04";
  if ($hex == 5) $hex = "05";
  if ($hex == 6) $hex = "06";
  if ($hex == 7) $hex = "07";
  if ($hex == 8) $hex = "08";
  if ($hex == 9) $hex = "09";
  return $hex;
}
function PutHexByte($byte) {
  $ord = ord($byte);
  $unpack = unpack('n',$byte);
  $u1 = $unpack[1];
  echo "PutHexByte gets byte=$byte ord=$ord u1=$u1 ";
  $hex = dechex(ord($byte));
  echo "hex=$hex ";
  if ($hex == 0) $hex = "00";
  if ($hex == 1) $hex = "01";
  if ($hex == 2) $hex = "02";
  if ($hex == 3) $hex = "03";
  if ($hex == 4) $hex = "04";
  if ($hex == 5) $hex = "05";
  if ($hex == 6) $hex = "06";
  if ($hex == 7) $hex = "07";
  if ($hex == 8) $hex = "08";
  if ($hex == 9) $hex = "09";
  echo "$hex<br />";
}

function InitializeStdError() {

$STDIN = fopen('/dev/null', 'r');
$STDOUT = fopen('application.log', 'wb');
$STDERR = fopen('error.log', 'wb');
}
 
function ResetWriteMsg() {
  $fh = fopen("msg.txt", "w");
  fclose($fh);
}
function WM($msg) {
  WriteMsg($msg);
}
function WriteMsg($Msg) {
  $Msg = str_replace(chr(13), "", $Msg);
  $Msg = str_replace(chr(10), "", $Msg);
  $fh = fopen("msg.txt", "a+");
  $Msg = $Msg . "\n";
  fwrite($fh, $Msg);
  fclose($fh);
}
function WriteMsgRaw($Msg) {
  $fh = fopen("msg.txt", "a+");
  fwrite($fh, $Msg);
  fclose($fh);
}
date_default_timezone_set('America/Los_Angeles');



?>
