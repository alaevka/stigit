<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";

  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  if(isset($act))  
  {
  
    $SQL_Rep = "INSERT INTO Enterprise_address ( id_enterprise_address, id_enterprise,
	id_country,id_area,id_city,id_street,home_num, mail_index,id_type_address,d_city,abon_box,note,action,id_department )     
                              VALUES ( seq_id_enterprise_address.NEXTVAL,'$id_enterprise'
	,'$id_country','$id_area','$id_city','$id_street','$home_num','$mail_index','$id_type_address','$d_city','$abon_box','$note',1,'$id_department' )";

    $Res = Query(0,$SQL_Rep);
	
	$Curr = QueryA("SELECT seq_id_enterprise_address.CURRVAL ID_CURR FROM DUAL");
	
    F_OCICommit();
	die("<script>alert('Запись добавлена!');window.location.href='../UpdAddress/UpdAddress.php?id_enterprise_address=".$Curr["ID_CURR"][0]."'</script>");
  }
  
  $SQL_Rep = "SELECT *
 		FROM Enterprise
		WHERE Enterprise.id_enterprise=$id_enterprise";
  $Enterprise_Arr = QueryA($SQL_Rep); 

  $SQL_Rep = "SELECT *
                FROM country
	    ORDER BY name_country";
  $country_arr = QueryA($SQL_Rep); 
  
    $SQL_Rep = "SELECT *
                FROM type_address
	    ORDER BY name_type_address";
  $type_address_arr = QueryA($SQL_Rep);
  
    $SQL_Rep = "SELECT *
 		FROM department
		WHERE id_enterprise=$id_enterprise ORDER BY name_department";
  $Department_Arr = QueryA($SQL_Rep); 

  include "AddAddress_.php";
?>
