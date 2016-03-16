<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  if(empty($action))$action=1;
  
  if(isset($act))  
  {
    $SQL_Rep = "INSERT INTO area ( id_area, name_area, type_area, code, mail_index )     
                              VALUES ( seq_id_area.NEXTVAL,'$name_area','$type_area','$code','$mail_index')";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись добавлена!');window.location.href='../ViewArea/ViewArea.php'</script>");
  }

  include "AddArea_.php";
?>
