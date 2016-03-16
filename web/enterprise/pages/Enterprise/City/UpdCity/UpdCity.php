<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  $SQL_Rep = "SELECT *
 		FROM city
		WHERE id_city=$id_city";
  $City_Arr = QueryA($SQL_Rep); 
  
  if(isset($act) && $act == "upd")  
  {
    if(empty($action))$action=1;
	
    $SQL_Rep = "UPDATE City 
	SET   
	name_city = '$name_city',
	type_city = '$type_city',
	code = '$code',
	index_ = '$index_'
	WHERE 
	id_city=$id_city";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>/*alert('Запись обновлена!');*/history.go(-1);</script>");
  }
   
  if(isset($act) && $act == "del")  
  {
    $SQL_Rep = "DELETE city WHERE id_city = '$id_city'";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись удалена!');window.location.href='../ViewCity/ViewCity.php'</script>");
  }  
     
  
  include "UpdCity_.php";
?>
