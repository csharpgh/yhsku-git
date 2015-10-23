<?php 
require 'shared/common.php';
die;
$local = 0;

if ($local == 0) {
  define('DB_NAME',     'yubaharv_sku');
  define('DB_USER',     'yubaharv_sku');
  define('DB_PASSWORD', 'ClbbQt@r_TzN');
  define('DB_HOST',     'localhost');
}
if ($local == 1) {
  define('DB_NAME', 'realdat2_yhsku');
  define('DB_USER', 'root');
  define('DB_PASSWORD', 'root12');
  define('DB_HOST', '127.0.0.1');
}

// connect to database
function Connect() {
  error_reporting(0);
  $link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);
   error_reporting(E_ALL);
  if (!$link) {
    echo "can't link"; die;
  }
  $selected = mysql_select_db(DB_NAME);
  if (!$selected) {
    echo "can't select to:".DB_NAME; die;
  }
  echo "connected to ".DB_NAME."<br>\n";
}
function error($msg) {
  $error = mysql_error();
  echo $error;
  die;
}

function check($status, $table) {
  if (!$status) error($table);
  echo "$table created<br>\n";
}
Connect();
/*
  The sku has three fields separated by a hyphen
The first field is the general category and is three characters
The second field is the vendor and is 1 to MAX_VENDOR_CHARACTERS characters.  The underscore character is used in place of a space
The third field is the product code number and is three digits

*/

mysql_query("DROP TABLE inventory");
  $status = mysql_query("CREATE TABLE inventory (iid INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(iid),
    description  text,
    cid          integer,
    vid          integer,
    cat          text,
    item_number  integer,
    item_id      text,
    variation_id text,
    price        integer,
    sq_sku       text,
    photo        integer

  )");
  check($status,'inventory');


  $created = time();
  $passphrase = '123456';
  
  $hash     = hash('sha256', $passphrase);
   mysql_query("DROP TABLE users");
  $status = mysql_query("CREATE TABLE users (uid INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(uid),
    created      integer,
    user         varchar(30), INDEX(user),
    hash         char(64),    INDEX(hash),
    role         integer

  )");
  $hash     = hash('sha256', '123456');
  $status = mysql_query("INSERT INTO users (created,user,hash,role) VALUES('$created','admin','$hash','0') ");
  check($status,'insert user 1');






// classes
mysql_query("DROP TABLE classes");
  $status = mysql_query("CREATE TABLE classes (cid INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(cid),
    class       text,
    description text

  )");
  check($status,'classes');

// vendors 
mysql_query("DROP TABLE vendors");
  $status = mysql_query("CREATE TABLE vendors (vid INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(vid),
    name     text,
    address  text,
    city     text,
    state    varchar(2),
    zip      varchar(9),
    phone    text,
    email    text,
    notes    text,
    next_number integer,
    vendor   text,
    company  text,
    commission integer

  )");
  check($status,'vendors');



mysql_query("DROP TABLE payments");
$status = mysql_query("CREATE TABLE payments(paymentID INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(paymentID),
  created_at text,
  mdy       integer,
  hour      integer,
  min       integer,
  sec       integer,
  product_name       text,
  quantity  integer,
  item_cost integer,
  id        text,

  category_key char(50), INDEX(category_key),
  sku       text,
  class     text,
  gross     integer,
  discount  integer,
  net       integer,
  hash      char(64),    INDEX(hash),
  sales_tax integer
  )");
check($status,'payments');


mysql_query("DROP TABLE payments_list");
$status = mysql_query("CREATE TABLE payments_list(paymentListID INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(paymentListID),
  created_at text,
  mdy       integer,
  hour      integer,
  min       integer,
  sec       integer,
  product_name   text,
  quantity  integer,
  item_cost integer,

  category_key char(50), INDEX(category_key),
  sku       text,
  class     text,
  gross     integer,
  discount  integer,
  net       integer,
  sales_tax integer
  )");
check($status,'payments_list');


mysql_query("DROP TABLE sku");
$status = mysql_query("CREATE TABLE sku(skuID INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(skuID),
  sku       varchar(32),      INDEX(sku)
  )");
check($status,'sku');

$result = mysql_query("SELECT * from inventory");
for ($i = 0; $i < mysql_num_rows($result); $i++) {
  $sq_sku = mysql_result($result, $i,'sq_sku');
  if (!InSKU($sq_sku)) AddSKU($sq_sku);
}



function InSKU($sq_sku) {
  $result = mysql_query("SELECT * from sku WHERE sku='$sq_sku'");
  
  //$result = mysql_query("SELECT * from sku WHERE sku='$sq_sku'");
  //if (mysql_num_rows($result) != 0) return true;
  //return false;
}
function AddSKU($sq_sku) {
  mysql_query("INSERT into sku (sku) VALUES('$sq_sku')");

}


// classes
mysql_query("DROP TABLE categories");
  $status = mysql_query("CREATE TABLE categories (categoryID INTEGER NOT NULL AUTO_INCREMENT, PRIMARY KEY(categoryID),
  category text

  )");
  check($status,'categories');
  

  
?>