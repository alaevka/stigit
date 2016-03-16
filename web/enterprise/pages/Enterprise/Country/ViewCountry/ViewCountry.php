<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  include "../../../../include/utils.php";
  $CurrUserID = TryTAB_NUM();

	  
  $SQL_Rep = "SELECT *
 		FROM country WHERE " . (isset($name_country) ? " UPPER(name_country) LIKE UPPER('%$name_country%')" : " 1 = 0 ")
		. " ORDER BY name_country";
		
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM <=100";

  $Country = QueryA($SQL_Rep);
  
  include "Viewcountry_.php";
?>
