<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  $SQL_Rep = "SELECT *
 		FROM country
		WHERE id_country=$id_country";
  $Country_Arr = QueryA($SQL_Rep); 
  
  if(isset($act) && $act == "upd")  
  {
    if(!isset($action))$action=1;
	
    $SQL_Rep = "UPDATE Country 
	SET   
	name_country = '$name_country', 
	action = '$action',
	code_m = '$code_m',
	post_zone = '$post_zone',
	name_country_eng = '$name_country_eng'
	WHERE 
	id_country=$id_country";//die ($SQL_Rep );
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>/*alert('Запись обновлена!');*/history.go(-1);</script>");
  }
   
  if(isset($act) && $act == "del")  
  {
    $SQL_Rep = "DELETE country WHERE id_country = '$id_country'";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись удалена!');window.location.href='../ViewCountry/ViewCountry.php'</script>");
  }  
     
  
  include "UpdCountry_.php";
?>
