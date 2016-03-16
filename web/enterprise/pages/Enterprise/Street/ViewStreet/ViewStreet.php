<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  include "../../../../include/utils.php";
  $CurrUserID = TryTAB_NUM();

	  
  $SQL_Rep = "SELECT *
 		FROM street WHERE " . (isset($name_street) ? " UPPER(name_street) LIKE UPPER('%$name_street%')" : " 1 = 0 ")
		. " ORDER BY name_street";
		
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM <=100";

  $Street = QueryA($SQL_Rep);
  
  include "ViewStreet_.php";
?>
