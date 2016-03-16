<?php define("PROTECTED","OFF");

include "../../../../include/security.php";
  
if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

$SQL_Rep = "
  	SELECT *
 	FROM form_owner
	WHERE id_form_owner=$id_form_owner";
$arr = QueryA($SQL_Rep); 
  
if(isset($act) && $act == "upd")  {
	$action = !empty($action)?1:0;
	
    $SQL_Rep = "UPDATE form_owner 
	SET   
	name_form_small = '$name_form_small',
	name_form = '$name_form',
	action = '$action'
	WHERE 
	id_form_owner=$id_form_owner";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>/*alert('Запись обновлена!');*/history.go(-1);</script>");
}
   
if(isset($act) && $act == "del" && 0){
    $SQL_Rep = "DELETE form_owner WHERE id_form_owner = '$id_form_owner'";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись удалена!');window.location.href='../View/View.php'</script>");
}  

include "upd_.php";
?>
