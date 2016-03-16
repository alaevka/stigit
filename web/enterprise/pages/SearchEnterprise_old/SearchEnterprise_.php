<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Поиск предприятия</title>
<link rel="stylesheet" type="text/css" href="../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../styles/style_menu.css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></head>

<script type="text/javascript" src="../../scripts/najax2.js"></script>
<script>
var m_spisok = new Array(
['FastSearch','FastSearch_ID','../../Include/ShowEnterprise.php',20],
['country','id_country','../../Include/showCountry.php',20],
['city','id_city','../../Include/showCity.php',20],
['street','id_street','../../Include/showStreet.php',20]
);

function sendRequest(id)
{ 
  doptext="";  
  switch(m_spisok[id.alt][0])
    {
       //case 'LetDAT': { doptext= "&LetNUM="+ document.getElementById('LetNUM').value+"&type=&Kodpodr_ALL=<? //echo $Kodpodr_ALL ?>&ID_Kind_Document="+document.getElementById('KindDocumentID').value;} break;
     }
  sendReq( m_spisok[id.alt][2],"size=" + m_spisok[id.alt][3] + "&text=" + id.value + "&num=" + id.alt + doptext );
}

function tbody_search()
{
  if(document.getElementById('tbody_search').style.display == 'none') {
    document.getElementById('tbody_search').style.display = 'inline';
	document.getElementById('search').value = 1;
  }
  else {
    document.getElementById('tbody_search').style.display = 'none';
	document.getElementById('search').value = 0;
  }
}

