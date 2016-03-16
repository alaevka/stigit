<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  include "../../../../include/utils.php";
  $CurrUserID = TryTAB_NUM();

	  
  $SQL_Rep = "SELECT *
 		FROM city WHERE " . (isset($name_city) ? " UPPER(name_city) LIKE UPPER('%$name_city%')" : " 1 = 0 ")
		. " ORDER BY name_city";
		
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM <=100";

  $City = QueryA($SQL_Rep);
  
  include "ViewCity_.php";
?>
