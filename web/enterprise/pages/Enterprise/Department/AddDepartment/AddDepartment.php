<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  if(isset($act))  
  {
    $SQL_Rep = "INSERT INTO department ( id_department,name_department,num_department,id_enterprise,fax,phone,email,action_dep)     
                              VALUES ( seq_id_department.NEXTVAL,'$name_department','$num_department','$id_enterprise','$fax','$phone','$email',1)";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись добавлена!');window.location.href='../ViewDepartment/ViewDepartment.php?id_enterprise=".$id_enterprise."'</script>");
  }
  
  $SQL_Rep = "SELECT *
 		FROM Enterprise
		WHERE Enterprise.id_enterprise=$id_enterprise";
  $Enterprise_Arr = QueryA($SQL_Rep); 

  include "AddDepartment_.php";
?>
