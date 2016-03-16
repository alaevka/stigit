<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Выбор предприятия</title>
<link rel="stylesheet" type="text/css" href="../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../styles/style_menu.css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></head>
<script>
function tbody_search()
{
  if(document.getElementById('tbody_search').style.display == 'none')
    document.getElementById('tbody_search').style.display = 'inline';
  else
    document.getElementById('tbody_search').style.display = 'none';
}

function SelectEnterprise(id_enterprise,name_small)
{
  //alert(5);
  //alert(id_enterprise + ' * ' + name_small);
  window.opener.document.getElementById("<? echo $IdSend ?>").value = id_enterprise;
  window.opener.document.getElementById('<? echo $NameSend ?>').value = name_small;
  window.close();
}
</script>
<body>
<table width="100%" height="100%" border="0" cellpadding="10" cellspacing="0">
      <tr class="tdInt" height="10px">
        <td>
           <p><span class="title">Выбор предприятия</span></p>	 </td>
      </tr>
      <tr class="tdInt" height="10px">
         <td>
	 <form  method="get" name="SearchForm" target="_parent" id="SearchForm" style="padding:0px; margin:0px;">
            <table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="2">
              <tr class="headSubList">
                <td colspan="5" class="headBorder">Параметры поиска</td>
              </tr>
              <tr class="rowList item">
                <td width="200"><strong>Быстрый поиск </strong></td>
                <td ><input name="FastSearch" type="text" class="textField" id="FastSearch" value="<? if ( !empty($FastSearch) ) echo htmlspecialchars($FastSearch) ?>" size="40" maxlength="30">
				<a href="#" onMouseUp="tbody_search()" class="link_upd"><b>[Расширенный поиск]</b></a>
                <input name="IdSend" type="hidden" value="<? echo $IdSend ?>">
                <input name="NameSend" type="hidden" value="<? echo $NameSend ?>"></td>
              </tr>
			  <tbody id="tbody_search" style="display:<? if( !empty($Fam) || !empty($NameCall) || !empty($NameSmall) || !empty($Name) || !empty($Address) ) echo 'inline'; else echo 'none';?>;">
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
		<td >Адрес</td>
                <td><input name="Address" type="text" class="textField" id="Address" value="<? if ( !empty($Address) ) echo htmlspecialchars($Address) ?>" size="80" maxlength="200"></td>
                </tr>
			  </tbody>
			  		 <tr class="rowList item">
		<td colspan="2" align="center" ><input name="Submit" type="submit" class="blueButton" value="Найти"></td>
                </tr>
		  <tr>
              <td colspan="2" class="endList"></td>
            </tr>
        </table>
		<div style=" font-size:12px; padding-top:10px;">Найдено предприятий: <b><? if(sizeof($EnterpriseID_Arr)==100) echo 'более '; echo sizeof($EnterpriseID_Arr) ?></b></div>
           </form>	 </td>
      </tr>
      <tr valign="top" class="tdInt"  height="%">
	  <td><div id="pfc_online" style="border: #072a66 1px solid;">
	    <table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="4" style="border-width:0px;">
          <tr class="headSubList" style="position: relative;top: expression(this.parentElement.parentElement.parentElement.scrollTop);" >
            <td width="19%" align="center" class="headBorder">Обращение</td>
            <td width="14%" align="center" class="headBorder">Краткое наименование</td>
            <td width="34%" align="center" class="headBorder">Полное наименование</td>
            <td width="33%" align="center" class="headBorder">Адрес</td>
          </tr>
          <? if (!empty($EnterpriseID_Arr))for ( $i = 0; $i < sizeof( $EnterpriseID_Arr); $i++ ) { ?>
          <tr class='rowList item' style="cursor:pointer" onMouseOver="this.style.background = '#f0f5fa';" onMouseOut="this.style.background = '#e4eaf2';"
		  onClick="SelectEnterprise(<? echo $EnterpriseID_Arr[$i] ?>,'<? echo htmlspecialchars($EnterpriseNameSmall_Arr[$i]) ?>')">
            <td width="19%" valign="top"><? echo $EnterpriseNameCall_Arr[$i] ?></td>
            <td  width="14%" valign="top"><? echo $EnterpriseNameSmall_Arr[$i] ?></td>
            <td width="34%" valign="top"><? echo $EnterpriseName_Arr[$i] ?></td>
              <td valign="top">
                  <?
				   if(!empty($EnterpriseAddressID[$EnterpriseID_Arr[$i]]))
				     for ( $j = 0; $j < sizeof( $EnterpriseAddressID[$EnterpriseID_Arr[$i]]); $j++ ) 
				     { 
					   if($j>0)echo '<hr style="height:1px; color:#ffffff;">';
					   if (!empty($EnterpriseAddressIDText[$EnterpriseID_Arr[$i]][$j])) echo '<u>',$EnterpriseAddressType[$EnterpriseID_Arr[$i]][$j],'</u>: ',$EnterpriseAddressIDText[$EnterpriseID_Arr[$i]][$j] ; 
                     } 
				   ?>		   </td>
          </tr>
          <? } ?>
          <tr>
            <td colspan="4" class="endList"></td>
          </tr>
        </table>
	  </div></td>
      </tr>
    </table>
</body>
</html>
