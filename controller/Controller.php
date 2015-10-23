<?php 
//include_once 'model/Model.php';
class Controller {
    
  public $model;

  public function __construct() {
    $this->model = new Model();
  }

  private function InvertOrder($changed) {

    if ($changed == 1) {
     
      return;
    }

    if ($_SESSION['FIELD'] != $_SESSION['LAST_FIELD']) {
      $_SESSION['FIELD'] = $_SESSION['LAST_FIELD'];
      $_SESSION['FIELD'] = !$_SESSION['FIELD'];
 
    }
    
        $_SESSION['LAST_ORDER'] = $_SESSION['ORDER'];
        if ($_SESSION['ORDER'] == 'ASC') $_SESSION['ORDER'] = 'DESC';
        else $_SESSION['ORDER'] = 'ASC';
    
  }
  public function SetAction() {
    if (sizeof($_POST) != 0) return false;
    return true;
  }
  /*
   index.php gets us here, if this is first time, SESSION[STATE] will be not be set
   so we set it to LOGIN

  */
  
  public function invoke() {
    $trace = false;
    set_time_limit(600);

    if ($trace) ResetWriteMsg();
    if (!isset($_SESSION['STATE']))          $_SESSION['STATE']       = LOGIN;
    if (!isset($_SESSION['ORDER']))          $_SESSION['ORDER']       = 'ASC';
    if (!isset($_SESSION['FIELD']))          $_SESSION['FIELD']       = NUMBER_FIELD;
    if (!isset($_SESSION['LAST_ORDER']))     $_SESSION['LAST_ORDER']       = 'ASC';
    if (!isset($_SESSION['LAST_FIELD']))     $_SESSION['LAST_FIELD']       = NUMBER_FIELD;
  
    if (!isset($_SESSION['LAST_ORDER']))     $_SESSION['LAST_ORDER']  = $_SESSION['ORDER'];
    if (!isset($_SESSION['NAME_ORDER']))     $_SESSION['NAME_ORDER']  = 'ASC';
    if (!isset($_SESSION['VENDOR_SORT']))    $_SESSION['VENDOR_SORT'] = 'ASC';
    if (!isset($_SESSION['VID']))            $_SESSION['VID'] = '';
    if (!isset($_SESSION['CID']))            $_SESSION['CID'] = '';

    error_reporting(0);
    $action = $_GET['action'];
    $iid    = $_GET['iid'];
    $vid    = $_GET['vid'];
    $cid    = $_GET['cid'];


    $class_select = $_POST['ClassSelect'];

    /*
       must have enough vendor select posts statements as there vendor select columns
       set vendor_select to which ever POST[VendorSelect1] or POST['VendorSelect2'] is not 0
    */

    $vendor_select = 0;
    $vendor_select1 = $_POST['VendorSelect1'];
    $vendor_select2 = $_POST['VendorSelect2'];

    if ($vendor_select1 != 0) $vendor_select = $vendor_select1;
    if ($vendor_select2 != 0) $vendor_select = $vendor_select2;

    // for case where there is just one vendor select list (reports.php PAYMENTS)
    if ($vendor_select == 0) $vendor_select = $_POST['VendorSelect'];
    $company = $_POST['Company'];
    $vendor  = $_POST['Vendor'];
    $begin_date = $_POST['BeginDate'];
    $end_date = $_POST['EndDate'];
   
    $search_begin_date = $_POST['SearchBeginDate'];
    $search_end_date = $_POST['SearchEndDate'];
    $search_product  = $_POST['ProductNameSearch'];
  

    $from_hour           = $_POST['FromHour'];
    $to_hour            = $_POST['ToHour'];
    $category_select    = $_POST['CategorySelect'];

    /*
    $_POST['BeginDate'],$_POST['EndDate']
    */
    $begin_date = $_POST['BeginDate'];
    $end_date   = $_POST['EndDate'];
 
    $from_sku   = $_POST['FromSKU'];
    $to_sku     = $_POST['ToSKU'];
    $user       = $_POST['User'];
    $passphrase = $_POST['Passphrase'];

    error_reporting(E_ALL);

    $notice = '';
    
    $changed = 0;
    if (sizeof($_POST) != 0) $changed = 1;
 
    $_SESSION['STATE'] = $this->getInputState($_SESSION['STATE']);
    
 

    if ($this->SetAction() && $action != '') {
      
      $_SESSION['STATE']     = $action;
    }


    if ($trace) {
  
      Dump("START post",$_POST);
      Dump("START get",$_GET);
      Dump("START files",$_FILES);
      Dump("SESSION",$_SESSION);
    }

    if ($trace) WM("switch(".$_SESSION['STATE'].")");
    switch ($_SESSION['STATE']) {
      case LOGOUT:
        session_destroy();
      case LOGIN:
        include 'view/login.php';
        break;
      case LOGIN_INVENTORY:
        switch ($this->model->LoginSubmit($user,$passphrase)) {
          case USER_BLANK:
            $notice = 'user blank';
            include 'view/login.php' ;
            break;
          case INVALID_CREDENTIALS:
            $notice = 'invalid credentials';
            include 'view/login.php';
            break;
          case LOGIN_VALID:
            $_SESSION['STATE'] = INVENTORY;
            $_SESSION['FIELD'] = CLASS_FIELD;
            $_SESSION['ORDER'] = 'ASC';
            include 'view/inventory.php';
          
            break;
        }
        break;
        case LOGIN_REPORTS:
          switch ($this->model->LoginSubmit($user,$passphrase)) {
            case USER_BLANK:
               $notice = 'user blank';
              include 'view/login.php' ;
              break;
            case INVALID_CREDENTIALS:
              $notice = 'invalid credentials';
              include 'view/login.php';
              break;
            case LOGIN_VALID:
              $_SESSION['STATE'] = REPORTS;
              $this->model->BuildCategories();
              include 'view/reports.php';
          
              break;
        }
        break;
       case INVENTORY:
         $_SESSION['FIELD'] = CLASS_FIELD;
         $_SESSION['ORDER'] = 'ASC';

         include 'view/inventory.php';
  
         $this->InvertOrder($changed);
         break;
  
       case REFRESH:
         $add_count = $this->model->Refresh();
         $notice = "$add_count records refreshed";
         $_SESSION['FIELD'] = CLASS_FIELD;
         include 'view/inventory.php';
         
         break;

      case EDIT_ITEM_SUBMIT:

        $this->model->UpdateItem($iid,$class_select,$vendor_select);
        list ($field,$ret_value,$status) = $this->model->UpdateSquareSKU($iid);


        if ($status == 'OK') $notice = "$field updated with $ret_value";
        else $notice = $ret_value;

        include 'view/inventory.php';
        //$this->InvertOrder($changed);
        break;
      case 76:
        $this->model->DeleteItem($iid);
         include 'view/inventory.php';
        break;
      case EDIT_ITEM:
        $_SESSION['ORDER'] = $_SESSION['LAST_ORDER'];
        include 'view/edit_item.php';
        break;
    
      case CONFIRM_CLASS_CANCEL:
      case EDIT_CLASS_CANCEL:
      case NEW_CLASS_CANCEL:
      case CLASSES:
         include 'view/classes.php';
         break;
      case NEW_CLASS:
        include 'view/new_class.php';
        break;
      case NEW_CLASS_SUBMIT:
        switch ($this->model->NewClassSubmit($_POST['Class'])) {
          case CLASS_BLANK:
            $notice = 'class blank';
            include 'view/new_class.php';
            break;
          case CLASS_EXISTS:
            include 'view/new_class.php';
            break;
          case NEW_CLASS_VALID:
            $this->model->InsertClass($_POST['Class'],$_POST['Description']);
            include 'view/classes.php';
            break;
        }
        break;
      
      case VENDOR_NAME:
      switch ($_SESSION['NAME_ORDER']) {
        case 'ASC':
          $_SESSION['NAME_ORDER'] = 'DESC';
          break;
        case 'DESC':
          $_SESSION['NAME_ORDER'] = 'ASC';
          break;
      }
      case VENDOR_SORT:
      switch ($_SESSION['VENDOR_SORT']) {
        case 'ASC':
          $_SESSION['VENDOR_SORT'] = 'DESC';
          break;
        case 'DESC':
          $_SESSION['VENDOR_SORT'] = 'ASC';
          break;
      }
      
      case CONFIRM_DELETE_VENDOR:
        include 'view/confirm_vendor_delete.php';
        break;
      case DELETE_VENDOR_CONFIRMED:
        $this->model->DeleteVendor($vid);
      case EDIT_VENDOR_CANCEL:
      case NEW_VENDOR_CANCEL:
      case VENDORS:
         include 'view/vendors.php';
         break;
      case NEW_VENDOR:
        include 'view/new_vendor.php';
        break;
      case NEW_VENDOR_SUBMIT:
        switch ($this->model->NewVendorSubmit($company,$vendor)) {
          case COMPANY_BLANK:
            $notice = 'Company blank';
            include 'view/new_vendor.php';
            break;
          case COMPANY_EXISTS:
            $notice = 'Company exists';
            include 'view/new_vendor.php';
            break;
          case INVALID_VENDOR:
            $notice = 'Vendor is invalid (must be only letters and *)';
            include 'view/new_vendor.php';
            break;
          case NEW_VENDOR_EXISTS:
            $new_vendor = getVendorField($_POST['Company']);
            $notice = $new_vendor . 'vendor exists';
            include 'view/new_vendor.php';
            break;
          case NEW_VENDOR_VALID:
            $this->model->InsertVendor($_POST['Name'],$_POST['Address'],
            $_POST['City'],$_POST['State'],$_POST['Zip'],$_POST['Phone'],$_POST['Email'],$_POST['Notes'],$_POST['Company'],$_POST['Vendor'],$_POST['Commission']);
            include 'view/vendors.php';
            break;
        }
        
        break;

      case EDIT_VENDOR:
        include 'view/edit_vendor.php';
        break;
      case EDIT_VENDOR_SUBMIT:
        switch ($this->model->EditVendorSubmit($company,$vendor)) {
          case COMPANY_BLANK:
            $notice = 'Company blank';
            include 'view/edit_vendor.php';
            break;
          case COMPANY_EXISTS:
            $notice = 'Company exists';
            include 'view/edit_vendor.php';
            break;
          case EDIT_VENDOR_BLANK:
            $notice = 'vendor field is blank';
            include 'view/edit_vendor.php';
            break;
          case INVALID_VENDOR:
            $notice = 'Vendor is invalid (must be only letters and *)';
            include 'view/edit_vendor.php';
            break;
          case COMPANY_NOT_FOUND:
            $notice = 'Company not found';
            include 'view/edit_vendor.php';
            break;
          case EDIT_VENDOR_VALID:
            error_reporting(0);
            $this->model->UpdateVendor($vid,$_POST['Name'],$_POST['Company'],$_POST['Address'],
            $_POST['City'],$_POST['State'],$_POST['Zip'],$_POST['Phone'],$_POST['Email'],$_POST['Notes'],
            $_POST['ClassSelect'],$_POST['Vendor'],$_POST['Commission']);
            error_reporting(E_ALL);
            include 'view/vendors.php';
            break;

        }
        break;
      case ASSIGN_ITEM_NO:
 
        $_SESSION['ORDER'] = $_SESSION['LAST_ORDER'];
        switch ($this->model->AssignSubmit($class_select,$vendor_select)) {
          case CLASS_NOT_SELECTED:
            $notice = 'class not selected';
            include 'view/edit_item.php';
            break;
          case VENDOR_NOT_SELECTED:
            $notice = 'vendor not selected';
            include 'view/edit_item.php';
            break;
          case ASSIGN_VALID:
            $this->model->AssignItemNo($iid,$class_select,$vendor_select);
            list ($field,$ret_value,$status) = $this->model->UpdateSquareSKU($iid);

           if ($status == 'OK') $notice = "$field assigned with $ret_value";
           else $notice = $ret_value;


           $_SESSION['CID'] = $class_select;
           $_SESSION['VID'] = $vendor_select;


            
           if ($trace) WM("ASSIGN_ITEM_NUMBER SESSION['CID']:".$_SESSION['CID']);
           if ($trace) WM("ASSIGN_ITEM_NUMBER SESSION['VID']:".$_SESSION['VID']);

            include 'view/inventory.php';
            $this->InvertOrder($changed);
            break;
        }
       
        break;

      // cases of inventory.php title links to sort by
      case NUMBER_FIELD:
        $_SESSION['FIELD'] = NUMBER_FIELD;
        include 'view/inventory.php';
        $this->InvertOrder($changed);
        break;
      case CLASS_FIELD: 
        $_SESSION['FIELD'] = CLASS_FIELD;

        include 'view/inventory.php';
        $this->InvertOrder($changed);
        break;
      case CATEGORY_FIELD:
        $_SESSION['FIELD'] = CATEGORY_FIELD;
        include 'view/inventory.php';
   
        $this->InvertOrder($changed);
        break;
      case VENDOR_FIELD:
        
        $_SESSION['FIELD'] = VENDOR_FIELD;
        include 'view/inventory.php';

        $this->InvertOrder($changed);
        break;
      case DESCRIPTION_FIELD:
        $_SESSION['FIELD'] = DESCRIPTION_FIELD;
        include 'view/inventory.php';

        $this->InvertOrder($changed);
        break;
  
      case EDIT_CLASS:
        include 'view/edit_class.php';
        break;

      case EDIT_CLASS_SUBMIT:
        switch ($this->model->EditClassSubmit($_POST['Class'])) {
          case CLASS_BLANK:
            $notice = 'class is blank';
            include 'view/edit_class.php';
            break;
          case EDIT_CLASS_VALID:
            $this->model->UpdateClass($cid,$_POST['Class'],$_POST['Description']);
            include 'view/classes.php';
            break;
        }
   
        break;
      case EDIT_CLASS_DELETE:
        include 'view/confirm_class_delete.php';
        break;
      case CONFIRM_CLASS_DELETE:
        $this->model->DeleteClass($cid);
        include 'view/classes.php';
        break;

       case RESET_PAYMENTS:
        $this->model->ResetPayments();
        include 'view/reports.php';
        break;

      case DOWNLOAD_PAYMENTS:

        $reports_notice = DownloadPaymentsReport($begin_date,$end_date);

      
      /* 
        reports is only done on initial entry to page
        after that, only REPORTS_CONTINUE is the case
      */
      case REPORTS:

        $this->model->BuildCategories();
        
      case REPORTS_CONTINUE:
        $_SESSION['STATE'] = REPORTS_CONTINUE;
        include 'view/reports.php';
        break;

      case EXPORT_PAYMENTS:
        
        
        RemoveFiles(DOWNLOAD_DIR,'.pdf');
        //                                           $search_begin_date,$search_end_date,$search_product,$vendor_select,$from_hour,$to_hour,$category_select
        $export_data = $this->model->ReportsExport(1,$search_begin_date,$search_end_date,$search_product,$vendor_select,$from_hour,$to_hour,$category_select);
        $filename = REPORTS_FILE_NAME;
        $path     = DOWNLOAD_DIR . '/' . $filename;
        file_put_contents(DOWNLOAD_DIR . '/' . $filename,$export_data);
        CallPage("download.php?filename=$filename&path=$path");
        $_SESSION['STATE'] = REPORTS;
        include 'view/reports.php';
        break;

      case EXPORT_WINE:
          $this->model->ExportWinePDF();
         $_SESSION['STATE'] = REPORTS;
 
        break;


      case EXPORT_INVENTORY:
        RemoveFiles(DOWNLOAD_DIR,'.csv');
        $export_data = $this->model->InventoryExport($_SESSION['LAST_FIELD'],$_SESSION['LAST_ORDER']);
        $_SESSION['FIELD'] = $_SESSION['LAST_FIELD'];
        $_SESSION['ORDER'] = $_SESSION['LAST_ORDER'];

        $filename = INVENTORY_FILE_NAME;
        $path     = DOWNLOAD_DIR . '/' . $filename;
        file_put_contents(DOWNLOAD_DIR . '/' . $filename,$export_data);
        CallPage("download.php?filename=$filename&path=$path");
        
        include 'view/inventory.php';

        break;
      case GENERATE_PAYMENT:
         $this->model->GeneratePayment($search_begin_date,$search_end_date,$search_product,$vendor_select,$from_hour,$to_hour,$category_select);
         $_SESSION['STATE'] = REPORTS;
        include 'view/reports.php';
        break;
      case MAP_SKU:
        include 'view/map_sku.php';
        break;
      case EXPORT_VENDORS:
         $this->model->ExportVendors();
       
     
        include 'view/vendors.php';
        break;

    }

  }
   private function getInputState($state) {
    if (isset($_POST['LoginInventory']))    return LOGIN_INVENTORY;
    if (isset($_POST['LoginReports']))      return LOGIN_REPORTS;

    /* menu selections */
    if (isset($_POST['Inventory']))         return INVENTORY;
    if (isset($_POST['Classes']))           return CLASSES;
    if (isset($_POST['Vendors']))           return VENDORS;
    if (isset($_POST['Refresh']))           return REFRESH;
    if (isset($_POST['Logout']))            return LOGOUT;
    if (isset($_POST['EditItemSubmit']))    return EDIT_ITEM_SUBMIT;
    if (isset($_POST['EditItemCancel']))    return INVENTORY;
    if (isset($_POST['NewClass']))          return NEW_CLASS;
    if (isset($_POST['NewClassSubmit']))    return NEW_CLASS_SUBMIT;
    if (isset($_POST['NewClassCancel']))    return NEW_CLASS_CANCEL;
    if (isset($_POST['NewVendor']))         return NEW_VENDOR;
    if (isset($_POST['NewVendorSubmit']))   return NEW_VENDOR_SUBMIT;
    if (isset($_POST['NewVendorCancel']))   return NEW_VENDOR_CANCEL;
    if (isset($_POST['EditVendorSubmit']))  return EDIT_VENDOR_SUBMIT;
    if (isset($_POST['EditVendorCancel']))  return EDIT_VENDOR_CANCEL;
    if (isset($_POST['AssignItemNo']))      return ASSIGN_ITEM_NO;
    if (isset($_POST['EditClassSubmit']))   return EDIT_CLASS_SUBMIT;
    if (isset($_POST['EditClassCancel']))   return EDIT_CLASS_CANCEL;
    if (isset($_POST['EditClassDelete']))   return EDIT_CLASS_DELETE;
    if (isset($_POST['ConfirmDeleteClassSubmit']))   return CONFIRM_CLASS_DELETE;
    if (isset($_POST['ConfirmDeleteClassCancel']))   return CONFIRM_CLASS_CANCEL;
    if (isset($_POST['ExportInventory']))    return EXPORT_INVENTORY;
    if (isset($_POST['Reports']))            return REPORTS;
    if (isset($_POST['DownloadPayments']))   return DOWNLOAD_PAYMENTS;
    if (isset($_POST['ResetPayments']))      return RESET_PAYMENTS;
    
    if (isset($_POST['ReportsList']))        return REPORTS_CONTINUE;

    if (isset($_POST['ReportsSKU']))         return REPORTS_CONTINUE;
    if (isset($_POST['ReportsClass']))       return REPORTS_CONTINUE;
    if (isset($_POST['ReportsName']))        return REPORTS_CONTINUE;
    if (isset($_POST['ExportPayments']))     return EXPORT_PAYMENTS;
    
    if (isset($_POST['EditVendorDelete']))    return CONFIRM_DELETE_VENDOR;
    if (isset($_POST['ConfirmDeleteVendorSubmit']))    return DELETE_VENDOR_CONFIRMED;
    if (isset($_POST['ReportsSearch']))       return REPORTS_CONTINUE;
    if (isset($_POST['ConfirmDeleteVendorCancel']))    return EDIT_VENDOR_CANCEL;
    if (isset($_POST['GeneratePayment']))    return GENERATE_PAYMENT;
    if (isset($_POST['ExportWinePDF']))      return EXPORT_WINE;
    if (isset($_POST['MapSKU']))             return MAP_SKU;
    if (isset($_POST['ExportVendors']))      return EXPORT_VENDORS;
    if (isset($_POST['EditItemDelete']))     return 76;






    /*EditVendorDelete
    ConfirmDeleteVendorSubmit
  ConfirmDeleteVendorCancel
    DivID('reports_list_button','<input type="submit" name="ReportsList" value="ITEMS" class="btn btn-primary" style="width:125px;" />');
DivID('reports_sku_button','<input type="submit" name="ReportsSKU" value="SKU" class="btn btn-primary" style="width:125px;" />');
DivID('reports_class_button','<input type="submit" name="ReportsClass" value="CLASS" class="btn btn-primary" style="width:125px;" />');

    */


    
    return $state;
  }

}

?>