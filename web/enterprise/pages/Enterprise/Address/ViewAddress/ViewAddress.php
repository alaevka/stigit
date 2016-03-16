<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  include "../../../../include/utils.php";
  $CurrUserID = TryTAB_NUM();


  $SQL_Rep = "SELECT *
 		FROM Enterprise
		WHERE Enterprise.id_enterprise=$id_enterprise";
		
  $Enterprise_Arr = QueryA($SQL_Rep); 
  
  $SQL_Rep = "
  		SELECT 
			Enterprise_address.*, country.*,city.*,street.*,
			type_address.*, area.*, department.name_department,
            Enterprise_address.mail_index mail_index_address,
			Enterprise_address.action action_address
 		FROM Enterprise_address,country,city,street,type_address,area,department
		WHERE Enterprise_address.id_enterprise=$id_enterprise
			AND Enterprise_address.id_country = country.id_country(+)
			AND Enterprise_address.id_city = city.id_city(+)
			AND Enterprise_address.id_street = street.id_street(+)
			AND Enterprise_address.id_area = area.id_area(+)
			AND Enterprise_address.id_type_address = type_address.id_type_address(+)
			AND Enterprise_address.id_department = department.id_department(+)
		ORDER BY action_address DESC";

  $Enterprise_address = QueryA($SQL_Rep); 
  
	$SQL_Rep = "
		UPDATE Enterprise SET
			sort = sort+1
		WHERE 
			id_enterprise = '$id_enterprise'";

	$Res = Query(0,$SQL_Rep);
    F_OCICommit();
  
  include "ViewAddress_.php";
?>
