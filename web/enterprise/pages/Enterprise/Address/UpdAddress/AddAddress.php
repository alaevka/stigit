<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";

  if(isset($act))  
  {
  
    $SQL_Rep = "INSERT INTO Enterprise_address ( id_enterprise_address, id_enterprise,
	id_country,id_area,id_city,id_street,home_num, mail_index,id_type_address )     
                              VALUES ( seq_id_enterprise_address.NEXTVAL,'$id_enterprise'
	,'$id_country','$id_area','$id_city','$id_street','$home_num','$mail_index','$id_type_address' )";

    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись добавлена!');window.location.href='../ViewAddress/ViewAddress.php?id_enterprise=".$id_enterprise."'</script>");
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

  include "AddAddress_.php";
?>
