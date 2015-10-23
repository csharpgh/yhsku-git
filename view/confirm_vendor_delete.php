<?php
include 'head.html';


echo '<form method="post" action="">';
include 'navigation.html';

 
list ($vendor_name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->model->getVendorInfo($vid);
WM("confirm_vendor_depete vendor_name:$vendor_name vendor:$vendor company:$company");
DivID('notice',$notice);


DivID('logo','<img src="images/logo-100.jpg" alt="yuba harvest">');
DivID('title', 'CONFIRM DELETE VENDOR');

DivID('left_button1','<input type="submit" name="ConfirmDeleteVendorSubmit" value="CONFIRM" class="btn btn-primary" style="width:125px;" />');
DivID('left_button2','<input type="submit" name="ConfirmDeleteVendorCancel" value="CANCEL" class="btn btn-primary" style="width:125px;" />');
DivID('confirm_class_delete_msg',"Please confirm that you want to delete the vendor: $vendor_name $company $vendor");



?>
</form></body></html>