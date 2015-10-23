<?php
include 'head.html';


echo '<form method="post" id="form1" name="form1" action="">';
include 'navigation.html';

list ($name,$address,$city,$state,$zip,$phone,$email,$notes,$next_number,$vendor,$company,$commission) = $this->model->getVendorInfo($vid);

error_reporting(0);
$auto_fill  = $_POST['AutoFill'];
error_reporting(E_ALL);
/*
autofill entries defined in common.php function AutoFill
*/
if ($auto_fill != 0) list ($city,$state,$zip) = ExecuteAutoFill($auto_fill);

DivID('notice',$notice);
 

DivID('logo','<img src="images/logo-100.jpg" alt="yuba harvest">');
DivID('title', 'EDIT VENDOR');


DivID('left_button1','<input type="submit" name="EditVendorSubmit" value="SUBMIT" class="btn btn-primary" style="width:125px;" />');
DivID('left_button2','<input type="submit" name="EditVendorCancel" value="CANCEL" class="btn btn-primary" style="width:125px;" />');
DivID('left_button3','<input type="submit" name="EditVendorDelete" value="DELETE" class="btn btn-primary" style="width:125px;" />');

DivID('vendor_lb','Vendor cannot be blank');

DivID('company_label','Company:');
DivID('company_input',"<input type=\"text\" name=\"Company\" value=\"$company\" size=\"30\" placeholder=\"company\" />");

$mvc = MAX_VENDOR_CHARACTERS;
$siz = $mvc + 2;
DivID('vendor_label','Vendor:');
DivID('vendor_input',"<input type=\"text\" name=\"Vendor\" value=\"$vendor\" size=\"10\" maxlength=\"$mvc\" placeholder=\"vendor\" />");

DivID('name_label','Name:');
DivID('name_input',"<input type=\"text\" name=\"Name\" value=\"$name\" size=\"30\" placeholder=\"vendor\" />");


DivID('address_label','Address:');
DivID('address_input',"<input type=\"text\" name=\"Address\" value=\"$address\" size=\"30\" placeholder=\"address\" />");


DivID('autofill_label','Auto fill City, State Zip');
DivID('autofill_select',AutoFill());
DivID('city_label','City:');
DivID('city_input',"<input type=\"text\" name=\"City\" value=\"$city\" size=\"30\" placeholder=\"city\" />");

DivID('state_label','State:');
DivID('state_input',"<input type=\"text\" name=\"State\" value=\"$state\" size=\"2\" onkeypress=\"return isAlphaKey(event)\" placeholder=\"state\" />");

DivID('zip_label','Zip:');
DivID('zip_input',"<input type=\"text\" name=\"Zip\" value=\"$zip\" size=\"10\" onkeypress=\"return isZipKey(event)\" placeholder=\"zip\" />");

DivID('phone_label','Phone:');
DivID('phone_input',"<input type=\"text\" name=\"Phone\" value=\"$phone\" size=\"20\" onkeypress=\"return isPhoneKey(event)\"  placeholder=\"phone\" />");

DivID('email_label','Email:');
DivID('email_input',"<input type=\"text\" name=\"Email\" value=\"$email\" size=\"30\" placeholder=\"email\" />");

DivID('commission_label','Commission:');
DivID('commission_input',"<input type=\"number\" name=\"Commission\" value=\"$commission\" min=\"0\" max=\"100\" placeholder=\"%\" /> %");

DivID('notes_label','Notes:');
DivID('notes_input',"<textarea name=\"Notes\" value=\"$notes\" rows=\"3\" cols=\"40\" placeholder=\"notes\">$notes</textarea>");

?>
</form></body></html>