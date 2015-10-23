<?php
include 'head.html';


echo '<form method="post" action="">';
include 'navigation.html';

DivID('logo','<img src="images/logo-100.jpg" alt="yuba harvest">');
DivID('title', 'MAP SKU');

DivID('notice',$notice);

DivID('top_row_button1','<input type="submit" name="NewMapping" value="NEW MAPPING" class="btn btn-primary" style="width:125px;" />');
DivID('top_row_button2','<input type="submit" name="UpdateMapping" value="UPDATE" class="btn btn-primary" style="width:125px;" />');

DivID('map_sku_title',"<pre>Product                                   From SKU  To SKU</pre>");
DivID('map_sku_list',$this->model->MapSKUList());


?>
</form></body></html>