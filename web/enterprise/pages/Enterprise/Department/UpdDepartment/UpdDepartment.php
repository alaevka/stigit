<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  $SQL_Rep = "SELECT *
 		FROM department
		WHERE id_department=$id_department";
  $Department_Arr = QueryA($SQL_Rep); 
  
  $id_enterprise = $Department_Arr['ID_ENTERPRISE'][0];
  
  if(isset($act) && $act == "upd")  
  {
    $SQL_Rep = "
	UPDATE department 
	SET
		name_department = '$name_department',
		num_department = '$num_department',
		fax = '$fax',
		phone = '$phone',
		email = '$email',
		action_dep = $action_dep
	WHERE 
		id_department=$id_department";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>/*alert('Запись обновлена!');*/window.location.href='../ViewDepartment/ViewDepartment.php?id_enterprise=".$id_enterprise."'</script>");
  }
   
  if(isset($act) && $act == "del")  
  {
    $SQL_Rep = "DELETE department WHERE id_department = '$id_department'";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись удалена!');window.location.href='../ViewDepartment/ViewDepartment.php?id_enterprise=".$id_enterprise."'</script>");
  }  
     
  $SQL_Rep = "SELECT *
 		FROM Enterprise
		WHERE Enterprise.id_enterprise=$id_enterprise";
  $Enterprise_Arr = QueryA($SQL_Rep); 

  include "UpdDepartment_.php";
?>
