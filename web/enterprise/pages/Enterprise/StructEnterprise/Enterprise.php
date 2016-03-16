<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  include "../../../../include/utils.php";
  $CurrUserID = TryTAB_NUM();


  $SQL_Rep = "SELECT *
 		FROM Enterprise
		WHERE Enterprise.id_enterprise=$id_enterprise";
		
  $Enterprise_Arr = QueryA($SQL_Rep); 
	
	
  $SQL_Rep = "SELECT *
 		FROM Enterprise_address,country,city,street,type_address,area
		WHERE Enterprise_address.id_enterprise=$id_enterprise
		AND Enterprise_address.id_country = country.id_country(+)
		AND Enterprise_address.id_city = city.id_city(+)
		AND Enterprise_address.id_street = street.id_street(+)
		AND Enterprise_address.id_area = area.id_area(+)
		AND Enterprise_address.id_type_address = type_address.id_type_address(+)";
		
  $Enterprise_address = QueryA($SQL_Rep); 	
  
    $SQL_Rep = "SELECT *
 		FROM enterprise_employee
		WHERE enterprise_employee.id_enterprise=$id_enterprise";
		
  $Enterprise_employee = QueryA($SQL_Rep); 
  include "Enterprise_.php";
?>
