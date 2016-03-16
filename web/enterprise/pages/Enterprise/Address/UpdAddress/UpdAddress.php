<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  
  if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  

  $SQL_Rep = "SELECT Enterprise_address.*,area.*,city.*,street.*,Enterprise_address.mail_index mail_index_address,
  			Enterprise_address.action action_address
 		FROM Enterprise_address,area,city,street
		WHERE id_enterprise_address=$id_enterprise_address
		AND Enterprise_address.id_city = city.id_city(+)
		AND Enterprise_address.id_street = street.id_street(+)
		AND Enterprise_address.id_area = area.id_area(+)
		";
  $Address_Arr = QueryA($SQL_Rep); 
  
  $id_enterprise = $Address_Arr['ID_ENTERPRISE'][0];
  
  if(isset($act) && $act == "upd")  
  {
    if(!isset($action))$action=1;
    $SQL_Rep = "
	UPDATE Enterprise_address 
	SET 
		id_type_address = '$id_type_address',
		id_country = '$id_country',
		id_area = '$id_area',
		id_city = '$id_city',
		id_street = '$id_street',
		home_num = '$home_num',
		mail_index = '$mail_index',
		d_city = '$d_city',
		abon_box = '$abon_box',
		note = '$note',
		action = '$action',
		id_department = '$id_department'
	WHERE 
		id_enterprise_address = '$id_enterprise_address'";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>/*alert('Запись обновлена!');*/window.location.href='../UpdAddress/UpdAddress.php?id_enterprise_address=".$id_enterprise_address."'</script>");
  }
   
  if(isset($act) && $act == "del")  
  {
    //$SQL_Rep = "DELETE Enterprise_address WHERE id_enterprise_address = '$id_enterprise_address'";
	$SQL_Rep = "UPDATE Enterprise_address 
	SET 
    action = 0
	WHERE 
	id_enterprise_address = '$id_enterprise_address'";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись удалена (неактивна)!');window.location.href='../ViewAddress/ViewAddress.php?id_enterprise=".$id_enterprise."'</script>");
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

  include "UpdAddress_.php";
?>
