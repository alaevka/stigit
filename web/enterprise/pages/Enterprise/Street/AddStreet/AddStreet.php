<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  if(empty($action))$action=1;
  
  if(isset($act))  
  {
    $SQL_Rep = "INSERT INTO street ( id_street,name_street, type,code,index_street,gninmb )     
                              VALUES ( seq_id_street.NEXTVAL,'$name_street','$type','$code','$index_street','$gninmb')";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись добавлена!');window.location.href='../ViewStreet/ViewStreet.php'</script>");
  }

  include "AddStreet_.php";
?>
