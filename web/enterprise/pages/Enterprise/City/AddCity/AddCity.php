<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  if(empty($action))$action=1;
  
  if(isset($act))  
  {
    $SQL_Rep = "INSERT INTO city ( id_city, name_city, type_city, code, index_ )     
                              VALUES ( seq_id_city.NEXTVAL,'$name_city','$type_city','$code','$index_')";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись добавлена!');window.location.href='../ViewCity/ViewCity.php'</script>");
  }

  include "AddCity_.php";
?>
