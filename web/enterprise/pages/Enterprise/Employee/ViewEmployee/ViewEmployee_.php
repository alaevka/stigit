<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Выбор предприятия</title>
<link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/style_menu.css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></head>
<script>
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
<body>
<table width="100%" height="100%" border="0" cellpadding="10" cellspacing="0">
      <tr>
        <td style="padding:0px" height="10px">
           <? include "../../../../menu.php"; ?></td>
      </tr>
      <tr class="tdInt">
        <td height="10px">
           <div class="title"> <a href="../../Enterprise/ViewEnterprise/ViewEnterprise.php?id_enterprise=<? echo $id_enterprise ?>">Информация о предприятии</a> » Руководство</div>
		   <div style="font-size:12px; text-align:right;"><? echo $Enterprise_Arr['NAME_SMALL'][0] ?></div>
		   <div class="title2">
		   <a class='page_doc_current' href="../../Employee/ViewEmployee/ViewEmployee.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Руководство</a>
           <a class="page_doc" href="../../Address/ViewAddress/ViewAddress.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Контактная информация</a>
           <a class="page_doc" href="../../Department/ViewDepartment/ViewDepartment.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Подразделения</a>
		   </div>
           <div>&nbsp;</div>
		   <div align="left" style="float:left"><a class="link_upd" href="../AddEmployee/AddEmployee.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">
		     [ добавить сотрудника ]</a>
	      </div>
		  		  <div style="float:right; font-size:12px; background-color:#f0f5fa; border:#000000 solid 1px; color:#999999; width:40px">&nbsp;уволенные&nbsp;</div>
		</td>
      </tr>
	  <tr class="tdInt">
         <td height="10px">
	 <form  action="" method="post" name="SearchForm" target="_parent" id="SearchForm" style="padding:0px; margin:0px;">
            <table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="2">
              <tr class="headSubList">
                <td colspan="2" class="headBorder">Параметры поиска</td>
              </tr>
              <tr class="rowList item">
                <td width="200">Фамилия</td>
                <td >
                  <input name="Fam" type="text" class="textField" id="Fam" value="<? if ( !empty($Fam) ) echo htmlspecialchars($Fam) ?>" size="40" maxlength="30">
                  <a href="#" onMouseUp="tbody_search()" class="link_upd">[Расширенный поиск]</a>
				<input name="search" type="hidden" id="search" value="<? echo empty($search)?0:$search; ?>">				</td>
              </tr>
			  <tbody id="tbody_search" style="display:<? if( !empty($search) && $search!=0 ) echo 'inline'; else echo 'none';?>;">
              <tr class="rowList item">
                  <td>Подразделение</td>
                  <td  ><input name="NameDepartment" type="text" class="textField" id="NameDepartment" value="<? if ( !empty($NameDepartment) ) echo htmlspecialchars($NameDepartment) ?>" size="40" maxlength="30"></td>
              </tr>
		 <tr class="rowList item">
                  <td >Должность</td>
                <td ><input name="Post" type="text" class="textField" id="Post" value="<? if ( !empty($Post) ) echo htmlspecialchars($Post) ?>" size="40" maxlength="30"></td>
                </tr>
			  </tbody>
		 <tr class="rowList item">
		        <td colspan="2" align="center"><input name="Submit" type="submit" class="blueButton" value="Найти"></td>
              </tr>
		  <tr>
              <td colspan="2" class="endList"></td>
            </tr>
        </table>
	     <div style=" font-size:12px; padding-top:10px;">Найдено сотрудников: <b><? if(sizeof( $Enterprise_employee['ID_ENTERPRISE_EMPLOYEE'] )==100) echo 'более '; echo sizeof( $Enterprise_employee['ID_ENTERPRISE_EMPLOYEE'] ) ?></b></div>
	 </form>	 </td>
      </tr>
      <tr valign="top" class="tdInt" height="%">
         <td ><div id="pfc_online" style="border: #072a66 1px solid;">
	    <table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="4" style="border-width:0px;">
          <tr class="headSubList" style="position: relative;top: expression(this.parentElement.parentElement.parentElement.scrollTop);" >
            <td width="7%" align="center" class="headBorder">Фамилия</td>
            <td width="3%" align="center" class="headBorder">Имя</td>
            <td width="7%" align="center" class="headBorder">Отчество</td>

            <td width="37%" align="center" class="headBorder">Должность</td>
            <td width="7%" align="center" class="headBorder">Телефон стационарный </td>
			<td width="10%" align="center" class="headBorder">Телефон мобильный</td>
			<? if($_SESSION["ruk_any_podr"] || $_SESSION["acc_kanc"]) { ?>
			<td width="10%" align="center" class="headBorder">День рождения </td>
			<? } ?>
            <td width="7%" align="center" nowrap class="headBorder">Е-mail</td>
			<td width="5%" align="center" nowrap class="headBorder">Факс</td>
            <td width="4%" align="center" class="headBorder">Подр.</td>
            <td width="13%" align="center" class="headBorder">&nbsp;</td>
          </tr>
          <? 
		  for ( $i = 0; $i < sizeof( $Enterprise_employee['ID_ENTERPRISE_EMPLOYEE']); $i++ ) 
		  { 
		  ?>
           <tr class='rowList item' onMouseOver="this.style.background = '#f0f5fa';" onMouseOut="this.style.background = '#e4eaf2';"
		   <? if(!$Enterprise_employee['ACTION'][$i]) echo'style="color:#999999"'; ?>
		   >
            <td><? echo $Enterprise_employee['FAM'][$i] ?></td>
              <td><? echo $Enterprise_employee['IMJ'][$i] ?></td>
              <td><? echo $Enterprise_employee['OTCH'][$i] ?></td>
              <td><? echo $Enterprise_employee['POST'][$i] ?></td>
			  <td><? echo $Enterprise_employee['PHONE'][$i] ?></td>
			  <td align="center" nowrap><? 
			  				if($_SESSION["ruk_any_podr"] || $_SESSION["acc_kanc"]) 
			  					echo /*!empty($Enterprise_employee['PHONE'][$i])&&*/!empty($Enterprise_employee['MOBILE_PHONE'][$i]) ? $Enterprise_employee['MOBILE_PHONE'][$i] : '' 
						?>				</td>
			  <? if($_SESSION["ruk_any_podr"] || $_SESSION["acc_kanc"]) { ?>
			     <td nowrap>&nbsp;<? echo $Enterprise_employee['A'][$i] ?></td>
			  <? } ?>
              <td><? echo $Enterprise_employee['EMAIL'][$i] ?></td>
			  <td nowrap><? echo $Enterprise_employee['PHONE4'][$i] ?></td>
              <td><? echo $Enterprise_employee['NAME_DEPARTMENT'][$i] ?></td>
              <td> <a href="../UpdEmployee/UpdEmployee.php?id_enterprise_employee=<? echo $Enterprise_employee['ID_ENTERPRISE_EMPLOYEE'][$i] ?>">ред.</a></td>
          </tr>
		  
          <? } ?>
          <tr>
            <td colspan="19" class="endList"></td>
          </tr>
        </table>
	  </div></td>
      </tr>
    </table>
</body>
</html>
