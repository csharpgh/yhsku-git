<?php
include 'head.html';


echo '<form method="post" action="">';
include 'navigation.html';

 
list ($class,$description) = $this->model->getClassInfo($cid);
DivID('notice',$notice);


DivID('logo','<img src="images/logo-100.jpg" alt="yuba harvest">');
DivID('title', 'EDIT CLASS');

DivID('left_button1','<input type="submit" name="EditClassSubmit" value="SUBMIT" class="btn btn-primary" style="width:125px;" />');
DivID('left_button2','<input type="submit" name="EditClassCancel" value="CANCEL" class="btn btn-primary" style="width:125px;" />');

DivID('left_button3','<input type="submit" name="EditClassDelete" value="DELETE" class="btn btn-primary" style="width:125px;" />');

DivID('class_class_label','Class:');
DivID('class_class_input',"<input type=\"text\" name=\"Class\" value=\"$class\" size=\"30\" placeholder=\"class\" />");

DivID('class_description_label','Last:');
DivID('class_description_input',"<textarea name=\"Description\" rows=\"3\" cols=\"80\" placeholder=\"description\">$description</textarea>");

?>
</form></body></html>