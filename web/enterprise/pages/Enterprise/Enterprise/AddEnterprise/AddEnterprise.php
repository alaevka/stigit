<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  include "../../../../include/utils.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");
  
  if(isset($act))  
  {
    $SQL_Rep = "INSERT INTO Enterprise ( id_enterprise, name_enterprise,name_small,phone,fax,url,action,email,
										name_call,callsign,id_enterprise_parent, note,id_ent_last_incarnation )     
                              VALUES ( seq_id_enterprise.NEXTVAL,'$name_enterprise','$name_small','$phone','$fax','$url','1','$email',
							 			'$name_call','$callsign','$id_enterprise_parent', '$note','$id_ent_last_incarnation' )";
    $Res = Query(0,$SQL_Rep);
	
	$Res = Query(1,"SELECT seq_id_enterprise.currval id_enterprise FROM dual");
	$id_enterprise = F_OCIResult($Res,"ID_ENTERPRISE");
	
	F_OCICommit();
	/*die("<script>alert('Запись добавлена!');window.location.href='../../../SearchEnterprise/SearchEnterprise.php?Name=".$name_enterprise."'</script>");*/
    die("<script>alert('Запись добавлена!');window.location.href='../ViewEnterprise/ViewEnterprise.php?id_enterprise=$id_enterprise';</script>");

  }
  
  $SQL_Rep = "SELECT *
 		FROM form_owner
		WHERE action=1
		ORDER BY name_form_small";
  $form_owner = QueryA($SQL_Rep); 
  include "AddEnterprise_.php";
?>
