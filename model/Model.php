<?php
class Model {

public function DeleteItem($iid) {
  $db = new Database();
  $db->query("DELETE FROM inventory WHERE iid='$iid'");

}
/*
  return first and last mdy from payments_list
   //SELECT fields FROM payemnt_lists  ORDER BY paymentListID DESC LIMIT 1;
*/
  public function PaymentsListFirstLast() {
   $db = new Database();
   $db->query("SELECT * FROM payments_list ORDER By paymentListID");
   $db->singleRecord();
   $first_mdy = $db->Record['mdy'];
   $db->query("SELECT * FROM payments_list ORDER By paymentListID DESC LIMIT 1");
   $db->singleRecord();
   $last_mdy = $db->Record['mdy'];

   return array($first_mdy,$last_mdy);
 }
 
 /*
   export vendors as pdf file
     $status = mysql_query("CREATE TABLE vendors (vid INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(vid),
    name     text,
    address  text,
    city     text,
    state    varchar(2),
    zip      varchar(9),
    phone    text,
    email    text,
    notes    text,
    next_number integer,
    vendor   text,
    company  text,
    commission integer

  )");
  check($status,'vendors');
*/

  private function getVendorList() {
    $list = array(); $count = 0;

    $db = new Database();
    $db->query("SELECT * FROM vendors ORDER by company");


    while ($db->nextRecord()) {
      $vid = $db->Record['vid'];
      list ($vendor_name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->getVendorInfo($vid);
      $list[$count++] = array('VendorName' => $vendor_name,'Address' => $address, 'City' => $city, 'State' => $state, 'Zip' => $zip, 'Phone' => $phone, 'Company' => $company);
    }
    
    return $list;
  }

  public function ExportVendors() {
   require('fpdf/fpdf.php');

   $list = $this->getVendorList();

   $filename = VENDORS_FILE_NAME;
   $pdf = new FPDF('P','pt');
   $pdf->AddPage();
   $x = 10;
   $y = 20;
   $pdf->Image('images/logo-100.jpg',10,10);
   $pdf->SetFont('Arial','B',12);
   $date = date('m/d/Y',time());
   $pdf->Text($x+200,$y,"Yuba Harvest Vendor List $date");


   $y += LINE_HEIGHT*4;
   $pdf->Text($x,$y,"Company");
   $pdf->Text($x+300,$y,"Contact");
   $pdf->Text($x+500,$y,"Phone");
   $pdf->SetFont('Arial','',12);
   $y += LINE_HEIGHT*2;
   for ($i = 0; $i < count($list); $i++) {
     $vendor_name  = $list[$i]['VendorName'];
     $city         = $list[$i]['City'];
     $state        = $list[$i]['State'];
     $zip          = $list[$i]['Zip'];
     $phone        = $list[$i]['Phone'];
     $company      = $list[$i]['Company'];
     if ($phone != '') {
       $pdf->Text($x,$y,$company);
       $pdf->Text($x+300,$y,substr($vendor_name,0,30));
       $pdf->Text($x+500,$y,$phone);

       $y += LINE_HEIGHT;
     }
   }
   
   $pdf->Output($filename,'D');

 }
 /*
   get list of unique categories used in payments

 */
   private function getAllCategories() {
    $list = array(); $count = 0;
    $db = new Database();
    $db->query("SELECT * FROM payments");
    while ($db->nextRecord()) {
      $category = stripslashes($db->Record['category_key']);
      if (!ValueInList($category,$list,'Category')  && $category != '') {
        $list[$count++] = array('Category' => $category, 'PaymentID' => $db->Record['paymentID']);
      }
    }
    sort($list);
    return $list;  
  }
  /*
    return a list of selected categories (if any)
  */
    public function RetrieveCategories() {
      $list = array(); $count = 0;
      $db = new Database();
      $db->query("SELECT * FROM payments");
      while ($db->nextRecord()) {
        $pid = $db->Record['paymentID'];
        $category = stripslashes($db->Record['category_key']);

        $sel = "c$pid";
      if (isset($_POST["$sel"])) $list[$count++] = $pid;// = array('PaymentListID' => $pid, 'Category' => $category);
      
    }
    


    return $list;
  }
 /*
   display selection for which categories to choice for the reports page
    [c75681] => on
    [c75683] => on
    [c75685] => on

    selected_list array of paymentListID's from the payment_list table that were selected  by the user (may be naught)
    Checkbox checked="yes"
 */
    public function CategorySelections($selected_list) {

      $stream = '';
      $list = $this->getAllCategories();

      for ($i = 0; $i < count($list); $i++) {
        $pid = $list[$i]['PaymentID'];
        $category   = $list[$i]['Category'];
        $name_value = "Choose$pid";
        $checked = '';

        if (FindInList($selected_list,$pid)) $checked = 'checked';
        
        $stream .= '<p style="line-height:7pt; font-size:8pt;font-family:courier">' . 


        "<input type=\"checkbox\" name=\"c$pid\" $checked />&nbsp;&nbsp;" . $category . '</p>' . "\n";
      }
      return $stream;
 }/* 
    called by Reports to return the stream to display in the listing area
    prior to this call, the selected records have been built in the table payments_list

  */
    private function ListStream() {

      $trace = false;
      $write_report = false;

      $tcount = 0;
      $filename = 'report_oct.1-15.csv';
      if ($write_report) {
        $fh = fopen($filename,'w');
        fclose($fh);
      }



      $stream = '';

      $amt_total = 0;
      $min_mdy = 0;  $max_mdy = 0;

      $db = new Database();
      $db->query("SELECT * FROM payments_list ORDER by paymentListID");
      while ($db->nextRecord()) {
        $mdy        = $db->Record['mdy'];


        $product_name  = trim(stripslashes($db->Record['product_name']));
        $category      = substr(stripslashes($db->Record['category_key']),0,20);
        $sku           = $db->Record['sku'];
        $item_cost     = $db->Record['item_cost'];
        $gross         = $db->Record['gross'];

        $qty           = $db->Record['quantity'];
        $hour          = $db->Record['hour'];
        $min           = $db->Record['min'];
        $sec           = $db->Record['sec'];

        $amt = $qty * $item_cost;



        $amt_formatted  = padright(FormatAmount($amt,8),8,'&nbsp;');

        $sku_padded = $sku;
        if ($sku_padded == '') $sku_padded = pad('',16,'&nbsp;');

        $product_name_padded = pad($product_name, 60, '.');
         

        $qty_padded  = pad($qty,3,'&nbsp;');

        if ($qty >= 10 && $qty < 100) $qty_padded = '&nbsp;' . $qty_padded;
        if ($qty >= 100 && $qty < 1000) $qty_padded = '&nbsp;&nbsp;' . $qty_padded;

        $date   = date('D m/d/Y',$mdy);

        $item_cost_formatted = padright(FormatAmount($item_cost,9),9,'&nbsp;');



        if ($min_mdy == 0) $min_mdy = $mdy;
        else if ($mdy < $min_mdy) $min_mdy = $mdy;

        if ($max_mdy == 0) $max_mdy = $mdy;
        else if ($mdy > $max_mdy) $max_mdy = $mdy;

        $hour_ampm = '';
        if ($min == 0) $min = $min . '0';
        if ($min == 1) $min = '0' . $min;
        if ($min == 2) $min = '0' . $min;
        if ($min == 3) $min = '0' . $min;
        if ($min == 4) $min = '0' . $min;
        if ($min == 5) $min = '0' . $min;
        if ($min == 6) $min = '0' . $min;
        if ($min == 7) $min = '0' . $min;
        if ($min == 8) $min = '0' . $min;
        if ($min == 9) $min = '0' . $min;


        if ($sec == 0) $sec = $sec . '0';
        if ($sec == 1) $sec = '0' . $sec;
        if ($sec == 2) $sec = '0' . $sec;
        if ($sec == 3) $sec = '0' . $sec;
        if ($sec == 4) $sec = '0' . $sec;
        if ($sec == 5) $sec = '0' . $sec;
        if ($sec == 6) $sec = '0' . $sec;
        if ($sec == 7) $sec = '0' . $sec;
        if ($sec == 8) $sec = '0' . $sec;
        if ($sec == 9) $sec = '0' . $sec;

        if ($trace) WriteMsgRaw("hour:$hour:$min:$sec  ");
        if ($hour < 10) {
          $hour_ampm = "&nbsp;&nbsp;$hour:$min:$sec am";
        }
        if ($hour >= 10 && $hour < 12) {
          $hour_ampm = "&nbsp;$hour:$min:$sec am";
        }
        if ($hour == 12) {
          $hour_ampm = "&nbsp;$hour:$min:$sec pm";
        }
        if ($hour > 12) {
          $hour -= 12;
          if ($hour < 10) {
            $hour_ampm = "&nbsp;&nbsp;$hour:$min:$sec pm";
          }
          if ($hour >= 10) {
            $hour_ampm = "&nbsp;$hour:$min:$sec pm";
          }


        }
        

  

        $stream .= '<p style="line-height:7pt; font-size:8pt;font-family:courier">' . $date . ' ' . $hour_ampm .
        
        '&nbsp;&nbsp;' . $product_name_padded . '&nbsp;&nbsp;' . 
        $qty_padded . 'x&nbsp;' .
        $item_cost_formatted . '&nbsp;' .
        '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$' . $amt_formatted . '&nbsp;&nbsp;&nbsp;' . $category . '</p>';
        $amt_total += $amt;
        $tcount++;

        if ($write_report) {
          $hour_ampm              = trim(str_replace('&nbsp;',' ',$hour_ampm));
          $product_name           = trim($product_name);
          $tw_amt_formatted       = $amt/100; // trim(FormatAmount($amt,9));
          $tw_amt_total           = $amt_total/100; //trim(FormatAmount($amt_total,9));
          
          $dateday   = date('D',$mdy);
          $date1   = date('m/d/Y',$mdy);
          

          $the24_hour = $hour;
          if ($hour >= 1 && $hour < 9) $the24_hour += 12;

          $str = "$date1,$the24_hour:$min,$dateday,$product_name,$tw_amt_formatted,$tw_amt_total,$category\n";

          $fh = fopen($filename,'a+');
          fwrite($fh,$str);
          fclose($fh);
        }


      }
      return array($stream,$amt_total, $min_mdy, $max_mdy, $tcount);
    }
    private function getCategoryFromID($paymentID) {
     $db = new Database();
     $db->query("SELECT * FROM payments WHERE paymentID='$paymentID'");
     if ($db->numRows() == 0) return 0;
     $db->singleRecord();
     return stripslashes($db->Record['category_key']);
   }
  /*
    pids is a list of payments record id's (paymentID),  return list of the associated category for each recoard
  */
    private function CategoryList($pids) {
      $list = array(); $count = 0;
      if (count($pids) == 0) return $list;

      for ($i = 0; $i < count($pids); $i++) {
        $list[$count++] = $this->getCategoryFromID($pids[$i]);
      }
      return $list;


    }


/*
    called to create payment_list table.  This was done, because internal memory could not handle so many records.
    the payment_list table is used to output the selected rows from the payments table
    category_select is the actual category name, Wine, Home & Bath, etc
    case                  - order by case
                            1 - ORDER BY mdy,sku
                            2 - ORDER BY sku
                            3 - class
                            4 - ORDER BY name,mdy
    search_begin_date - m/d/y begin search date
    search_end_date   - m/d/y end search date
    search_product    - product name to search for (maybe multiple words
    vendor_select     - vendor, eight letters from sku
    from_hour         - hour to begin at 0 - 24
    to_hour           - hour to end at 0 - 24
    from_ampm         - am 0, pm 1
    to_ampm           - am 0, pm 1
    $selected_list    - list of paymentID record numbers from the payments table for which the first entry matching any selected
                        category from the Choose Categories list was selected.
                        if this list has any entries, only those selected categories will be included.
 
    group list will be
    [0] => Wine Sales,Wine
    [1] => Kitchen,Food,Lunch and Dinner,Pastries/Breads,Kitchen
    [2] => Retail Sales,Home & Bath,Olive Oil,Art,Jams/Spreads/Sauses,Cheese,Beverages,Meats/Seitan,Beer,Olives/Oils/Vinegards,Produce,Crafts
    [3] => Other,Yuba Harvest
    
    specific - will be 0 to 3, depending on which entry line in the group list has been selected by the user 
    category - the category under consideration


  */
    private function PaymentsWOB($search_begin_date,$search_end_date) {
      $trace = false;
      $order_by = '';
      $where = '';
      $scase = 0;
      if ($search_begin_date == '' && $search_end_date == '') $scase = 1;
      if ($search_begin_date == '' && $search_end_date != '') $scase = 2;
      if ($search_begin_date != '' && $search_end_date == '') $scase = 3;
      if ($search_begin_date != '' && $search_end_date != '') $scase = 4;

      $bd = strtotime($search_begin_date);
      $ed = strtotime($search_end_date);
    //$ed += 86400 - 1;
      switch ($scase) {
        case 1:
        break;
        case 2:
        $where = "WHERE `mdy` <= $ed";
        break;
        case 3:
        $where = "WHERE $bd >= `mdy`";
        break;
        case 4:
        $where = "WHERE `mdy` BETWEEN $bd AND $ed";
        break;
      }
      $order_by = ' ORDER BY mdy,hour,min';

      if ($trace) WM("PaymentsWOB search_begin_date:$search_begin_date search_end_date:$search_end_date bd:$bd ed:$ed where:$where");

      return array($where,$order_by);
    }

  /*
    product vendor filter
    if a product name is entered and/or a vendor is selected, determine if there is a match

    sku          - stock keeping unit
                 - ex: RET_APOLLOOL_101

    product_name - product name
    the_vendor   - the vendor name, ex: APOLLOOL
                 - if blank, no search is done

                  
    speach_product - one or more words to search for the product description
                   - if blank, no search is done

    true  - returned if any words in product_name match any product description
          - returned if the vendor is not none and it matches the selected vendor
    false - returned otherwise
  */
    private function ProductVendorFilter($sku,$product_name,$the_vendor,$search_product) {
      $trace = false;

      $display = false;
      $vendor = ExtractVendor($sku);
      $product_names_array   = MakeArray($product_name);
      $search_products_array = MakeArray($search_product);
      $case = 0;

      if ($search_product == '' && $the_vendor == '') $case = 1;
      if ($search_product == '' && $the_vendor != '') $case = 2;
      if ($search_product != '' && $the_vendor == '') $case = 3;
      if ($search_product != '' && $the_vendor != '') $case = 4;
      
      $vendor_keep = $search_keep = false;
      if ($the_vendor != '') if ($the_vendor     == $vendor)            $vendor_keep   = true;
      if (wordSearchMatch($search_products_array,$product_names_array)) $search_keep   = true;

      if ($trace) WM("ProductVendorFilter case:$case search_product:$search_product the_vendor:$the_vendor vendor_keep:$vendor_keep search_keep:$search_keep $product_name");

      $display = false;
      switch ($case) {
        case 1:
        $display = true;
        break;
        case 2:
        $display = $vendor_keep;
        break;
        case 3:
        $display = $search_keep;
        break;
        case 4:
        if ($vendor_keep || $search_keep) $display = true;
        break;
      }
      return $display;
    }
  /*
    hour time filter
    determine if the selected from and/or to hour is within the hour range
    
    display   - true or false, depending on the previous filter
    hour      - hour in 24 hour format, 0 - 23
    from_hour - from hour as selected by user, may be blank, 0 - 12
    to_hour   - to_hour as selected by user, may be blank, 0 - 12
    from_ampm - am/pm radio button selection 0 - am  1 - pm    
    to_ampm - am/pm radio button selection 0 - am  1 - pm    
  */
    private function HourTimeFilter($display,$hour,$from_hour,$to_hour,$from_ampm,$to_ampm,$product_name) {
      $trace = false;
      $msg = '';
      $from_hour24 = $from_hour;
      $to_hour24   = $to_hour;
      if ($from_ampm == 1) $from_hour24 = $from_hour + 12;
      if ($to_ampm == 1)   $to_hour24   = $to_hour + 12;

      $show = false;
      $case = 0;
      if ($from_hour == '' && $to_hour == '') $case = 1;
      if ($from_hour == '' && $to_hour != '') $case = 2;
      if ($from_hour != '' && $to_hour == '') $case = 3;
      if ($from_hour != '' && $to_hour != '') $case = 4;

      switch ($case) {
        case 1:
        if ($trace) $msg = "HourTimeFilter case 1 hour:$hour from_hour24:$from_hour24 to_hour24:$to_hour24 ";
        $show = true;
        break;
        case 2:
        if ($trace) $msg = "HourTimeFilter case 2 $hour >= $to_hour24 hour:$hour from_hour24:$from_hour24";
        if ($hour >= $to_hour24) $show = true;
        break;
        case 3:
        if ($trace) $msg = "HourTimeFilter case 3 $hour >= $from_hour24 hour:$hour from_hour24:$from_hour24";
        if ($hour >= $from_hour24) $show = true;
        break;
        case 4:
        if ($trace) $msg = "HourTimeFilter case 4 $hour >= $from_hour24 && $hour <= $to_hour24 hour:$hour from_hour24:$from_hour24 to_hour24:$to_hour24";
        if ($hour >= $from_hour24  && $hour <= $to_hour24) $show= true;
        break;
      }
      if ($trace) $msg = "display:$display show:$show ". $msg;
      if ($display) $display = $show;
      if ($trace)    {
        if ($display)  $msg = "HourTimeFilter RETURNS $display ".$msg . ' ' .$product_name;
        if (!$display) $msg = "HourTimeFilter RETURNS  $display ".$msg . ' ' .$product_name;
      }

      if ($trace) WM($msg);
      return $display;
    }

  /*
    day filter
    display   - true or false, depending on the previous filter
    myd       - unix time stamp representing the month/day/year
    mon       - set to 1 if Mon checkbox selected
    tue       - set to 1 if Tue checkbox selected
    wed       - set to 1 if Wed checkbox selected
    thu       - set to 1 if Thu checkbox selected
    fri       - set to 1 if Fri checkbox selected
    sat       - set to 1 if Sat checkbox selected
    sun       - set to 1 if Sun checkbox selected
  */
    private function DayFilter($display,$mdy,$mon,$tue,$wed,$thu,$fri,$sat,$sun) {
      $trace  = false;

      $day = strtolower(date('D',$mdy));



      $test_day = false;

      $let_mon = $let_tue = $let_wed = $let_thu = $let_fri = $let_sat = $let_sun = 0;

      $show = false;


      if ($mon == 1 || $tue == 1 || $wed == 1 || $thu == 1 || $fri == 1 || $sat == 1 || $sun == 1) $test_day = true;    
      if ($mon == 1) if ($day == 'mon') $let_mon = 1;
      if ($tue == 1) if ($day == 'tue') $let_tue = 1;
      if ($wed == 1) if ($day == 'wed') $let_wed = 1;
      if ($thu == 1) if ($day == 'thu') $let_thu = 1;
      if ($fri == 1) if ($day == 'fri') $let_fri = 1;
      if ($sat == 1) if ($day == 'sat') $let_sat = 1;
      if ($sun == 1) if ($day == 'sun') $let_sun = 1;

      if ($trace) WM("day:$day test_day:$test_day mon:$mon tue:$tue wed:$wed thu:$thu fri:$fri sat:$sat sun:$sun");
      if ($test_day) {
        $display = $let_mon || $let_tue || $let_wed || $let_thu || $let_fri || $let_sat || $let_sun; ; 
      }

      if ($trace) WM("DayFilter      RETURNS $display day:$day mon:$mon wed:$wed thu:$thu fri:$fri sat:$sat sun:$sun");


      return $display;
    }

  /*
    specific selection filter
    display   - true or false, depending on the previous filter
    specific  - selection number of the specific filter radio buttons
                specific must be zero because if there is a specific group selection (specific is not zero)
                it will override this filter
              0 - none
              1 - Wine Sales
              2 - Kitchen
              3 - Retail Sales
              4 - Other Sales
    cateory_list - list of categories that might have been selected
                   if the count is not zero, then one or more categories
                   have been selected.

  
  */
                   private function SpecificFilter($display,$specific,$category_list,$category) {
                     $trace = false;
                     $orig_display = $display;
                     if ($specific == 0 && count($category_list) != 0) $display = FindInList($category_list,$category);

                     if ($trace) WM("SpecificFilter RETURNS $display orig_display:$orig_display specific:$specific count(category_list):".count($category_list));

                     return $display;
                   }
    /*
     group list will be
    [0] => Wine Sales,Wine
    [1] => Kitchen,Food,Lunch and Dinner,Pastries/Breads,Kitchen
    [2] => Retail Sales,Home & Bath,Olive Oil,Art,Jams/Spreads/Sauses,Cheese,Beverages,Meats/Seitan,Beer,Olives/Oils/Vinegards,Produce,Crafts
    [3] => Other,Yuba Harvest
    
    entry    will be 0 to 3, depending on which entry line in the group list has been selected by the user 
    category - the category under consideration
  */
    private function GroupInList($group_list,$entry,$category) {

      $trace = false;
      if ($entry == 0) return true;
      $gl = $group_list[$entry-1];
      $glx = explode(',',$gl);
      $cglx = count($glx);
      for ($i = 1; $i < $cglx; $i++) {
        if ($glx[$i] == $category) return true;
      }
      return false;

    }
  /*
    determine if a group radio button has been selected (upper rigth corner)
    display   - true or false, depending on the previous filter
    specific  - selection number of the specific filter radio buttons
              0 - none
              1 - Wine Sales
              2 - Kitchen
              3 - Retail Sales
              4 - Other Sales
    category - category of the record under consderation
  */
    private function GroupListFilter($display,$specific,$category) {
      $group_list = LoadGroupList();
      if ($display) $display = $this->GroupInList($group_list,$specific,$category);
      return $display;
    }
  /*
    build entries in the payment_list table according to the selected criteria
    this table is then read to product the output display
  */
    private function BuildPaymentsList($case,$search_begin_date,$search_end_date,$search_product,$the_vendor,
      $from_hour,$to_hour,$from_ampm,$to_ampm,$selected_categories,$specific,$mon,$tue,$wed,$thu,$fri,$sat,$sun) {


      $db = new Database();
      $db->query("DELETE FROM payments_list");

    /*

    */
    list ($where,$order_by) = $this->PaymentsWOB($search_begin_date,$search_end_date);
    
    $db->query("SELECT * FROM payments $where $order_by ");
    while ($db->nextRecord()) {
      $class     = substr($db->Record['sku'],0,3);
      $mdy       = $db->Record['mdy'];
      $hour      = $db->Record['hour'];
      $min       = $db->Record['min'];
      $sec       = $db->Record['sec'];
      $product_name  = stripslashes($db->Record['product_name']);

      $quantity  = $db->Record['quantity'];
      $item_cost = $db->Record['item_cost'];
      $category  = stripslashes($db->Record['category_key']);
      $sku       = $db->Record['sku'];
      $gross     = $db->Record['gross'];
      $discount  = $db->Record['discount'];
      $sales_tax = $db->Record['sales_tax'];
      $net       = $db->Record['net'];
      
      $display   = $this->ProductVendorFilter($sku,$product_name,$the_vendor,$search_product);
      if ($display) $display   = $this->HourTimeFilter($display,$hour,$from_hour,$to_hour,$from_ampm,$to_ampm,$product_name);
      if ($display) $display   = $this->DayFilter($display,$mdy,$mon,$tue,$wed,$thu,$fri,$sat,$sun);
      

      if ($display) $display   = $this->SpecificFilter($display,$specific,$this->CategoryList($selected_categories),$category);
      if ($display) $display   = $this->GroupListFilter($display,$specific,$category);

      $trace = false;
      if ($trace) WM("BuildPaymentsList display:$display");
      if ($trace) WM("");
      if ($display) $this->InsertPaymentList($mdy,$hour,$min,$sec,$product_name,$quantity,$item_cost,$category,$sku,$class,$gross,$discount,$net,$sales_tax);

    }
  }
  public function Reports($case,$search_begin_date,$search_end_date,$search_product,$the_vendor,
   
    $from_hour,$to_hour,$from_ampm,$to_ampm,$selected_categories,$specific,$mon,$tue,$wed,$thu,$fri,$sat,$sun) {
    $stream = ''; $net_total = 0;

    $this->BuildPaymentsList($case,$search_begin_date,$search_end_date,$search_product,$the_vendor,
      $from_hour,$to_hour,$from_ampm,$to_ampm,$selected_categories,$specific,$mon,$tue,$wed,$thu,$fri,$sat,$sun);
    $tcount = 0;
    switch ($case) {
      case 1:
      case 2:
      case 4:
      list ($stream, $net_total,$min_mdy, $max_mdy, $tcount) = $this->ListStream();
      break;
      case 3:

         //list ($stream, $net_total) =  $this->ClassStream();
      break;

    }

    return array($stream,$net_total,$tcount);
  }

  /*
    Delete Vendor 
  */
    public function DeleteVendor($vid) {
      $db = new Database();
      $db->query("DELETE FROM vendors WHERE vid='$vid'");
    }
  /*
    called by reports.php to return the vendor given the vendor record id vid
      
  */

    public function TheVendorSelection($vid) {
      $trace = false;
      if ($vid == 0) return '';
      list ($vendor_name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->getVendorInfo($vid);
      return $vendor;
      

    }


    /*
      return list with array of vendor information
      VID - vendor record id (vid)
      Company - company
      Vendor  - vendor

    */
    private function getListOfVendors() {
      $list = array(); $count = 0;
      $db = new Database();
      $db->query("SELECT * FROM vendors ORDER BY company");
      while ($db->nextRecord()) {
        $vid     = $db->Record['vid'];
        $company   = stripslashes($db->Record['company']);
        $vendor    = $db->Record['vendor'];
        $list[$count++] = array('VID' => $vid, 'Company' => $company, 'Vendor' => $vendor);
      }
      return $list;
    }

    /*
      vendor select in segments, since the list is getting really long
      I hope this works.
    */
    public function getVendorSegment($segment) {
    $trace = false;
    
    $list = array(); $count = 0;
    $vendor_list = $this->getListOfVendors();

    $count = count($vendor_list);
    $segment_count = $count/2;
    $s_count = $segment_count;

    $l_count = $v_count = 0;

    if ($trace) WM("getVendorSegment segment:$segment count:$count segment_count:$segment_count s_count:$s_count");
    switch ($segment) {
      case 1:
     
        $count_index = 0;
        $list_index = 0;
        while ($s_count-- > 0) $list[$l_count++] = $vendor_list[$list_index++];
        break;
      case 2:
        $count_index = $segment_count;
        $list_index  = $segment_count;
        while ($s_count-- > 0) $list[$l_count++] = $vendor_list[$list_index++];
        break;
  
    }

    return $list;
  }
public function VendorSelectSegment($segment,$sel) {
   
    $trace = false;
     
    $name = "VendorSelect$segment";
 
    $list = $this->getVendorSegment($segment);
    $stream = '';
    $stream .= "<select name=\"$name\" style=\"width:150px;\">";
    //$stream .= "<select name="\$name\"  style=\"width:150px;\">";//OnChange="document.form1.submit();">';
    $stream .= '<option value="0">none</option>';
    for ($i = 0; $i < count($list); $i++) {
      $vid      = $list[$i]['VID'];
      $company  = $list[$i]['Company'];
      $vendor   = $list[$i]['Vendor'];
      
      $selected = '';
      if ($sel == $vid) $selected = 'selected="selected"';

      $s1 = "<option value=\"$vid\" $selected >$company</option>\n";
      if ($trace) WM("VendorSelect Segment segment:$segment $s1");
      $stream .= $s1;
    }
    
    $stream .= '</select>';
    return $stream;

  }

    /*
    return select list of vendors 
    called by reports.pdf, edit_item.php
  */
    public function VendorSelect($sel) {
    $trace = false;
    if ($trace) WM("VendorSelect sel:$sel");

      $stream = '';
    $stream .= '<select name="VendorSelect"  style="width:150px;">';//OnChange="document.form1.submit();">';
    $stream .= '<option value="0">none</option>';
    $db = new Database();
    $db->query("SELECT * FROM vendors ORDER BY company");
    while ($db->nextRecord()) {

      $vid     = $db->Record['vid'];
      $company   = stripslashes($db->Record['company']);
      $vendor    = $db->Record['vendor'];
      $selected = '';
      if ($sel == $vid) $selected = 'selected="selected"';
 
      $stream .= "<option value=\"$vid\" $selected >$company</option>\n";
    }
    $stream .= '</select>';
    return $stream;

  }
  //OnChange="document.form1.submit();" 
 public function ClassSelect($sel) {

      $trace = false;
     

      $stream = '';
      $stream .= '<select name="ClassSelect" style="width:150px;">';
      $stream .= '<option value="0">none</option>';
      $db = new Database();
      $db->query("SELECT * FROM classes ORDER BY class");
      while ($db->nextRecord()) {

        $cid     = $db->Record['cid'];
        $class   = stripslashes($db->Record['class']);

        $selected = '';
        if ($sel == $cid) $selected = 'selected="selected"';

        $stream .= "<option value=\"$cid\" $selected>$class</option>";
      }
      $stream .= '</select>';
      return $stream;

    }

  /*
    called by ExportWinePDF and ExportWineList to get the wine list
    returns list
    ProductName
    Price
  */
  private function getWineList() {
    $list = array(); $count = 0;
    $db = new Database();
    $db->query("SELECT * FROM inventory WHERE cat='Wine Bottles'");
    while ($db->nextRecord()) {
      $product_name  = stripslashes($db->Record['description']);
      $price   = $db->Record['price'];

      if ($price != 0 && $product_name != 'Tasting') $list[$count++] = array('ProductName' => $product_name, 'Price' => $price);

    }
    return $list;
  }
   /*
   called by Controller.php EXPORT_WINE_LIST case (the second button on the inventory page)
 */
   public function ExportWineList() {
    $stream = '';
    $list = $this->getWineList();
    sort($list);
    for ($i = 0; $i < count($list); $i++) {
      $product_name = $list[$i]['ProductName'];
      $price        = $list[$i]['Price'];

      $stream      .= '"'.$product_name.'",';
      $stream      .= '"'.$price.'"';
      $stream .= "\n";
    }
   return $stream;
  }
 /*
   called by Controller.php EXPORT_WINE case (the third button on the inventory page)
 */
   public function ExportWinePDF() {
    $filename = "Wine List.pdf";
    require('fpdf/fpdf.php');

    $pdf = new FPDF('P','pt');
    
    $pdf->AddPage();
    $pdf->Image('images/logo-100.jpg',10,10);
    $pdf->SetFont('Arial','B',24);
    $pdf->Text(140,35,'Yuba Harvest Wine List');


    $x = 240;
    $y = 50;
    $pdf->SetFont('Arial','',9);
    $pdf->Text($x,$y,date('F j, Y',time()));


    $list = $this->getWineList();
    sort($list);

    $x = 100;
    $y = 100;

    for ($i = 0; $i < count($list); $i++) {
      $product_name = $list[$i]['ProductName'];
      $formatted_price        = FormatAmount($list[$i]['Price'],6);

      $pdf->SetFont('Arial','',12);
      $pdf->Text($x,$y,$product_name);
      $pdf->SetFont('Arial','B',12);
      $pdf->Text($x+295,$y+3,'$');
      $pdf->SetXY($x+320,$y);

      $pdf->Cell(20,0,$formatted_price,0,0,'R');
      $y += 13;
    }
    $x = 250;
    $y = 750;
    $pdf->SetFont('Arial','',10);

    //$pdf->SetXY($x,$y);
    //$pdf->Cell(40,0,'yubaharvest.com',0,0,'C');
   // $y += 12;
    $pdf->SetXY($x,$y);
    $pdf->Cell(40,0,'9222 Marysville Rd, Oregon House',0,0,'C');
    $y += 12;
    $pdf->SetXY($x,$y);
    $pdf->Cell(40,0,'(530) 418-8240',0,0,'C');
    $y += 12;

    $pdf->Output($filename,'D');
  }


  private function PaymentPDF($filename,$vendor_name,$address,$city,$state,$zip,$phone,$email,$company,$min_mdy,$max_mdy,$commission,$list) {
    $trace = false;
  
    if ($trace) WM("PaymentPDF min_mdy:$min_mdy:".date('m/d/Y',$min_mdy). " max_mdy:$max_mdy:".date('m/d/Y',$max_mdy));

    $TOP = 20;
    $PAGE_HEIGHT = 792;
    require('fpdf/fpdf.php');

    $pdf = new FPDF('P','pt');
    $x = 110;
    $y = $TOP;
    $pdf->AddPage();
    $pdf->Image('images/logo-100.jpg',10,10);
    $pdf->SetFont('Arial','B',TEXT_PTS);
    $pdf->Text($x,$y,'Yuba Harvest');
    $y += LINE_HEIGHT;
    $pdf->Text($x,$y,'9222 Marysville Rd #7');
    $y += LINE_HEIGHT;
    $pdf->Text($x,$y,'Oregon House, CA 95962');
    $x = LEFT_MARGIN+5;
    $y += LINE_HEIGHT*2;
    $pdf->Text($x,$y,'CONSIGNMENT PAYMENT STATEMENT');
    $y += LINE_HEIGHT*1.5;




    $pdf->SetFont('Arial','',TEXT_PTS);
    $pdf->Text($x,$y,'Date:');
    $pdf->SetFont('Arial','B',TEXT_PTS);
    $date = date('m/d/Y',time());

    $pdf->Text($x+60,$y,$date);


    $y += LINE_HEIGHT;

    $pdf->SetFont('Arial','',TEXT_PTS);
    $pdf->Text($x,$y,'Vendor:');
    $pdf->SetFont('Arial','B',TEXT_PTS);
    $pdf->Text($x+60,$y,$vendor_name);


    $y += LINE_HEIGHT;
    $pdf->SetFont('Arial','',TEXT_PTS);
    $pdf->Text($x,$y,'Address:');
    $pdf->SetFont('Arial','B',TEXT_PTS);
    $pdf->Text($x+60,$y,$address);


    $y += LINE_HEIGHT;
    $pdf->SetFont('Arial','',TEXT_PTS);
    $csz = "$city, $state $zip";
    if ($city == '') $csz = '';
    $pdf->Text($x,$y,'City:');
    $pdf->SetFont('Arial','B',TEXT_PTS);
    $pdf->Text($x+60,$y,$csz);

    $y += LINE_HEIGHT;
    $pdf->SetFont('Arial','',TEXT_PTS);
    $pdf->Text($x,$y,'Phone:');
    $pdf->SetFont('Arial','B',TEXT_PTS);
    $pdf->Text($x+60,$y,$phone);

    $y += LINE_HEIGHT;
    $pdf->SetFont('Arial','',TEXT_PTS);
    $pdf->Text($x,$y,'Email:');
    $pdf->SetFont('Arial','B',TEXT_PTS);
    $pdf->Text($x+60,$y,$email);

    $y += LINE_HEIGHT;
    $pdf->SetFont('Arial','',TEXT_PTS);
    $pdf->Text($x,$y,'Dates Sold:');
    $d1 = date('m/d/y',$min_mdy);
    $d2 = date('m/d/Y',$max_mdy);
    $pdf->SetFont('Arial','B',TEXT_PTS);
    $pdf->Text($x+60,$y, "$d1 to $d2");
    $y += LINE_HEIGHT;

    $pdf->SetFont('Arial','',TEXT_PTS);
    $pdf->Text($x,$y,'Commission:');
    $com = $commission . '%';
    $pdf->SetFont('Arial','B',TEXT_PTS);
    if ($commission == 0) $com = 'none';
    $pdf->Text($x+60,$y,$com);



    $y += LINE_HEIGHT*2;



    $pdf->Text($x,$y,'DATE          PRODUCT');
    $pdf->Text($x+QTY_XCOOR+5,$y,'QTY');
    $pdf->Text($x+QTY_XCOOR+PRICE_XCOOR-3,$y,'EACH');
    $pdf->Text($x+QTY_XCOOR+PRICE_XCOOR+TOTAL_XCOOR-7,$y,'TOTAL');
    $pdf->SetFont('Arial','',TEXT_PTS);

    $pdf->Line(LEFT_MARGIN,$y+7,350,$y+7);

    $y += ITEM_HEIGHT*1.5;

    $product_y = $y;
    $sub_total = 0;
    for ($i = 0; $i < count($list); $i++) {
      $date            = $list[$i]['Date'];
      $product_name    = $list[$i]['ProductName'];
      $qty   = $list[$i]['Qty'];
      $item_cost = $list[$i]['ItemCost'];

      $sub_total += $qty * $item_cost;
      $price = FormatAmount($item_cost,8);
      $qxp = FormatAmount($qty*$item_cost,8);

      $pdf->Text($x,$y,"$date   $product_name");
      $pdf->SetXY($x+QTY_XCOOR,$y);
      $pdf->Cell(20,0,$qty,0,0,'R');

      $pdf->SetXY($x+QTY_XCOOR+PRICE_XCOOR,$y);
      $pdf->Cell(20,0,$price,0,0,'R');
      $pdf->SetXY($x+QTY_XCOOR+PRICE_XCOOR+TOTAL_XCOOR,$y);
      $pdf->Cell(20,0,$qxp,0,0,'R');

      $y += ITEM_HEIGHT; 
      if ($y >= $PAGE_HEIGHT - $product_y) {
        $product_y = 0;
        $y = $TOP;
        $pdf->AddPage();
      }
    }



    $pdf->Line(LEFT_MARGIN,$y,350,$y);

    $y += LINE_HEIGHT*2;
    $formatted_sub_total = FormatAmount($sub_total,8);
    $pdf->SetXY($x+QTY_XCOOR-5,$y);
    $pdf->Cell(10,0,'Sub Total                  $',0,0,'L');
    $pdf->SetXY($x+QTY_XCOOR+PRICE_XCOOR+TOTAL_XCOOR+9,$y);
    $pdf->Cell(10,0,$formatted_sub_total,0,0,'R');


    
    $y += ITEM_HEIGHT;

    $commission100 = $commission/100;
    $commission_amt = round(($sub_total * $commission100)/100,2);

    $formatted_commission_amt = FormatAmount($commission_amt*100,8);

    
    if ($commission != 0) {
      $pdf->SetXY($x+QTY_XCOOR-5,$y);
      $pdf->Cell(10,0,'Less Commission     $',0,0,'L');
      $pdf->SetXY($x+QTY_XCOOR+PRICE_XCOOR+TOTAL_XCOOR+9,$y);
      $pdf->Cell(10,0,$formatted_commission_amt,0,0,'R');
      $y += ITEM_HEIGHT;
    }



    $net_payment = $sub_total - $commission_amt * 100;



    $formatted_net_payment = FormatAmount($net_payment,8);
    $pdf->SetXY($x+QTY_XCOOR-5,$y);

    $pdf->SetFont('Arial','B',TEXT_PTS);
    $pdf->Cell(10,0,'AMOUNT DUE         $',0,0,'L');
    $pdf->SetXY($x+QTY_XCOOR+PRICE_XCOOR+TOTAL_XCOOR+9,$y);

    $pdf->Cell(10,0,$formatted_net_payment,0,0,'R');



    $pdf->Output($filename,'D');

  }
 /*
    generate payment
    called by Controller GENERATE_PAYMENT to create a payment statement
    search_begin_date - m/d/y begin search date
    search_end_date   - m/d/y end search date
    search_product    - product name to search for (maybe multiple words
    vendor_select     - vendor record select number (vid), if 0, none was selected
    from_hour         - hour to begin at 0 - 24
    to_hour           - hour to end at 0 - 24
    category_select   - category selection, Wine, Art, etc

    filename
    vendor_name
    address
    city
    state
    zip
    phone
    email
    min_mdy
    max_mdy
    commission
    list
      ProductName
      Qty
      ItemCost
  */

      public function GeneratePayment($search_begin_date,$search_end_date,$search_product,$vendor_select,$from_hour,$to_hour,$category_select) {
        $trace = false;
        if ($trace) WM("GeneratePayment vendor_select:$vendor_select");

        $list = array(); $count = 0;

        list ($vendor_name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->getVendorInfo($vendor_select);


        $filename =  $vendor_name . ' Payment ' .date('m-d-Y',time()).'.pdf';

        $path     = $filename;

        if ($trace) WM("GeneratePayment vendor_name:$vendor_name path:$path");

    //$min_mdy = 0;  $max_mdy = 2147483647;
        $max_mdy = 0;  $min_mdy = 2147483647;


        $db = new Database();
        $db->query("SELECT * FROM payments_list");

        

        if ($db->numRows() == 0) return;

        while ($db->nextRecord()) {
          $mdy            = $db->Record['mdy'];
          $product_name  = stripslashes($db->Record['product_name']);

          $category       = stripslashes($db->Record['category_key']);
          $sku            = $db->Record['sku'];
          $item_cost      = $db->Record['item_cost'];
          $gross          = $db->Record['gross'];
          $net            = $db->Record['net'];
          $qty            = $db->Record['quantity'];

          if ($trace) WM("GeneratePayment min_mdy:$min_mdy:".date('m/d/Y',$min_mdy). "max_mdy:$max_mdy:".date('m/d/Y',$max_mdy));

          $date = date('m/d/Y',$mdy);
          $list[$count++] = array('ProductName' => $product_name, 'Date' => $date,  'Qty' => $qty, 'ItemCost' => $item_cost);


          if ($mdy > $max_mdy) $max_mdy = $mdy;
          if ($mdy < $min_mdy) $min_mdy = $mdy;

        }
        sort($list);
        if ($trace) WM("GeneratePayment min_mdy:$min_mdy max_mdy:$max_mdy");
        $this->PaymentPDF($path,$vendor_name,$address,$city,$state,$zip,$phone,$email,$company,$min_mdy,$max_mdy,$commission,$list);

      }



  /*
    return unix time stamp last record myd in payments plus 1 day
  */

    public function LastDate() {
      $db = new Database();



      $db->query("SELECT * from payments ORDER BY mdy DESC LIMIT 1");
      if ($db->numRows() == 0) return strtotime('5/23/2014');
      $db->singleRecord();

      $paymentID = $db->Record['paymentID'];
      $mdy = $db->Record['mdy'];


      $mdy_86400 = $mdy+86400;


      return $mdy_86400;

    }
  /*
   reset payments
  */
   public function ResetPayments() {
    $db = new Database();
    $db->query("DELETE FROM payments");
  }

  private function NetTotalPaymentSKU($item,$list) {
    $total = 0;
    for ($i = 0; $i < count($list); $i++) if ($item == $list[$i]['SKU']) $total =+ $list[$i]['Net'];
      return $total;

  }
  private function NetTotalPaymentClass($item,$list) {
    $total = 0;
    for ($i = 0; $i < count($list); $i++) if ($item == $list[$i]['Class']) $total =+ $list[$i]['Net'];
      return $total;

  }

  private function InsertPaymentList($mdy,$hour,$min,$sec,$product_name,$quantity,$item_cost,$category_key,$sku,$class,$gross,$discount,$net,$sales_tax) {
    $db = new Database();
    $product_name = addslashes($product_name);
    $category_key = addslashes($category_key);
    

    $db->query("INSERT INTO payments_list(mdy,hour,min,sec,product_name,quantity,item_cost,category_key,sku,class,gross,discount,net,sales_tax) VALUES(
      '$mdy',
      '$hour',
      '$min',
      '$sec',
      '$product_name',
      '$quantity',
      '$item_cost',
      '$category_key',
      '$sku',
      '$class',
      '$gross',
      '$discount',
      '$net',
      '$sales_tax'
      )

    ");
  }

  /*
    called by Controller REPORTS to initially build category list
  */
    public function BuildCategories() {
      $db = new Database();
      $db->query("DELETE from categories");
      $db = new Database();
      $db->query("SELECT * FROM payments");

      while ($db->nextRecord()) {
        $category = stripslashes($db->Record['category_key']);

        $this->InsertCategory($category);
      }
    }
  /*
    called by PaymentList everytime an new payments_list records table is built
    this populates the table catetories with all the current categories in use 
  */
    private function InsertCategory($category) {
     
      if ($category =='') return;
      $category = addslashes($category);
      $db = new Database();
      $db->query("SELECT * FROM categories WHERE category='$category'");
      if ($db->numRows() == 0) {
         
        $db->query("INSERT INTO categories (category) VALUES('$category')");
      }
    }

  /*
    called to create payment_list table.  This was done, because internal memory could not handle so many records.
    the payment_list table is used to output the selected rows from the payments table
    category_select is the actual category name, Wine, Home & Bath, etc
    case
        case 1 - sort is item_list
        case 2 - sort is by sku
        case 3 - sort is by class
        case 4 - sort is by name
    search_begin_date
    search_end_date
    reports_filter
    search_sku
    from_hour
    to_hour
    category
    category_select
  */
    private function ReportsPaymentsList($case,$search_begin_date,$search_end_date,$reports_filter,
      $search_sku,$from_hour,$to_hour,$category_select) {

      $trace = false;




      $order_by = '';
      $where = '';
      $scase = 0;
      if ($search_begin_date == '' && $search_end_date == '') $scase = 1;
      if ($search_begin_date == '' && $search_end_date != '') $scase = 2;
      if ($search_begin_date != '' && $search_end_date == '') $scase = 3;
      if ($search_begin_date != '' && $search_end_date != '') $scase = 4;

      $bd = strtotime($search_begin_date);
      $ed = strtotime($search_end_date);
      switch ($scase) {
        case 1:
        break;
        case 2:
        $where = "WHERE `mdy` <= $ed";
        break;
        case 3:
        $where = "WHERE $bd >= `mdy`";
        break;
        case 4:
        $where = "WHERE `mdy` BETWEEN $bd AND $ed";
        break;
      }
      if ($category_select != '') {
        if ($where != '') $where .= " AND category='".$category_select."'";
        else $where = "WHERE category='$category_select'";
      }

      $order_by = 'ORDER BY mdy,hour,min,sec,sku';

      $db = new Database();
      $db->query("DELETE FROM payments_list");


      if ($trace) WM("PaymentsList case:$case reports_filter:$reports_filter search_sku:$search_sku csase:$scase SELECT * FROM payments $where $order_by ");

      $db->query("SELECT * FROM payments $where $order_by ");
      while ($db->nextRecord()) {
        $class     = substr($db->Record['sku'],0,3);
        $mdy       = $db->Record['mdy'];
        $hour      = $db->Record['hour'];
        $min       = $db->Record['min'];
        $sec       = $db->Record['sec'];
        $product_name  = $db->Record['product_name'];

        $quantity  = $db->Record['quantity'];
        $item_cost = $db->Record['item_cost'];
        $category  = stripslashes($db->Record['category_key']);
        $sku       = $db->Record['sku'];
        $gross     = $db->Record['gross'];
        $discount  = $db->Record['discount'];
        $sales_tax = $db->Record['sales_tax'];
        $net       = $db->Record['net'];



        $this->InsertPaymentList($mdy,$hour,$min,$sec,$product_name,$quantity,$item_cost,$category,$sku,$class,$gross,$discount,$net,$sales_tax);

      }

    }


    private function ExportCVS() {

     $stream = 'Date,Time,Time Zone,Category,Item,Quanity,Price Point,SKU,Modifiers,Gross Sales,Discounts,Net Sales' . "\n";
     $db = new Database();
     $db->query("SELECT * FROM payments_list ORDER by paymentListID");
     while ($db->nextRecord()) {
      $mdy        = $db->Record['mdy'];


      $product_name  = stripslashes($db->Record['product_name']);
      $category      = stripslashes($db->Record['category_key']);
      $sku           = $db->Record['sku'];
      $item_cost     = $db->Record['item_cost'];
      $gross         = $db->Record['gross'];

      $qty           = $db->Record['quantity'];
      $gross         = $db->Record['gross'];
      $hour          = $db->Record['hour'];
      $min           = $db->Record['min'];
      $discount      = $db->Record['discount'];
      $net           = $db->Record['net'];
      $hm            = $db->Record['hour'];
      $min           = $db->Record['min'];




      $gross_amount      = FormatAmountCVS($gross);
      $discount_amount   = FormatAmountCVS($discount);
      $net_amount        = FormatAmountCVS($net);


      $stream .= date('n/j/y',$mdy) . ',';
      $stream .= $hm . ':' . $min . ',';
      $stream .= 'Pacific Time' . ',';
      $stream .= $category . ',';
      $stream .= $product_name . ',';
      $stream .= $qty . ',';
      $stream .= 'Regular Price' . ',';
      $stream .= $sku . ',';
      $stream .= '' . ',';
      $stream .= $gross_amount . ',';
      $stream .= $discount_amount . ',';
      $stream .= $net_amount . ',';

      $stream .= "\n";

    }
    return $stream;
  }
  /* 
    export reports, called by controller EXPORT_PAYMENTS
    case 1 - sort is item_list (Note, case is always 1 and is ignored)
   
  */
    public function ReportsExport($case,$search_begin_date,$search_end_date,$search_product,$vendor_select,$from_hour,$to_hour,$category_select) {

      $this->ReportsPaymentsList($case,$search_begin_date,$search_end_date,$search_product,$vendor_select,$from_hour,$to_hour,$category_select);

      $export_data = $this->ExportCVS();
      return $export_data;
    }

  /*
    download item sales from square, called by Controller DOWNLOAD_PAYMENTS
    returns blank if no load error
    otherwise, returns error message
  */

    private function SetPhoto($value, $iid) {
      $trace = false;
      $db = new Database();
      if ($trace) WM("SetPhoto UPDATE inventory set photo='$value' WHERE iid='$iid'");
      $db->query("UPDATE inventory set photo='$value' WHERE iid='$iid'");
    }

  /*
    set sku value in database
  */
    private function SetSKUValue($value, $iid) {
      $db = new Database();
      $db->query("UPDATE inventory set sq_sku='$value' WHERE iid='$iid'");
    }
  /*
    update square, called by controller case EDIT_ITEM_SUBMIT
    json object returned by square
    stdClass Object
    (
    [pricing_type] => FIXED_PRICING
    [track_inventory] => 
    [inventory_alert_type] => NONE
    [id] => 5AA4AE96-C1ED-4A26-B1EA-B9B59611213D
    [name] => Regular Price
    [price_money] => stdClass Object
        (
            [currency_code] => USD
            [amount] => 300
        )

    [sku] => RET-RIVERBRO-106
    [ordinal] => 0
    [item_id] => E2FB985E-3431-4D92-AFEC-B1349A7058B2
    )
  */
public function UpdateSquareSKU($iid) {
  $trace = false;


  list ($inventory_description,$cid,$vid,$item_number,$item_id,$price,$sq_sku,$cat,$variation_id) = $this->getInventoryInfo($iid);
  list ($name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->getVendorInfo($vid);
  list ($class,$class_description) = $this->getClassInfo($cid);

  $field = 'sku';
  $the_squ = buildSKU($class,$vendor,$item_number);



  $response = putSquareVariations($item_id,$variation_id,$field,$the_squ);
  $obj = json_decode($response);

    /*
     sometimes, the square database returns a non existent sku entry.  In this case an sku entry needs to be defined
     in square.  If this happens, return a blank status value and a message that the sku value needs to be defined
     in the ret_value.
     */
     if (isset($obj->sku)) $ret_value = $obj->sku;
     else {
      $ret_value =  "SKU EXCEPTION $inventory_description $class $vendor - Define SKU for this item and try again";
      return array($inventory_description,$ret_value,'');

    }

    // update the sku value in the database to the same as the square database
    $this->SetSKUValue($ret_value,$iid);


    return array($inventory_description,$ret_value,'OK');
  }
  /*
    update class
  */
    public function UpdateClass($cid,$class,$description) {
      $description = addslashes($description);
      $db = new Database();
      $db->query("UPDATE classes SET class='$class' WHERE cid='$cid'");
      $db->query("UPDATE classes SET description='$description' WHERE cid='$cid'");


    }
  /* 
    delete class with record id cid
    will set all inventory records with this cid to 0
  */
    public function DeleteClass($cid) {
      $db = new Database();
      $db->query("DELETE FROM classes WHERE cid='$cid'");
      $db->query("UPDATE inventory SET cid='0' WHERE cid='$cid'");
    }
  /* 
    return count of inventory records that match cid
  */
    public function InventoryCount($cid) {
      $db = new Database();
      $db->query("SELECT * FROM inventory where cid='$cid'");
      return $db->numRows();
    }
  /*
    edit class submit
  */
    public function EditClassSubmit($class) {
      if ($class == '') return CLASS_BLANK;
      return EDIT_CLASS_VALID;
    }
  /*
    Assign submit
    must have the same number of vid inputs as there are vendor select columns
  */
    public function AssignSubmit($cid,$vid) {
    

      if ($cid == 0) return CLASS_NOT_SELECTED;
      if ($vid == 0) return VENDOR_NOT_SELECTED;

      return ASSIGN_VALID;
    }
  /*
    update item, called from controller EDIT_ITEM_SUBMIT (from edit_item.php)
  */
    public function UpdateItem($iid,$cid,$vid) {
      $db = new Database();
      $db->query("UPDATE inventory set cid='$cid' WHERE iid='$iid'");
      $db->query("UPDATE inventory set vid='$vid' WHERE iid='$iid'");
      list ($vendor_name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->getVendorInfo($vid);
      $db->query("UPDATE inventory set item_number='$next_number' WHERE iid='$iid'");
      $the_number = $next_number;
      $next_number += 1;
      $db->query("UPDATE vendors set next_number='$next_number' WHERE vid='$vid'");
      list ($class,$class_description) = $this->getClassInfo($cid);
      $sku = $class . '-' . $vendor . '-' . $the_number;
      $db->query("INSERT into sku (sku) VALUE('$sku')");
    }
  /*
    assign item number
  */
    public function AssignItemNo($iid,$cid,$vid) {

      $db = new Database();
      list ($vendor_name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->getVendorInfo($vid);

      $db->query("UPDATE inventory set item_number='$next_number' WHERE iid='$iid'");
      $next_number += 1;

      $db->query("UPDATE vendors set next_number='$next_number' WHERE vid='$vid'");

      $db->query("UPDATE inventory set cid='$cid' WHERE iid='$iid'");
      $db->query("UPDATE inventory set vid='$vid' WHERE iid='$iid'");


    }
  /*
    update vendor information
  */
    public function UpdateVendor($vid,$name,$company,$address,$city,$state,$zip,$phone,$email,$notes,$cid,$vendor,$commission) {
      if ($company == '') $company = $name;
      $name      = addslashes($name);
      $address   = addslashes($address);
      $city      = addslashes($city);
      $notes     = addslashes($notes);

      $company   = addslashes($company);

      $db = new Database();
      $db->query("UPDATE vendors set name='$name' WHERE vid='$vid'");
      $db->query("UPDATE vendors set company='$company' WHERE vid='$vid'");
      $db->query("UPDATE vendors set address='$address' WHERE vid='$vid'");
      $db->query("UPDATE vendors set city='$city' WHERE vid='$vid'");
      $db->query("UPDATE vendors set state='$state' WHERE vid='$vid'");
      $db->query("UPDATE vendors set zip='$zip' WHERE vid='$vid'");
      $db->query("UPDATE vendors set phone='$phone' WHERE vid='$vid'");
      $db->query("UPDATE vendors set email='$email' WHERE vid='$vid'");
      $db->query("UPDATE vendors set notes='$notes' WHERE vid='$vid'");
      $db->query("UPDATE vendors set vendor='$vendor' WHERE vid='$vid'");
      $db->query("UPDATE vendors set commission='$commission' WHERE vid='$vid'");

    }
  /*
    insert new vendor
  */

    public function InsertVendor($name,$address,$city,$state,$zip,$phone,$email,$notes,$company,$vendor,$commission) {

      if ($company == '') $company = $name;
      $name      = addslashes($name);
      $address   = addslashes($address);
      $city      = addslashes($city);
      $notes     = addslashes($notes);
      if ($vendor != '') $vendor    = getVendorField($vendor);
      if ($vendor == '') $vendor    = getVendorField($company);
      $company   = addslashes($company);
      $db = new Database();


      $db->query("INSERT INTO vendors (name,address,city,state,zip,phone,email,notes,next_number,vendor,company,commission) VALUES(
       '$name',
       '$address',
       '$city',
       '$state',
       '$zip',
       '$phone',
       '$email',
       '$notes',
       '100',
       '$vendor',
       '$company',
       '$commission'
       )");
    }

  /*
    edit vendor submit
  */
    public function EditVendorSubmit($company,$vendor) {
      if ($company == '') return COMPANY_BLANK;
      $db = new Database();



      if ($vendor == '') return EDIT_VENDOR_BLANK;
      if (!ValidVendor($vendor)) return INVALID_VENDOR;

      $db = new Database();


      return EDIT_VENDOR_VALID;
    }
  /*
    new vendor submit
  */
    public function NewVendorSubmit($company,$vendor) {
      $trace = false;
      if ($trace) WM("NewVendorSubmit company:$company vendor:$vendor");

      if ($company == '') return COMPANY_BLANK;
      $db = new Database();

      $company = addslashes($company);
      $db->query("SELECT * FROM vendors WHERE company='$company'");
      if ($db->numRows() != 0) return COMPANY_EXISTS;

      if ($vendor != '') {
        if (!ValidVendor($vendor)) return INVALID_VENDOR;
        $new_vendor = $vendor;
      }
      if ($vendor == '') $new_vendor = getVendorField($company);
      

      $db->query("SELECT * FROM vendors WHERE vendor='$new_vendor'");
      if ($db->numRows() != 0) return NEW_VENDOR_EXISTS;


      return NEW_VENDOR_VALID;
    }

    /* return list of vendors */
    private function VendorList() {
      $list = array(); $count = 0;
      $db = new Database();
      $db->query("SELECT * FROM vendors");
      while ($db->nextRecord()) {
        $vid         = $db->Record['vid'];
        $last        = getLastField($db->Record['name']);
        $vendor      = $db->Record['vendor'];  

        $list[$count++]  = array('Vendor' => $vendor, 'Last' => $last, 'VID' => $vid);
      }
      return $list;
    }

    /* get vendor info */
    public function getVendorInfo($vid) {
      $name = $address = $city = $state = $zip = $phone = $email = $notes = $vendor = $company = $commission = '';
      $next_number = 0;
      $db = new Database();

      $db->query("SELECT * FROM vendors where vid='$vid'");
      if ($db->numRows() == 0) return array($name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission);
      $db->singleRecord();
      $name        = stripslashes($db->Record['name']);
      $address     = stripslashes($db->Record['address']);
      $city        = stripslashes($db->Record['city']);
      $state       = $db->Record['state'];
      $zip         = $db->Record['zip'];
      $phone       = $db->Record['phone'];
      $email       = $db->Record['email'];

      $notes       = stripslashes($db->Record['notes']);
      $next_number = $db->Record['next_number'];
      $vendor      = $db->Record['vendor'];
      $company     = stripslashes($db->Record['company']);
      $commission  = $db->Record['commission'];
      return array($name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission);
    }

  /* 
    vendors list display for vendors.php
  */
    public function Vendors($order,$vendor_sort) {
      $shade = true;
      $edit_vendor = EDIT_VENDOR;
      $stream = '';
      $stream .= '<table><tbody>';
      $list = $this->VendorList();

      switch ($order) {
        case 'ASC':
        sort($list);
        break;
        case 'DESC':
        rsort($list);
        break;
      }



      for ($i = 0; $i < count($list); $i++) {
        $vid          = $list[$i]['VID'];
        $vendor       = $list[$i]['Vendor'];

        list ($vendor_name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->getVendorInfo($vid);





        $color = 'white';
        if ($shade) $color = '#eeeddd';
        $shade = !$shade;

        $stream  .= "<tr style=\"background:$color;\">";


        $stream  .= '<td width="300px" align="left">'."<a href=\"index.php?action=$edit_vendor&amp;vid=$vid\">$company</a>".'</td>';
        $stream  .= '<td width="150px" align="left">'.$phone.'</td>';
        $stream  .= '<td width="250px" align="left">'."<a href=\"mailto:$email\">$email</a>".'</td>';
        $stream  .= '<td width="150px" align="left">'.'<span style="font-family:courier">'.$vendor.'</span></td>';


        $stream  .= '</tr>';
      }
      return $stream;
    }
  /*
    insert new class
  */
    public function InsertClass($class,$description) {
      $class = strtoupper($class);
      $description = addslashes($description);
      $db = new Database();
      $db->query("INSERT INTO classes (class,description) VALUES('$class','$description')");
    }
  /*
    new class submit
  */
    public function NewClassSubmit($class) {


      if ($class == '') return CLASS_BLANK;
      $db = new Database();
      $db->query("SELECT * FROM classes WHERE class='$class'");
      if ($db->numRows() != 0) return CLASS_EXISTS;
      return NEW_CLASS_VALID; 
    }
    /* return select list of classes */
   
    /* 
      return select list of categories 
      sel is the actual category name, Wine, Art, etc.
    */
      public function CategorySelect($sel) {
        $trace = false;

        $stream = '';
    $stream .= '<select name="CategorySelect">';// OnChange="document.form1.submit();">';
    $stream .= '<option value="">none</option>';
    $db = new Database();
    $db->query("SELECT * FROM categories ORDER BY category");
    while ($db->nextRecord()) {

      $categoryID     = $db->Record['categoryID'];

      $category_slashes = $db->Record['category'];
      $category       = stripslashes($category_slashes);

      //WM("category_slashes:$category_slashes category:$category");

      $selected = '';
      if ($sel == $category) $selected = 'selected="selected"';
      if ($trace) WM("CategorySelect sel:$sel selected:$selected");
      $stream .= "<option value=\"$category\" $selected>$category</option>";
    }
    $stream .= '</select>';
    return $stream;

  }

  private function ListSKUs() {
    $list = array(); $count = 0;
    $db = new Database();
    $db->query("SELECT * FROM sku ORDER BY sku");
    while ($db->nextRecord()) {
      $sku = substr($db->Record['sku'],0,12);

      if (!InList($sku,$list,count($list))) $list[$count++] = $sku;
    }
    return $list;
  }

  private function FindItemID($item_id) {
    $trace = false;
    $db = new Database();
    $db->query("SELECT * from inventory where item_id='$item_id'");
    if ($db->numRows() == 0) {
      return 0;
    }
    $db->singleRecord();
    $iid = $db->Record['iid'];
    if ($trace) WM("FindItemID returns iid:$iid");
    if ($trace) WM("");
    return $iid;
  }

  


  /* 

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
  */
/*


  As Object

stdClass Object
(
    [visibility] => PUBLIC
    [available_online] => 
    [available_for_pickup] => 1
    [id] => 12B8C69F-0801-4AF5-81C4-0169EBD26C34
    [name] => Bangor Ranch - Extra Virgin
    [category_id] => B9D0B8AA-92A8-4E17-9334-FE7826FDC4F8
    [category] => stdClass Object
        (
            [id] => B9D0B8AA-92A8-4E17-9334-FE7826FDC4F8
            [name] => Olives/Oils/Vinegars
        )

    [variations] => Array
        (
            [0] => stdClass Object
                (
                    [pricing_type] => FIXED_PRICING
                    [track_inventory] => 
                    [inventory_alert_type] => NONE
                    [id] => 0DB552DC-5615-4EA5-ADEA-38B2724A1B49
                    [name] => Regular Price
                    [price_money] => stdClass Object
                        (
                            [currency_code] => USD
                            [amount] => 1500
                        )

                    [ordinal] => 0
                    [item_id] => 12B8C69F-0801-4AF5-81C4-0169EBD26C34
                )

        )

    [modifier_lists] => Array
        (
        )

    [fees] => Array
        (
        )

    [images] => Array
        (
        )
  
    As Array
    [4] => Array
        (
            [visibility] => PRIVATE
            [available_online] => 
            [available_for_pickup] => 
            [id] => 05B362D5-9C6F-494C-A6B0-54346DE16EE7
            [name] => Lemonade
            [category_id] => cbd776f2-ef9f-41d1-b5dd-cccd6c23f106
            [category] => Array
                (
                    [id] => cbd776f2-ef9f-41d1-b5dd-cccd6c23f106
                    [name] => Beverages
                )

            [variations] => Array
                (
                    [0] => Array
                        (
                            [pricing_type] => FIXED_PRICING
                            [track_inventory] => 
                            [inventory_alert_type] => NONE
                            [id] => D3EB8739-1878-4011-871C-6DD08FACA889
                            [name] => Regular Price
                            [price_money] => Array
                                (
                                    [currency_code] => USD
                                    [amount] => 200
                                )

                            [sku] => FOO-RIVERBRO-105
                            [ordinal] => 0
                            [item_id] => 05B362D5-9C6F-494C-A6B0-54346DE16EE7
                        )

                )

        )
)


--------------------->item count 11 Cowgirl Creamery - Red Hawk vi:PUBLIC sku: db_sku:RET-COWGIRLC-101<----------------------------
obj
stdClass Object
(
    [visibility] => PUBLIC
    [available_online] => 
    [available_for_pickup] => 1
    [id] => 06EF1B2E-6D53-48DF-A035-828A425475E7
    [name] => Cowgirl Creamery - Red Hawk
    [category_id] => 2023050C-7BDA-48AF-B247-113A05B60CDA
    [category] => stdClass Object
        (
            [id] => 2023050C-7BDA-48AF-B247-113A05B60CDA
            [name] => Cheese
        )

    [variations] => Array
        (
            [0] => stdClass Object
                (
                    [pricing_type] => FIXED_PRICING
                    [track_inventory] => 
                    [inventory_alert_type] => NONE
                    [id] => C6C4DF94-7723-49CF-A1B5-9D24D7A1B7B6
                    [name] => Regular Price
                    [price_money] => stdClass Object
                        (
                            [currency_code] => USD
                            [amount] => 1350
                        )

                    [sku] => RET-COWGIRLC-101
                    [ordinal] => 0
                    [item_id] => 06EF1B2E-6D53-48DF-A035-828A425475E7
                )

        )

    [modifier_lists] => Array
        (
        )

    [fees] => Array
        (
        )

    [images] => Array
        (
        )

)


function  buildSKU($class,$vendor,$item_number) {
    
    $value = "$class-$vendor-$item_number";
    return $value;
}  
  */

private function DumpItemInfo($iid,$item_id,$item_count) {
  $trace = false;
  list ($description,$cid,$vid,$item_number,$item_id,$price,$sq_sku,$cat,$variation_id) = $this->getInventoryInfo($iid);
  list ($name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->getVendorInfo($vid);
  list ($class,$class_description) = $this->getClassInfo($cid);

  WM("iid:$iid description:$description name:$name vendor:$vendor class:$class item_number:$item_number");
  $db_sku = buildSKU($class,$vendor,$item_number);

  $item_count++;
  $json = RetrieveItemInfo($item_id);
  $obj = json_decode($json);
  //if ($trace) Dump("$item_count",$obj);
    //WM("$item_count");
    //Dump("$item_count",$obj);
    //$ary = objectToArray($obj);
  $vi = $sku = $name = '';
  error_reporting(0);
  $vi = $obj->visibility;
  $name  = $obj->name;
  $sku  = $obj->variations[0]->sku;
  error_reporting(E_ALL);

  WM("--------------------->item count $item_count $name vi:$vi sku:$sku db_sku:$db_sku<----------------------------");

  return $item_count;
}
  /*
    given the name of a class, return the class id
  */
    private function getClassID($class) {
      $db = new Database();
      $db->query("SELECT * FROM classes");
      while ($db->nextRecord()) if ($class == $db->Record['class']) return $db->Record['cid'];
      return 0;
    }

  /*
    given the name of a vendor, return the vendor id
  */
    private function getVendorID($vendor) {
      $db = new Database();
      $db->query("SELECT * FROM vendors");
      while ($db->nextRecord()) if ($vendor == $db->Record['vendor']) return $db->Record['vid'];
      return 0;
    }


  /*
    decode the square sku
    if sku is blank, return 0,0,0
    the sqk consists of three fields separated by a hyphen A-B-C
    A - this is the classification field, for example
        ART - art
        FOO - food
        RET - retail
    B - this the vendor name field
        APOLLOOL - Apollo Olive Oil
        EARTHVI* - Earth & Vine
        FREJAS*  - Freja's Foods
    C - this is the item id number (starts at 100)

  */
    private function DecodeSKU($sku) {
      $cid = $vid = $item_number = 0;
      if ($sku == '') return array($cid,$vid,$item_number);
      $tokens = array(); $count = 0;
      $tok = strtok($sku, '-');
      while ($tok !== false) {
        $tokens[$count++] = $tok;
        $tok = strtok('-');
      }



      if ($count < 3) return array($cid,$vid,$item_number);
      $cid = $this->getClassID($tokens[0]);
      $vid = $this->getVendorID($tokens[1]);
      $item_number = $tokens[2];

      return array($cid,$vid,$item_number);    
    }
  /*
    called to insert an item in the inventory database
    name         - item description
    cat          - the catalog entry (class)
    price        - item price (in pennies)
    item_id      - square item id
    variation_id - square variation id
    sq_sku       - square sku

  */
    private function InsertItemID($name,$cat,$price,$item_id,$variation_id,$sq_sku,$in_tax_rate) {
      $trace = false;
      $tax_rate = $in_tax_rate * 1000; // scale tax rate from .075 to 750
      if ($trace) WM("name:$name in_tax_rate:$in_tax_rate tax_rate:$tax_rate ");

      $db = new Database();

      $name  = addslashes(ToAscii(cleancodes(tinymce_substitutions(html_entity_decode($name)))));
      $cat   = addslashes($cat);
      list ($cid,$vid,$item_number) = $this->DecodeSKU($sq_sku);
      
      $query = 
      $db->query("INSERT INTO inventory (description,cid,vid,cat,item_number,item_id,variation_id,price,sq_sku,photo,tax_rate) VALUES(
        '$name',
        '$cid',
        '$vid',
        '$cat',
        '$item_number',
        '$item_id',
        '$variation_id',
        '$price',
        '$sq_sku',
        '0',
        '$tax_rate'
        )");
    }


 /*
   refresh database
   1) delete all inventory records
   2) rebuild the inventory records from the square database
 */

public function Refresh() {
  $trace = false;
  $db = new Database();
  $db->query("DELETE FROM inventory");
  $db->query("ALTER TABLE inventory AUTO_INCREMENT = 1");

  $add_count = 0;
  $json = getSquareData();

  $obj = json_decode($json);

  if ($trace) Dump("Refresh",$obj);

  foreach ($obj as $key) {

    error_reporting(0);
    $key_name            = $key->name;
    $key_item_id         = $key->id;
    $key_tax_rate        = $key->fees[0]->rate;
    $key_cat             = $key->category->name;
    $key_price           = $key->variations[0]->price_money->amount;
    $key_variation_id    = $key->variations[0]->id;
    $key_sku             = $key->variations[0]->sku;

    error_reporting(E_ALL);

   
    $this->InsertItemID($key_name,$key_cat,$key_price,$key_item_id,$key_variation_id,$key_sku,$key_tax_rate);
    $add_count++;
  }
 
  return $add_count;
}

/* get sku info */
public function getSKUInfo($skuID) {
  $db = new Database();
  $db->query("SELECT * FROM sku where skuID='$skuID'");
  if ($db->numRows() == 0) return '';
  $db->singleRecord();
  return $db->Record['sku'];

}
/* get class info */
public function getClassInfo($cid) {
  $name = $description = '';
  $db = new Database();
  $db->query("SELECT * FROM classes where cid='$cid'");
  if ($db->numRows() == 0) return array($name,$description);
  $db->singleRecord();
  $class        = $db->Record['class'];
  $description = stripslashes($db->Record['description']);
  return array($class,$description);
}
/* return list of classes */
private function ClassList() {
  $list = array(); $count = 0;
  $db = new Database();
  $db->query("SELECT * FROM classes ORDER by class");
  while ($db->nextRecord()) {
    $cid  = $db->Record['cid'];
    list ($class,$description)  = $this->getClassInfo($cid);
    
    $list[$count++]  = array('CID' => $cid, 'Class' => $class, 'Description' => $description);
  }
  return $list;
}
/* display classes in classes.php */
public function Classes() {
  $ec  = EDIT_CLASS;
  $shade = true;
  $stream = '';
  $stream .= '<table><tbody>';
  $list = $this->ClassList();
  for ($i = 0; $i < count($list); $i++) {
    $cid         = $list[$i]['CID'];
    $class       = $list[$i]['Class'];
    $description = $list[$i]['Description'];
    $color = 'white';
    if ($shade) $color = '#eeeddd';
    $shade = !$shade;
    $stream  .= "<tr style=\"background:$color;\">";
    $stream  .= '<td width="70px" align="left">'."<a href=\"index.php?action=$ec&amp;cid=$cid\">$class</a>".'</td>';
    $stream  .= '<td width="300px" align="left">'.$description.'</td>';
    $stream  .= '</tr>';
  }
  return $stream;
}
  /* 
    return list of inventory 
    $status = mysql_query("CREATE TABLE inventory (iid INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(iid),
    description  text,
    cid          integer,
    vid          integer,
    item_number  integer,
    item_id      text,
    price        integer

  )");
  */
public function getInventoryInfo($iid) {
  $description = $price = $sq_sku = $cat = $variation_id = '';
  $cid = $vid = $item_number = $item_id = 0;
  $db = new Database();
  $db->query("SELECT * FROM inventory where iid='$iid'");
  if ($db->numRows() == 0) return array($description,$cid,$vid,$item_number,$item_id,$price,$sq_sku,$cat);
  $db->singleRecord();
  $description = stripslashes($db->Record['description']);
  $cid          = $db->Record['cid'];
  $vid          = $db->Record['vid'];
  $item_number  = $db->Record['item_number'];
  $item_id      = $db->Record['item_id'];
  $price        = $db->Record['price'];
  $sq_sku       = $db->Record['sq_sku'];
  $cat          = stripslashes($db->Record['cat']);
  $variation_id = $db->Record['variation_id'];
  return array($description,$cid,$vid,$item_number,$item_id,$price,$sq_sku,$cat,$variation_id);
}

  /* 
    return list of inventory items
    also rebuilds the list of categories
    field - NUMBER_FIELD
          - CLASS_FIELD
          - VENDOR_FIELD
          - CATEGORY_FIELD
          - DESCRIPTION_FIELD
    order - ASC
            DESC
  */
private function InventoryList($field,$order) {
  $trace = false;

  $list = array(); $count = 0;
  $db = new Database();
  $db->query("SELECT * FROM inventory");
  while ($db->nextRecord()) {
    $iid         = $db->Record['iid'];
    $cid         = $db->Record['cid'];
    $vid         = $db->Record['vid'];
    
    $photo       = $db->Record['photo'];
    $tax_rate    = $db->Record['tax_rate'];

    list ($inventory_description,$cid,$vid,$item_number,$item_id,$price,$sq_sku,$cat,$variation_id) = $this->getInventoryInfo($iid);
    list ($class,$class_description) = $this->getClassInfo($cid);
    list ($vendor_name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->getVendorInfo($vid);

    // the sorting is done by placing the desired field to sort by at the beginning of the list
    $sort_by  = '';

    list ($s_class, $s_vendor, $s_item_id) = SplitSKU($sq_sku);
    
    $d_cat = substr($cat,0,10);
    $d_dat = pad($d_cat,20,' ');

    $d_field = pad($field,5,' ');
 
   
    switch ($field) {
      case NUMBER_FIELD:
        $sort_by = $iid;
        break;
        /*
             $_SESSION['FIELD'] = CLASS_FIELD;
         $_SESSION['ORDER'] = 'ASC';
        */
      case CLASS_FIELD:
        $sort_by = $s_class;
        break;
      case CATEGORY_FIELD:
        $sort_by = $cat . $s_class  . $s_item_id . $s_vendor;
        break;
      case VENDOR_FIELD:
        $sort_by = $s_vendor . $s_class . $s_item_id;
        break;
      case DESCRIPTION_FIELD:
        $sort_by = $inventory_description . $s_vendor . $s_class . $s_item_id;
        break;
    }


    $list[$count++]  = array(
      'SortBy' => $sort_by,
      'Cat'    => $cat,
      'IID' => $iid, 
      'Description' => $inventory_description, 
      'CID' => $cid,
      'VID' => $vid, 
      'ItemNumber' => $item_number, 
      'ItemID' => $item_id, 
      'Price'  => $price, 
      'Class'  => $class, 
      'Vendor' => $vendor,
      'SQSku'  => $sq_sku,

      'Photo'  => $photo,
      'Vendor' => $vendor_name,
      'TaxRate' => $tax_rate
      );
    }



    switch ($order) {
      case 'ASC':
        sort($list);
        break;
      case 'DESC':
        rsort($list);
      break;
      }

    return $list;
  }

  /*
    sort list for export
  */

    private function ExportSort($list) {
      $export = array(); $count = 0;
      for ($i = 0; $i < count($list); $i++) {
        $item_number  = $list[$i]['ItemNumber'];
        $description  = $list[$i]['Description'];
        $price        = $list[$i]['Price'];
        $class        = $list[$i]['Class'];
        $vendor       = $list[$i]['Vendor'];
        $sq_sku       = $list[$i]['SQSku'];
        $cat          = $list[$i]['Cat'];
        $photo        = $list[$i]['Photo'];
        $vendor_name  = $list[$i]['Vendor'];
        $tax_rate     = $list[$i]['TaxRate'];



        $export[$count++]  = array(
          'Cat'    => $cat,
          'Description' => $description,
          'Price'  => $price, 
          'SQSku'  => $sq_sku,
          
          'ItemNumber' => $item_number,  
          
          'Class'  => $class, 
          'Vendor' => $vendor,
          
          'Photo'  => $photo,
          'Vendor' => $vendor_name,
          'TaxRate'=> $tax_rate
          );

      }
      sort($export);
      return $export;
    }
  /*
    inventory export as csv file
  */
    public function InventoryExport($field,$order) {
      $stream = '';
      $list = $this->InventoryList($field,$order);

      $list = $this->ExportSort($list);
      for ($i = 0; $i < count($list); $i++) {
        $item_number  = $list[$i]['ItemNumber'];
        $description  = $list[$i]['Description'];
        $price        = $list[$i]['Price'];
        $class        = $list[$i]['Class'];
        $vendor       = $list[$i]['Vendor'];
        $sq_sku       = $list[$i]['SQSku'];
        $cat          = $list[$i]['Cat'];
        $photo        = $list[$i]['Photo'];
        $vendor_name  = $list[$i]['Vendor'];
        $tax_rate     = $list[$i]['TaxRate']/1000;

        list ($dollars, $cents) = getDollarsCents($price);
        $ds = "$dollars.$cents";
        $stream      .= '"'.$sq_sku.'",';
        $stream      .= '"'.$description.'",';
        $stream      .= '"'.$cat.'",';
        $stream      .= '"'.$ds.'",';
        $stream      .= '"'.$tax_rate.'",';
        

        
        
        $stream .= "\n";

      }
      return $stream;
    }

    /*
    Display inventory records, called by inventory.php
    */
    public function Inventory($field,$order) {
      $trace = false;
      $_SESSION['LAST_FIELD'] = $field;
      $_SESSION['LAST_ORDER'] = $order;
      $shade = true;
      $edit_item = EDIT_ITEM;
      $stream = '';
      $stream .= '<table><tbody>';
      $list = $this->InventoryList($field,$order);
      


      for ($i = 0; $i < count($list); $i++) {
        $sb           = $list[$i]['SortBy'];
        $iid          = $list[$i]['IID'];
        $item_number  = $list[$i]['ItemNumber'];
        $description  = $list[$i]['Description'];
        $price        = $list[$i]['Price'];
        $class        = $list[$i]['Class'];
        $vendor       = $list[$i]['Vendor'];
        $sq_sku       = $list[$i]['SQSku'];
        $cat          = $list[$i]['Cat'];
        $tax_rate     = $list[$i]['TaxRate'];

        $is_taxed = '  ';
        if ($tax_rate != 0) $is_taxed = '-T';
 

        $color = 'white';
        if ($shade) $color = '#eeeddd';
        $shade = !$shade;

        if ($item_number == 0) $item_number = '';
        $stream  .= "<tr style=\"background:$color;\">";
        $stream  .= '<td width="40px" align="left">'."<a href=\"index.php?action=$edit_item&amp;iid=$iid\">$iid".'</a></td>';

      
        if ($sq_sku != '') $stream  .= '<td width="150px" align="left"><span style="font-family:courier">'.$sq_sku.'</span></td>';
        if ($sq_sku == '') $stream  .= '<td width="150px" align="left">&nbsp;</td>';
        $stream  .= '<td width="300px" align="left">'.$cat.'</td>';

        $stream  .= '<td width="400px" align="left">'.$description.'</td>';

        $stream  .= '<td width="100px" align="right"> $'.FormatAmount($price,6).'</td>';
        
        $stream  .= '<td width="20px" align="right">'.$is_taxed.'</td>';
        $stream  .= '</tr>';
      }
      return $stream;
    }

    /* login submit */
    public function LoginSubmit($user,$passphrase) {
     if ($user == '') return USER_BLANK;
     $hash     = hash('sha256', $passphrase);
     $db = new Database();
     $user = strtolower($user);
     $db->query("SELECT * FROM users WHERE user='$user' and hash='$hash'");


     if ($db->numRows() == 0) return INVALID_CREDENTIALS;

     return LOGIN_VALID;
   }

 }
 ?>