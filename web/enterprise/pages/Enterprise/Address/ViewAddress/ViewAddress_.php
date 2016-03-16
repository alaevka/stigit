<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Выбор предприятия</title>
<link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/style_menu.css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</head>
<body>
<table width="100%" height="100%" border="0" cellpadding="10" cellspacing="0">
      <tr>
        <td style="padding:0px" height="10px">
           <? include "../../../../menu.php"; ?></td>
      </tr>
      <tr class="tdInt">
        <td height="10px">
           <div class="title"><a href="../../Enterprise/ViewEnterprise/ViewEnterprise.php?id_enterprise=<? echo $id_enterprise ?>">Информация о предприятии</a> » Контактная информация </div>
		   <div style="font-size:12px; text-align:right;"><? echo $Enterprise_Arr['NAME_SMALL'][0] ?></div>
		   <div class="title2">
		   <a class='page_doc' href="../../Employee/ViewEmployee/ViewEmployee.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Руководство</a>
           <a class="page_doc_current" href="../../Address/ViewAddress/ViewAddress.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Контактная информация</a>
           <a class="page_doc" href="../../Department/ViewDepartment/ViewDepartment.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Подразделения</a>
		   </div>
           <div>&nbsp;</div>
		   <div align="left"><a class="link_upd" href="../AddAddress/AddAddress.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">
		     [ добавить адрес ]</a>
	      </div></td>
      </tr>
      <tr valign="top" class="tdInt" height="%">
         <td ><div id="pfc_online" style="border: #072a66 1px solid;">
	    <table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="4" style="border-width:0px;">
          <tr class="headSubList" style="position: relative;top: expression(this.parentElement.parentElement.parentElement.scrollTop);" >
            <td width="4%" align="center" class="headBorder">Тип  </td>
            <td width="16%" align="center" class="headBorder">Статус</td>
            <td width="16%" align="center" class="headBorder">Адм. единица </td>
            <td width="13%" align="center" class="headBorder">Населённый пункт </td>

            <td width="7%" align="center" class="headBorder">Улица</td>
            <td width="4%" align="center" class="headBorder">Дом</td>
            <td width="4%" align="center" nowrap class="headBorder">а/я</td>
            <td width="15%" align="center" class="headBorder">Почтовый индекс </td>
            <td width="6%" align="center" class="headBorder">Страна</td>
			<td width="28%" align="center" class="headBorder">Уточнение</td>
			<td width="28%" align="center" class="headBorder">Примечание</td>
            <td width="1%" align="center" class="headBorder">Подразделение</td>
            <td width="1%" align="center" class="headBorder">&nbsp;</td>
          </tr>
 	<? 
		  for ( $i = 0; $i < sizeof( $Enterprise_address['ID_ENTERPRISE_ADDRESS']); $i++ ) 
		  { 
	?>
          <tr class='rowList item' onMouseOver="this.style.background = '#f0f5fa';" onMouseOut="this.style.background = '#e4eaf2';"
		  <? if(empty($Enterprise_address['ACTION_ADDRESS'][$i])) echo'style="background-color:#cccccc"'; ?>>
            <td><? echo $Enterprise_address['NAME_TYPE_ADDRESS'][$i] ?></td>
              <td align="center"><? echo empty($Enterprise_address['ACTION_ADDRESS'][$i])?'не действующий':'действующий'; ?></td>
              <td><? echo $Enterprise_address['TYPE_AREA'][$i]  ?>&nbsp;<? echo $Enterprise_address['NAME_AREA'][$i]  ?></td>
              <td nowrap><? echo $Enterprise_address['TYPE_CITY'][$i]  ?>.&nbsp;<? echo $Enterprise_address['NAME_CITY'][$i]  ?></td>
              <td nowrap><? echo $Enterprise_address['NAME_STREET'][$i]  ?></td>
              <td><? echo $Enterprise_address['HOME_NUM'][$i]  ?></td>
              <td align="right"><? echo $Enterprise_address['ABON_BOX'][$i]  ?></td>
              <td align="right"><? echo $Enterprise_address['MAIL_INDEX_ADDRESS'][$i]  ?></td>
              <td><? echo $Enterprise_address['NAME_COUNTRY'][$i]  ?></td>
			  <td><? echo $Enterprise_address['D_CITY'][$i]  ?></td>
			  <td><? echo $Enterprise_address['NOTE'][$i] ?></td>
		      <td><? echo $Enterprise_address['NAME_DEPARTMENT'][$i] ?></td>
		      <td><a href="../UpdAddress/UpdAddress.php?id_enterprise_address=<? echo $Enterprise_address['ID_ENTERPRISE_ADDRESS'][$i] ?>">ред.</a></td>
		  </tr>
          <? } ?>
          <tr>
            <td colspan="16" class="endList"></td>
          </tr>
        </table>
	  </div></td>
      </tr>
    </table>
</body>
</html>
