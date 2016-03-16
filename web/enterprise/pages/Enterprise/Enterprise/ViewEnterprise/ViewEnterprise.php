<?php define("PROTECTED","OFF");

include "../../../../include/security.php";
include "../../../../include/utils.php";
$CurrUserID = TryTAB_NUM();


$SQL_Rep = "
	SELECT Enterprise.*, 
		e2.name_small name_small_parent,	
		e3.name_call name_ent_last_incarnation,
		TO_CHAR(enterprise.date_change,'DD.MM.YYYY') A
	FROM Enterprise, Enterprise e2, Enterprise e3
	WHERE 	Enterprise.id_enterprise=$id_enterprise
		AND Enterprise.id_enterprise_parent=e2.id_enterprise(+)
		AND Enterprise.id_ent_last_incarnation=e3.id_enterprise(+)";

$Enterprise_Arr = QueryA($SQL_Rep); 

$SQL_Rep = 
	"UPDATE Enterprise SET
		pop =  DECODE(pop,NULL,0,pop) + 1,
		sort =  DECODE(sort,NULL,0,sort) + 1
	WHERE 
		id_enterprise = '$id_enterprise'";

$Res = Query(0,$SQL_Rep);
F_OCICommit();


$SQL_Rep = "
	SELECT * 
	FROM Enterprise
	WHERE 	ID_ent_last_incarnation = $id_enterprise";

$Enterprise_incarnation_Arr = QueryA($SQL_Rep); 

include "ViewEnterprise_.php";
?>
