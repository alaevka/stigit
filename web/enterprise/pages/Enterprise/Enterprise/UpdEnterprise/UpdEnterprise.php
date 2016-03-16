<?php define("PROTECTED","OFF");

include "../../../../include/security.php";
include "../../../../include/utils.php";

if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

if(isset($act)) {

	$SQL_Rep = "
		UPDATE Enterprise SET
			name_enterprise = '$name_enterprise',
			name_small = '$name_small',
			phone = '$phone',
			fax = '$fax',
			url = '$url',
			action = '" . (isset($action)?1:0) . "',
			email = '$email',
			name_call = '$name_call',
			callsign = '$callsign',
			id_enterprise_parent = '$id_enterprise_parent',
			note = '$note',
			id_ent_last_incarnation = '$id_ent_last_incarnation',
			date_change = sysdate,
			guard_channel = '" . (isset($guard_channel)?1:0) ."'
		WHERE 
			id_enterprise = '$id_enterprise'";

	$Res = Query(0,$SQL_Rep);
	F_OCICommit();
	die("<script>/*alert('Запись обновлена!');*/window.location.href='../ViewEnterprise/ViewEnterprise.php?id_enterprise=$id_enterprise'</script>");
}

$SQL_Rep = "
	SELECT Enterprise.*, 
		e2.name_small name_small_parent,	
		e3.name_small name_ent_last_incarnation
	FROM Enterprise, Enterprise e2, Enterprise e3
	WHERE 	Enterprise.id_enterprise=$id_enterprise
		AND Enterprise.id_enterprise_parent=e2.id_enterprise(+)
		AND Enterprise.id_ent_last_incarnation=e3.id_enterprise(+)";
	
$Enterprise_Arr = QueryA($SQL_Rep); 
include "UpdEnterprise_.php";
?>
