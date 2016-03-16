<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  $SQL_Rep = "SELECT Enterprise_employee.*,TO_CHAR(date_birthday,'DD.MM.YYYY') A
 		FROM Enterprise_employee
		WHERE id_enterprise_employee=$id_enterprise_employee";
  $Employee_Arr = QueryA($SQL_Rep); 
  
  $id_enterprise = $Employee_Arr['ID_ENTERPRISE'][0];
  
  
  if(isset($act) && $act == "upd")  
  {
    if(!isset($action)){$action=1;}
	if(empty($boss)) {$boss=0;} else {$boss=1;}
	
	if($boss){ // обнуление руководителя у остальных
		$SQL_Rep = "
			UPDATE Enterprise_employee 
			SET boss = ''
			WHERE
				id_department = '$id_department'";
		$Res = Query(0,$SQL_Rep);
	}
	
    $SQL_Rep = "UPDATE Enterprise_employee 
	SET
	fam = '$fam',
	imj = '$imj',
	otch = '$otch',
	phone = '$phone',
	email = '$email',
	phone4 = '$phone4',
	id_department = '$id_department',
	post = '$post',
	date_birthday = TO_DATE('$date_birthday', 'DD/MM/YYYY'),
    to_fam = '$to_fam',
	to_imj = '$to_imj',
	to_otch = '$to_otch',
    to_post = '$to_post',
	action = $action,
	mobile_phone = '$mobile_phone',
	boss = $boss,
	note = '$note'
	WHERE 
	id_enterprise_employee=$id_enterprise_employee";
    $Res = Query(0,$SQL_Rep);
		
    F_OCICommit();
	die("<script>/*alert('Запись обновлена!');*/window.location.href='../ViewEmployee/ViewEmployee.php?id_enterprise=".$id_enterprise."'</script>");
  }
  
   
  if(isset($act) && $act == "del")  
  {
    $SQL_Rep = "DELETE Enterprise_employee WHERE id_enterprise_employee = '$id_enterprise_employee'";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись удалена!');window.location.href='../ViewEmployee/ViewEmployee.php?id_enterprise=".$id_enterprise."'</script>");
  }  
     
  $SQL_Rep = "SELECT *
 		FROM Enterprise
		WHERE Enterprise.id_enterprise=$id_enterprise";
  $Enterprise_Arr = QueryA($SQL_Rep); 

  $SQL_Rep = "SELECT *
 		FROM department
		WHERE id_enterprise=$id_enterprise ORDER BY name_department";
  $Department_Arr = QueryA($SQL_Rep); 
  
  include "UpdEmployee_.php";
?>
