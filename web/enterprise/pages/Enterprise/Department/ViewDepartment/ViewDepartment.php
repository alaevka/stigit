<?php define("PROTECTED","OFF");

include "../../../../include/security.php";
include "../../../../include/utils.php";
$CurrUserID = TryTAB_NUM();


$SQL_Rep = "
	SELECT *
	FROM Enterprise
	WHERE Enterprise.id_enterprise=$id_enterprise";
	
$Enterprise_Arr = QueryA($SQL_Rep); 

$SQL_Rep = "
	SELECT *
	FROM department
	WHERE id_enterprise=$id_enterprise
	ORDER BY num_department";
	
$Enterprise_department = QueryA($SQL_Rep); 
include "ViewDepartment_.php";
?>
