<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  include "../../../../include/utils.php";
  $CurrUserID = TryTAB_NUM();

	  
  $SQL_Rep = "SELECT *
 		FROM area WHERE " . (isset($name_area) ? " UPPER(name_area) LIKE UPPER('%$name_area%')" : " 1 = 0 ")
		. " ORDER BY name_area";
		
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM <=100";

  $Area = QueryA($SQL_Rep);
  
  include "ViewArea_.php";
?>
