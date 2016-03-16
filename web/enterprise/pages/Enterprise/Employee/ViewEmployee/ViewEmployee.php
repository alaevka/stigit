<?php define("PROTECTED","OFF");

  include "../../../../include/security.php";
  include "../../../../include/utils.php";
  $CurrUserID = TryTAB_NUM();


  $SQL_Rep = "SELECT *
 		FROM Enterprise
		WHERE Enterprise.id_enterprise=$id_enterprise";
		
  $Enterprise_Arr = QueryA($SQL_Rep); 
  
  $SQL_Rep = '';
  
  if (isset($search))
  {	
    if(!empty($Fam)) 
    {
	  $SerchName = str_replace( array(" ","'"),array("%","''"),$Fam );
      $SQL_Rep .= " AND UPPER(Fam) like upper('%$SerchName%')";
    }

    if(!empty($NameDepartment)) 
    {
	  $SerchName = str_replace( array(" ","'"),array("%","''"),$NameDepartment );
      $SQL_Rep .= " AND UPPER(Name_Department) like upper('%$SerchName%')";
    }
	
       if(!empty($Post)) 
    {
      $SerchName =  str_replace( array(" ","'"),array("%","''"),$Post );
      $SQL_Rep .= " AND UPPER(Post) like upper('%$SerchName%')";
    }
  
  } 
  
  $SQL_Rep = "SELECT enterprise_employee.*,department.name_department,TO_CHAR(date_birthday,'DD.MM.YYYY') A
 		FROM department, enterprise_employee
		WHERE enterprise_employee.id_enterprise = $id_enterprise 
		  AND enterprise_employee.id_department = department.id_department(+)
		  " . $SQL_Rep . "
		  ORDER BY action DESC,fam";
		
  $Enterprise_employee = QueryA($SQL_Rep);
  include "ViewEmployee_.php";
?>
