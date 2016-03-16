<?php define("PROTECTED","OFF");

include "../../include/security.php";
include "../../include/utils.php";
$CurrUserID = TryTAB_NUM();
$Count = 0;

$SQL_Rep='';
$SQL_From='';
	
if (isset($search)){	
	if(!empty($Fam)){
	  	$SerchName = str_replace( array(" ","'"),array("%","''"),$FastSearch );
	  	$SQL_Rep .= " AND ENTERPRISE.ID_Enterprise = empl.id_enterprise";
	  	$SQL_From .=  ",(SELECT DISTINCT id_enterprise FROM enterprise_employee WHERE UPPER(fam) LIKE UPPER('%$Fam%')) empl";
	}

	if(!empty($FastSearch)) {
  		$SerchName = str_replace( array(" ","'"),array("%","''"),$FastSearch );
  		$SQL_Rep .= " AND upper(Name_Call||Name_Small||Name_Enterprise) like upper('%$SerchName%')";
	}

	if(!empty($email)) {
  		$SerchName = str_replace( array(" ","'"),array("%","''"),$email );
  		$SQL_Rep .= " AND upper(email) like upper('%$SerchName%')";
	}

	if(!empty($NameCall)){
  		$SerchName =  str_replace( array(" ","'"),array("%","''"),$NameCall );
  		$SQL_Rep .= " AND upper(Name_Call) like upper('%$SerchName%')";
	}
	if(!empty($NameSmall)){
  		$SerchName =  str_replace( array(" ","'"),array("%","''"),$NameSmall );
  		$SQL_Rep .= " AND upper(Name_Small) like upper('%$SerchName%')";	
	}
	if(!empty($Name)){
  		$SerchName =  str_replace( array(" ","'"),array("%","''"),$Name );
  		$SQL_Rep .= " AND upper(Name_Enterprise) like upper('%$SerchName%')";	
	}
	
	if(!empty($id_country) || !empty($id_city) || !empty($id_street)|| !empty($id_area)){
	  	$SQL_Rep .= " AND Enterprise.id_enterprise = addr.id_enterprise";	
	  	$SQL_From .=  ",(SELECT DISTINCT id_enterprise FROM enterprise_address, area WHERE enterprise_address.id_area = area.id_area(+)"
	  	. (!empty($id_country)?" AND ID_country = $id_country":'')
	  	. (!empty($id_city)?" AND ID_city = $id_city":'')
	  	. (!empty($id_street)?" AND ID_street = $id_street":'')
		. (!empty($id_area)?" AND area.id_area = $id_area":'')
	  	. ") addr";
	}
	if(!empty($action)){
		$SQL_Rep .= " AND Enterprise.action = 0";
	}
}

$SQL_Rep .=	" ORDER BY pop DESC NULLS LAST,Name_Call";

$SQL_Rep = "
	SELECT Enterprise.ID_Enterprise, Name_Call, Callsign, Name_Small, Name_Enterprise, action
	FROM Enterprise". $SQL_From ."
	WHERE 
	1=1" . $SQL_Rep;
	

if (isset($search))
	$SQL_Rep = 'SELECT * FROM (' . $SQL_Rep . ') WHERE ROWNUM <= 100';
else
	$SQL_Rep = 'SELECT * FROM (' . $SQL_Rep . ') WHERE ROWNUM <= 15';


$EnterpriseArr = QueryA($SQL_Rep);

for ( $i = 0; $i < sizeof( $EnterpriseArr['ID_ENTERPRISE']); $i++ ) {
	$SQL_Rep = "
		SELECT ID_Enterprise_Address, Enterprise_address.ID_Type_Address, Name_Type_Address
		FROM Enterprise_address, Type_Address
		WHERE 
				ID_Enterprise = ".$EnterpriseArr['ID_ENTERPRISE'][$i]."
			AND Enterprise_address.ID_Type_Address = Type_Address.ID_Type_Address
		ORDER BY Enterprise_address.ID_Type_Address";

	$EnterpriseArr['ENTERPRISE_ADDRESS'][$i] = QueryA($SQL_Rep);
	
	if(!empty($Fam)){
		$SQL_Rep = "
			SELECT * FROM enterprise_employee 
			WHERE 	
					ID_Enterprise = ".$EnterpriseArr['ID_ENTERPRISE'][$i]." 
				AND UPPER(fam) LIKE UPPER('%$Fam%')";
		$EnterpriseArr['ENTERPRISE_EMPLOYEES'][$i] = QueryA($SQL_Rep);
	}
	
}


include "SearchEnterprise_.php";
?>
