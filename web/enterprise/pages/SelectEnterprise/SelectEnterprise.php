<?php define("PROTECTED","OFF");

  include "../../include/security.php";
  include "../../include/utils.php";
  $CurrUserID = TryTAB_NUM();
  $EnterpriseName_Arr = array();
  $EnterpriseID_Arr = array();
  $Address_Arr = array();
  $AddressEnterprise_Arr = array();
  $Count = 0;

  if (  !empty($Fam) || !empty($FastSearch) || !empty($NameCall) || !empty($NameSmall) || !empty($Name) || !empty($Address))
  {
    $SQL_Rep='';
    $SQL_From='';
	
	if(!empty($Fam)) 
    {
	  $SerchName = str_replace( array(" ","'"),array("%","''"),$FastSearch );
      $SQL_Rep .= " AND ENTERPRISE.ID_Enterprise = empl.id_enterprise";
	  $SQL_From .=  ",(SELECT DISTINCT id_enterprise FROM enterprise_employee WHERE UPPER(fam) LIKE UPPER('%$Fam%')) empl";
    }
		

    if(!empty($FastSearch)) 
    {
	  $SerchName = str_replace( array(" ","'"),array("%","''"),$FastSearch );
      $SQL_Rep .= " AND upper(Name_Call||Name_Small||Name_Enterprise) like upper('%$SerchName%')";
    }
	
       if(!empty($NameCall)) 
    {
      $SerchName =  str_replace( array(" ","'"),array("%","''"),$NameCall );
      $SQL_Rep .= " AND upper(Name_Call) like upper('%$SerchName%')";
    }
    if(!empty($NameSmall)) 
    {
      $SerchName =  str_replace( array(" ","'"),array("%","''"),$NameSmall );
      $SQL_Rep .= " AND upper(Name_Small) like upper('%$SerchName%')";	
    }
    if(!empty($Name)) 
    {
      $SerchName =  str_replace( array(" ","'"),array("%","''"),$Name );
      $SQL_Rep .= " AND upper(Name_Enterprise) like upper('%$SerchName%')";	
    }
    if(!empty($Address))
    {
      $SerchName = str_replace( array(" ","'"),array("%","''"),$Address );
      $SQL_Rep .= " AND Enterprise.ID_Enterprise in
                            (
                             SELECT ID_Enterprise
			     FROM Enterprise_Address
			     WHERE upper(Addr_Text) like upper('%$SerchName%')
                  	    )";	
    }
		
	$SQL_Rep .=	" ORDER BY Name_Call";
		
	
	$SQL_Rep = "SELECT Enterprise.ID_Enterprise,Name_Call,Callsign, Name_Small,Name_Enterprise
 	            FROM Enterprise ". $SQL_From ."
	         	WHERE Enterprise.Action = 1 
	        	AND Enterprise.ID_Enterprise > 0 ". $SQL_Rep;

	
	$SQL_Rep = 'SELECT * FROM (' . $SQL_Rep . ') WHERE ROWNUM <= 100';
  
    $Res = Query(0,$SQL_Rep);
    while(F_OCIFetch($Res))
    {
      $EnterpriseID = F_OCIResult($Res,'ID_ENTERPRISE');
      $EnterpriseID_Arr[] = $EnterpriseID;
      $EnterpriseNameCall_Arr[] = F_OCIResult($Res,'NAME_CALL');
      $EnterpriseNameSmall_Arr[] = F_OCIResult($Res,'NAME_SMALL');
      $EnterpriseName_Arr[] = F_OCIResult($Res,'NAME_ENTERPRISE');
      $Callsign_Arr[] = F_OCIResult($Res,'CALLSIGN');
        
      $SQL_Rep = "SELECT ID_Enterprise_Address, Enterprise_address.ID_Type_Address, Name_Type_Address
 	            FROM Enterprise_address, Type_Address
		   WHERE ID_Enterprise = $EnterpriseID
		     AND Enterprise_address.ID_Type_Address = Type_Address.ID_Type_Address
	        ORDER BY Enterprise_address.ID_Type_Address
 		 ";
      $Res2 = Query(0,$SQL_Rep);
      while (F_OCIFetch($Res2))
      {
        $EnterpriseAddressID[$EnterpriseID][] = F_OCIResult($Res2,'ID_ENTERPRISE_ADDRESS');
        $EnterpriseAddressIDText[$EnterpriseID][] = GetAddressEnterprise(F_OCIResult($Res2,'ID_ENTERPRISE_ADDRESS'),0);
        $EnterpriseAddressType[$EnterpriseID][] = F_OCIResult($Res2,'NAME_TYPE_ADDRESS');
	
      }
    }
  } 
  include "SelectEnterprise_.php";
?>
