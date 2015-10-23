<?php
include 'head.html';


echo '<form method="post" action="">';
include 'navigation.html';

DivID('logo','<img src="images/logo-100.jpg" alt="yuba harvest">');
DivID('title', 'CLASSES - ' . date('D, F j, Y',time()));

DivID('notice',$notice);

$new_class = NEW_CLASS;
DivID('left_button1','<input type="submit" name="NewClass" value="NEW CLASS" class="btn btn-primary" style="width:125px;" />');
DivID('classes_title',"Class&nbsp;&nbsp;&nbsp;&nbsp;Description");
DivID('classes_list',$this->model->Classes());


?>
</form></body></html>