<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  $SQL_Rep = "SELECT *
 		FROM Street
		WHERE id_street=$id_street";
  $Street_Arr = QueryA($SQL_Rep); 
  
  if(isset($act) && $act == "upd")  
  {
    if(empty($action))$action=1;
	
    $SQL_Rep = "UPDATE Street 
	SET   
	name_street = '$name_street',
	type = '$type',
	code = '$code',
	index_street = '$index_street',
	gninmb = '$gninmb'
	WHERE 
	id_street=$id_street";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>/*alert('Запись обновлена!');*/history.go(-1);</script>");
  }
   
  if(isset($act) && $act == "del")  
  {
    $SQL_Rep = "DELETE street WHERE id_street = '$id_street'";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись удалена!');window.location.href='../ViewStreet/ViewStreet.php'</script>");
  }  
     
  
  include "UpdStreet_.php";
?>
