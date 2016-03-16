<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  include "../../../../include/utils.php";
  $CurrUserID = TryTAB_NUM();

	  
  $SQL_Rep = "SELECT *
 		FROM form_owner WHERE " . (isset($name_form) ? " UPPER(name_form) LIKE UPPER('%$name_form%')" : '1=1')
		. " ORDER BY name_form";
		
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM <=100";

  $arr = QueryA($SQL_Rep);
  
  include "view_.php";
?>
