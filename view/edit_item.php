<?php
include 'head.html';


echo '<form method="post" action="">';
include 'navigation.html';

list ($description,$cid,$vid,$item_number,$item_id,$price,$sq_squ,$cat,$variation_id) = $this->model->getInventoryInfo($iid);

/*
  $_SESSION['CID'] will be set to the class id from the last class/vendor assignment (edit_item.php)
  $_SESSION['VID'] will be set to the class id from the last class/vendor assignment (edit_item.php)
  if cid is blank, assign cid to the session variable so it will be the same as the last class/vendor assignment
  if vid is blank, assign vid to the session variable so it will be the same as the last class/vendor assignment
 
*/

if ($cid == '' || $cid == 0) $cid = $_SESSION['CID'];
if ($vid == '' || $vid == 0) $vid = $_SESSION['VID'];

DivID('logo','<img src="images/logo-100.jpg" alt="yuba harvest">');
DivID('title', 'EDIT ITEM');

DivID('notice',$notice);

DivID('left_button2','<input type="submit" name="EditItemCancel" value="CANCEL", class="btn btn-primary" style="width:125px;" />');
DivID('left_button3','<input type="submit" name="EditItemDelete" value="DELETE", class="btn btn-primary" style="width:125px;" />');


DivID('item_class_label','Class:');
DivID('item_class_input',$this->model->ClassSelect($cid));


DivID('item_vendor_label','Vendor:');


DivID('item_vendor_input',$this->model->VendorSelectSegment(1,$vid).'&nbsp;&nbsp;'.$this->model->VendorSelectSegment(2,$vid));



DivID('item_item_no_label','Item No:');
if ($item_number == 0) {
	DivID('left_button1','<input type="submit" name="AssignItemNo" value="SUBMIT", class="btn btn-primary" style="width:125px;" />');
	DivID('item_item_no_input','to be assigned');
}
if ($item_number != 0) {
	DivID('left_button1','<input type="submit" name="EditItemSubmit" value="SUBMIT", class="btn btn-primary" style="width:125px;" />');
	DivID('item_item_no_input',$item_number);
}

DivID('item_description_label','Description:');
DivID('item_description_value',$description);

?>
</form></body></html>