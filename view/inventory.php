<?php
include 'head.html';


echo '<form method="post" id="form1" name="form1" action="">';
include 'navigation.html';

error_reporting(0);
$find = $_POST['Find'];
$ta   = $_POST['TitleArtist'];
$cc   = $_POST['CheckClear'];
$ss   = $_POST['StatusSelect'];
error_reporting(E_ALL);

DivID('logo','<img src="images/logo-100.jpg" alt="yuba harvest">');
DivID('title', 'INVENTORY - ' . date('D, F j, Y',time()));

DivID('notice',$notice);
DivID('top_row_button1','<input type="submit" name="Refresh" value="REFRESH" class="btn btn-primary" style="width:125px;" />');

DivID('top_row_button2','<input type="submit" name="ExportInventory" value="PRODUCT LIST" class="btn btn-primary" style="width:125px;" />');
DivID('top_row_button3','<input type="submit" name="ExportWinePDF" value="WINE PDF" class="btn btn-primary" style="width:125px;" />');




$nf   = NUMBER_FIELD;
$cf   = CLASS_FIELD;
$vf   = VENDOR_FIELD;
$df   = DESCRIPTION_FIELD;
$catf = CATEGORY_FIELD;
DivID('inventory_title',"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?action=$cf\">Class</a>-<a href=\"index.php?action=$vf\">Vendor</a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?action=$catf\">Category</a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?action=$df\">Description</a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	&nbsp;&nbsp;Price&nbsp;&nbsp;T");



DivID('inventory_list',$this->model->Inventory($_SESSION['FIELD'],$_SESSION['ORDER']));

?>
</form></body></html>