<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  if(empty($action))$action=1;
  if(empty($boss))$boss=0; else $boss=1;
  
  if(isset($act))  
  {
    $SQL_Rep = "INSERT INTO Enterprise_employee ( id_enterprise_employee,id_enterprise, fam,
	imj,otch,phone,email,phone4,id_department,post,date_birthday,to_fam,to_imj,to_otch,to_post,action, mobile_phone,boss )     
                              VALUES ( seq_id_enterprise_employee.NEXTVAL,'$id_enterprise','$fam',
	'$imj','$otch','$phone','$email','$phone4','$id_department','$post',TO_DATE('$date_birthday', 'DD/MM/YYYY'),
	'$to_fam','$to_imj','$to_otch','$to_post',$action,'$mobile_phone',$boss)";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись добавлена!');window.location.href='../ViewEmployee/ViewEmployee.php?id_enterprise=".$id_enterprise."'</script>");
  }
  
  $SQL_Rep = "SELECT *
 		FROM Enterprise
		WHERE Enterprise.id_enterprise=$id_enterprise";
  $Enterprise_Arr = QueryA($SQL_Rep); 
  
  $SQL_Rep = "SELECT *
 		FROM department
		WHERE id_enterprise=$id_enterprise ORDER BY name_department";
  $Department_Arr = QueryA($SQL_Rep); 

  include "AddEmployee_.php";
?>
