<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  $SQL_Rep = "SELECT *
 		FROM Area
		WHERE id_area=$id_area";
  $Area_Arr = QueryA($SQL_Rep); 
  
  if(isset($act) && $act == "upd")  
  {
    if(empty($action))$action=1;
	
    $SQL_Rep = "UPDATE Area 
	SET   
	name_area = '$name_area',
	type_area = '$type_area',
	code = '$code',
	mail_index = '$mail_index'
	WHERE 
	id_area=$id_area";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>/*alert('Запись обновлена!');*/history.go(-1);</script>");
  }
   
  if(isset($act) && $act == "del")  
  {
    $SQL_Rep = "DELETE area WHERE id_area = '$id_area'";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись удалена!');window.location.href='../ViewArea/ViewArea.php'</script>");
  }  
     
  
  include "UpdArea_.php";
?>