</script>
<body onLoad="Load();">
<table width="100%" height="100%" border="0" cellpadding="10" cellspacing="0" id="bgTable">
      <tr>
        <td style="padding:0px" height="10px">
           <? include "../../menu.php"; ?>		</td>
      </tr>
      <tr class="tdInt">
        <td height="10px">
           <p><span class="title">Поиск </span></p>	 </td>
      </tr>
      <tr class="tdInt">
         <td height="10px">
	 <form  action="" method="get" name="SearchForm" target="_parent" id="SearchForm" style="padding:0px; margin:0px;">
            <table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="2">
              <tr class="headSubList">
                <td colspan="2" class="headBorder">Параметры поиска</td>
              </tr>
              <tr class="rowList item">
                <td width="200">Наименование</td>
                <td ><input name="FastSearch" type="text" class="textFieldSelect" id="FastSearch" value="<? if ( !empty($FastSearch) ) echo htmlspecialchars($FastSearch) ?>" size="80">
                  <input type="hidden" name="FastSearch_ID" id="FastSearch_ID">
				<a href="#" onMouseUp="tbody_search()" class="link_upd">[Расширенный поиск]
				<input name="search" type="hidden" id="search" value="<? echo empty($search)?0:$search; ?>">
				</a>				</td>
              </tr>
			  <tbody id="tbody_search" style="display:<? if( !empty($search) && $search!=0 ) echo 'inline'; else echo 'none';?>;">
              <tr class="rowList item">
                  <td>Обращение</td>
                <td  ><input name="NameCall" type="text" class="textField" id="NameCall" value="<? if ( !empty($NameCall) ) echo htmlspecialchars($NameCall) ?>" size="40" maxlength="30"></td>
              </tr>
		 <tr class="rowList item">
                  <td >Краткое наименование</td>
                <td ><input name="NameSmall" type="text" class="textField" id="NameSmall" value="<? if ( !empty($NameSmall) ) echo htmlspecialchars($NameSmall) ?>" size="40" maxlength="30"></td>
                </tr>
		 <tr class="rowList item">
                  <td >Полное наименование</td>
                <td><input name="Name" type="text" class="textField" id="Name" value="<? if ( !empty($Name) ) echo htmlspecialchars($Name) ?>" size="80" maxlength="200"></td>
                </tr>
		 <tr class="rowList item">
           <td >Фамилия сотрудника </td>
		   <td><input name="Fam" type="text" class="textField" id="Fam" value="<? if ( !empty($Fam) ) echo htmlspecialchars($Fam) ?>" size="40" maxlength="30"></td>
		   </tr>
		 <tr class="rowList item">
		<td >Страна</td>
              <td><input name="country" type="text"  class="textFieldSelect" id="country" value="<? if ( !empty($country) ) echo htmlspecialchars($country) ?>" size="80" maxlength="200">
                <input type="hidden" name="id_country" id="id_country" value="<? if ( !empty($id_country) ) echo htmlspecialchars($id_country) ?>"></td>
                </tr>
		 <tr class="rowList item">
           <td >Город</td>
		   <td><input name="city" type="text" class="textFieldSelect" id="city" value="<? if ( !empty($city) ) echo htmlspecialchars($city) ?>" size="80" maxlength="200">
               <input type="hidden" name="id_city" id="id_city" value="<? if ( !empty($id_city) ) echo htmlspecialchars($id_city) ?>"></td>
		   </tr>
		 <tr class="rowList item">
           <td >Улица</td>
		   <td><input name="street" type="text" class="textFieldSelect" id="street" value="<? if ( !empty($street) ) echo htmlspecialchars($street) ?>" size="80" maxlength="200">
		   <input type="hidden" name="id_street" id="id_street" value="<? if ( !empty($id_street) ) echo htmlspecialchars($id_street) ?>" ></td>
		   </tr>
		 <tr class="rowList item">
				<td>E-mail</td>
		 		<td  ><input name="email" type="text" class="textField" id="email" value="<? if ( !empty($email) ) echo htmlspecialchars($email) ?>" size="40" maxlength="70"></td>
	 	</tr>
			  </tbody>
		 <tr class="rowList item">
		        <td colspan="2" align="center"><input name="Submit" type="submit" class="blueButton" value="Найти"></td>
              </tr>
		  <tr>
              <td colspan="2" class="endList"></td>
            </tr>
        </table>
	     <div style=" font-size:12px; padding-top:10px;">Найдено предприятий: <b><? if(sizeof( $EnterpriseArr['ID_ENTERPRISE'])==100) echo 'более '; echo sizeof( $EnterpriseArr['ID_ENTERPRISE'] ) ?></b></div>
	 </form>	 </td>
      </tr>
      <tr valign="top" class="tdInt"  height="%">
	  <td><div id="pfc_online" style="border: #072a66 1px solid;">
	    <table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="4" style="border-width:0px;">
          <tr class="headSubList" style="position: relative;top: expression(this.parentElement.parentElement.parentElement.scrollTop);" >
            <td width="13%" align="center" class="headBorder">Обращение</td>
            <td width="9%" align="center" class="headBorder">Краткое наименование</td>
            <td width="25%" align="center" class="headBorder">Полное наименование</td>
            <td width="24%" align="center" class="headBorder">Адрес</td>
			<?
			if(!empty($Fam)){
			?>
          	<td width="29%" align="center" class="headBorder">Сотрудники</td>
			<?
			}
			?>
          </tr>
          <? if (!empty($EnterpriseArr))
		  		for ( $i = 0; $i < sizeof( $EnterpriseArr['ID_ENTERPRISE']); $i++ ) { ?>
          <tr class='rowList item' style="cursor:pointer" onMouseOver="this.style.background = '#f0f5fa';" onMouseOut="this.style.background = '#e4eaf2';"
		   <? if(!$EnterpriseArr['ACTION'][$i]) echo'style="background-color:#cccccc"'; ?>
		  >
		  <a href="../Enterprise/Enterprise/ViewEnterprise/ViewEnterprise.php?id_enterprise=<? echo $EnterpriseArr['ID_ENTERPRISE'][$i] ?>">
            <td valign="top"><? echo $EnterpriseArr['NAME_CALL'][$i] ?></td>
            <td valign="top"><? echo $EnterpriseArr['NAME_SMALL'][$i] ?></td>
            <td valign="top"><? echo $EnterpriseArr['NAME_ENTERPRISE'][$i] ?></td>
              	<td valign="top">
                  	<?
					if(!empty($EnterpriseArr['ENTERPRISE_ADDRESS'][$i]))
				   	for ( $j = 0; $j < sizeof($EnterpriseArr['ENTERPRISE_ADDRESS'][$i]['ID_ENTERPRISE_ADDRESS']); $j++ ) { 
						if($j>0)echo '<hr style="height:1px; color:#ffffff;">';
					   	if(!empty($EnterpriseArr['ENTERPRISE_ADDRESS'][$i]['ID_ENTERPRISE_ADDRESS'])) 
					   	echo '<u>'.$EnterpriseArr['ENTERPRISE_ADDRESS'][$i]['NAME_TYPE_ADDRESS'][$j].'</u>: '.GetAddressEnterprise($EnterpriseArr['ENTERPRISE_ADDRESS'][$i]['ID_ENTERPRISE_ADDRESS'][$j],0); ; 
                    } 
					?>				</td>
				<?
				if(!empty($Fam)){
				?>
		  		<td valign="top">
				<?
					for ( $j = 0; $j < sizeof($EnterpriseArr['ENTERPRISE_EMPLOYEES'][$i]['ID_ENTERPRISE_EMPLOYEE']); $j++ ) { 
						echo $EnterpriseArr['ENTERPRISE_EMPLOYEES'][$i]['FAM'][$j].' '.substr($EnterpriseArr['ENTERPRISE_EMPLOYEES'][$i]['IMJ'][$j],0,1).'.'
						.substr($EnterpriseArr['ENTERPRISE_EMPLOYEES'][$i]['OTCH'][$j],0,1).'. - '.$EnterpriseArr['ENTERPRISE_EMPLOYEES'][$i]['POST'][$j].'<br>';
						
					}
				?>
				</td>
				<?
				}
				?>
		  </a>          </tr>
          <? } ?>
          <tr>
            <td colspan="5" class="endList"></td>
          </tr>
        </table>
	  </div></td>
      </tr>
    </table>
</body>
</html>
