<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  if(empty($action))$action=1;
  
  if(isset($act))  
  {
    $SQL_Rep = "INSERT INTO country ( id_country, name_country, action, code_m, post_zone, name_country_eng )     
                              VALUES ( seq_id_country.NEXTVAL,'$name_country',1,'$code_m','$post_zone','$name_country_eng')";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись добавлена!');window.location.href='../ViewCountry/ViewCountry.php'</script>");
  }

  include "AddCountry_.php";
?>
