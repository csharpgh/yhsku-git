<?php
include 'head.html';


echo '<form method="post" action="">';
include 'navigation.html';

 
list ($class,$description) = $this->model->getClassInfo($cid);
DivID('notice',$notice);


DivID('logo','<img src="images/logo-100.jpg" alt="yuba harvest">');
DivID('title', 'CONFIRM DELETE CLASS');

DivID('left_button1','<input type="submit" name="ConfirmDeleteClassSubmit" value="CONFIRM" class="btn btn-primary" style="width:125px;" />');
DivID('left_button2','<input type="submit" name="ConfirmDeleteClassCancel" value="CANCEL" class="btn btn-primary" style="width:125px;" />');
DivID('confirm_class_delete_msg',"You are about to delete the class $class");
DivID('confirm_class_delete_inventory','This will remove the class from '.$this->model->InventoryCount($cid).' records');



?>
</form></body></html>