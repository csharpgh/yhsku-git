<?php 
/*
 * Usage
*
<?php
// include the database class
include ('class_database.php');

// create an instance of the Database class and call it $db
$db = new Database;

// do a query to retrieve a single record
$Query = "SELECT * FROM tablename LIMIT 1";
$db->query($Query);        // query the database
$db->singleRecord();    // retrieve a single record
echo $db->Record['Field_Name'];        // output a field value from the recordset

// do a query to retrieve multiple records
$Query = "SELECT * FROM tablename";
$db->query($Query);        // query the database
while ($db->nextRecord()) {
echo $db->Record['Field_Name']."<br />rn";        // output a field value from the recordset
} // end while loop going through whole recordset

// num rows
$db->numRows();
?>
*
*/

class Database {
  var $Host     = "localhost"; // Hostname of our MySQL server.
  var $Database = "yubaharv_sku"; // Logical database name on that server.
  var $User     = "yubaharv_sku"; // User and Password for login.
  var $Password = "ClbbQt@r_TzN";
   
  var $Link_ID  = 0;  // Result of mysql_connect().
  var $Query_ID = 0;  // Result of most recent mysql_query().
  var $Record   = array();  // current mysql_fetch_array()-result.
  var $Row;           // current row number.
  var $LoginError = "";

  var $Errno    = 0;  // error state of query...
  var $Error    = "";
   
  //-------------------------------------------
  //    Connects to the database
  //-------------------------------------------
  function connect() {
    error_reporting(0);
    if( 0 == $this->Link_ID )
      
      $this->Link_ID=@mysql_connect( $this->Host, $this->User, $this->Password );

    if( !$this->Link_ID )
      $this->halt( "Link-ID == false, connect failed" );
    if( !mysql_query( sprintf( "use %s", $this->Database ), $this->Link_ID ) )
      $this->halt( "cannot use database ".$this->Database );
    error_reporting(E_ALL);
  } // end function connect

  //-------------------------------------------
  //    Queries the database
  //-------------------------------------------
  function query( $Query_String ) {
    $this->connect();
    $this->Query_ID = mysql_query( $Query_String,$this->Link_ID );
    $this->Row = 0;
    $this->Errno = mysql_errno();
    
    if( !$this->Query_ID )
      $this->halt( "Invalid SQL: ".$Query_String. ' <br />'.mysql_error() );
    return $this->Query_ID;
  } // end function query

  //-------------------------------------------
  //    If error, halts the program
  //-------------------------------------------
  function halt( $msg ) {
    printf( "</td></tr></table><b>Database error:</b> %s<br />n", $msg );
    printf( "<b>MySQL Error</b>: %s (%s)<br />n", $this->Errno, $this->Error );
    die( "Session halted." );
  } // end function halt

  //-------------------------------------------
  //    Retrieves the next record in a recordset
  //-------------------------------------------
  function nextRecord() {
    @ $this->Record = mysql_fetch_array( $this->Query_ID );
    $this->Row += 1;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();
    $stat = is_array( $this->Record );
    if( !$stat )
    {
      @ mysql_free_result( $this->Query_ID );
      $this->Query_ID = 0;
    }
    return $stat;
  } // end function nextRecord

  //-------------------------------------------
  //    Retrieves a single record
  //-------------------------------------------
  function singleRecord() {
    $this->Record = mysql_fetch_array( $this->Query_ID );
    $stat = is_array( $this->Record );
    return $stat;
  } // end function singleRecord

  //-------------------------------------------
  //    Returns the number of rows  in a recordset
  //-------------------------------------------
  function numRows() {
    return mysql_num_rows( $this->Query_ID );
  } // end function numRows


  //-------------------------------------------
  //    Returns the last record insert id
  //-------------------------------------------
  function insertID() {
    return mysql_insert_id();
  } // end function insertID

} // end class Database
?>