<?php
include 'head.html';
echo '<form method="post" action="">';

$user =  '';

if (isset($_POST['User'])) $user = $_POST['User'];


DivID('logo','<img src="images/logo-100.jpg" alt="yuba harvest">');
DivID('title', 'Inventory SKU Log In');

DivID('notice',$notice);
 
DivID('user_label','<h4><span class="label label-primary">User:</span></h4>');
DivID('user_input',"<input type=\"text\" name=\"User\" size=\"20\" value=\"$user\" />");
DivID('pass_label','<h4><span class="label label-primary">Passphrase:</span></h4>');
DivID('pass_input','<input type="text" name="Passphrase" size="20" />');

DivID('login_submit1','<input type="submit" value="INVENTORY" name="LoginInventory"  class="btn btn-primary" />');
DivID('login_submit2','<input type="submit" value="REPORTS" name="LoginReports"  class="btn btn-primary" />');

/*
DivID('login_submit1','<input type="submit" value="INVENTORY" name="LoginInventory"  class="submit_button" />');
DivID('login_submit2','<input type="submit" value="REPORTS" name="LoginReports"  class="submit_button" />');
*/
echo '</form>';
include 'tail.html';
?>