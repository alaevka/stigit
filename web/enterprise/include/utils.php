<? if ( !defined("PROTECTED") ) { echo "Hacking attention"; die(); }

//*******************************************************************//
//                 Текст Адреса по ID                                //
//*******************************************************************//  
function GetAddressEnterprise ( $EntAddrID, $type )
{
  GLOBAL $conn;
  if ($type == 1 ) $raz=", <br>"; else $raz=", ";
  
  $Address = '';
  $SQL_Rep = "SELECT ID_Country,ID_City,ID_Area,Mail_Index,ID_Street,Home_Num
                FROM Enterprise_Address
	       WHERE ID_Enterprise_Address = $EntAddrID
	     ";
  $Res = Query(1,$SQL_Rep);  
    
  $CountryID  = F_OCIResult($Res,'ID_COUNTRY');
  $CityID     = F_OCIResult($Res,'ID_CITY');
  $AreaID     = F_OCIResult($Res,'ID_AREA');
  $Mail_Index = F_OCIResult($Res,'MAIL_INDEX');
  $StreetID   = F_OCIResult($Res,'ID_STREET');
  $NumHome    = F_OCIResult($Res,'HOME_NUM');
   
  if (!empty($StreetID))
  {
    $SQL_Rep = "SELECT Name_Street, Type
                  FROM Street
	         WHERE ID_Street = $StreetID
	       ";
    $Res = Query(1,$SQL_Rep);
    $Address = F_OCIResult($Res,'NAME_STREET').' '.F_OCIResult($Res,'TYPE').'.';
  }
  
  if (!empty ($NumHome))
  {
    if (!empty($Address)) $Address .= ', ';
    $Address .= 'д. '.$NumHome;
  }
  
  if (!empty($CityID))
  {
    $SQL_Rep = "SELECT Name_city, type_city
	          FROM City
	         WHERE ID_City = $CityID
	       ";
    $Res = Query(1,$SQL_Rep);
    $Name = F_OCIResult($Res,'NAME_CITY');
    if (!empty ($Name))
    {
      if (!empty($Address)) $Address .= $raz;
      $Address .= F_OCIResult($Res,'TYPE_CITY').'. '.$Name;
    }
  }
    
  if (!empty($AreaID))
  {
    $SQL_Rep = "SELECT *
	          FROM area
	         WHERE ID_Area = $AreaID
	       ";
    $Res = Query(1,$SQL_Rep);
    $Name = F_OCIResult($Res,'NAME_AREA');
    if(!empty($Name)) 
    {
      if (!empty($Address)) $Address .= $raz;
      $Address .= $Name.' '.F_OCIResult($Res,'TYPE_AREA').'.';
    }
  }
    
  if (!empty($CountryID))
  {
    $SQL_Rep = "SELECT Name_Country
	          FROM Country
	         WHERE ID_Country = $CountryID
	       ";
    $Res = Query(1,$SQL_Rep);
    $Name = F_OCIResult($Res,'NAME_COUNTRY');
    if (!empty($Name))
    {
      if (!empty($Address)) $Address .= $raz;
      $Address .= $Name;
    }
  }
  
  if (!empty($Mail_Index))
  {
    if (!empty($Address))  $Address .= $raz; 
    $Address .= $Mail_Index;
  }
  
  return $Address;
}
//*********************************************************************//
//		Определение табельного номера директора		       //
//*********************************************************************//
function Get_TNDirector ()
{
 GLOBAL $conn;
 
 $SQL_Rep = "SELECT TN
               FROM DOC.V_F_Pers,DOC.V_F_ShRas
              WHERE V_F_ShRas.PunktShr = 1 
                AND V_F_Pers.PriznAkt = 1
		AND V_F_Pers.KodZifr = V_F_ShRas.KodZifr
             ";

 $Res = Query(1,$SQL_Rep); 
 return  F_OCIResult( $Res,"TN" );
}
//**********************************************************************************//
//                      Определение имени пользователя по табельному номеру         //
//**********************************************************************************//

function Get_NameUser($TabNum)
{
  GLOBAL $conn;
  
  $SQL_Rep = "SELECT FIO
                FROM DOC.V_F_Pers
	       WHERE TN = '$TabNum'
	     ";
  $Res = Query(1,$SQL_Rep);
  return  F_OCIResult($Res,'FIO');
}


 ?>
