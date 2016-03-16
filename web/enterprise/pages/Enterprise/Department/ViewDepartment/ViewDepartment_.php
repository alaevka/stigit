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
           <div class="title"> <a href="../../Enterprise/ViewEnterprise/ViewEnterprise.php?id_enterprise=<? echo $id_enterprise ?>">Информация о предприятии</a> » Подразделения </div>
		   <div style="font-size:12px; text-align:right;"><? echo $Enterprise_Arr['NAME_SMALL'][0] ?></div>
		   <div class="title2">
		   <a class='page_doc' href="../../Employee/ViewEmployee/ViewEmployee.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Руководство</a>
           <a class="page_doc" href="../../Address/ViewAddress/ViewAddress.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Контактная информация</a>
           <a class="page_doc_current" href="../../Department/ViewDepartment/ViewDepartment.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Подразделения</a>
		   </div>
           <div>&nbsp;</div>
		   <div align="left"><a class="link_upd" href="../AddDepartment/AddDepartment.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">
		     [ добавить подразделение ]</a>
	      </div></td>
      </tr>
      <tr valign="top" class="tdInt" height="%">
         <td ><div id="pfc_online" style="border: #072a66 1px solid;">
	    <table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="4" style="border-width:0px;">
          <tr class="headSubList" style="position: relative;top: expression(this.parentElement.parentElement.parentElement.scrollTop);" >
            <td width="24%" align="center" class="headBorder">Подразделение</td>
            <td width="33%" align="center" class="headBorder">Факс</td>
            <td width="19%" align="center" class="headBorder">Телефон</td>
            <td width="20%" align="center" class="headBorder">E-mail</td>
            <td width="4%" align="center" class="headBorder">&nbsp;</td>
          </tr>
          <? 
		  for ( $i = 0; $i < sizeof( $Enterprise_department['ID_DEPARTMENT']); $i++ ) 
		  { 
		  ?>
		  
           <tr class='rowList item' onMouseOver="this.style.background = '#f0f5fa';" onMouseOut="this.style.background = '#e4eaf2';">
            <td><? echo $Enterprise_department['NAME_DEPARTMENT'][$i] ?></td>
            <td><? echo $Enterprise_department['FAX'][$i] ?></td>
            <td nowrap><? echo $Enterprise_department['PHONE'][$i] ?></td>
            <td nowrap><? echo $Enterprise_department['EMAIL'][$i] ?></td>
            <td><a href="../UpdDepartment/UpdDepartment.php?id_department=<? echo $Enterprise_department['ID_DEPARTMENT'][$i] ?>">ред.</a></td>
          </tr>
		  
          <? } ?>
          <tr>
            <td colspan="1" class="endList"></td>
            <td class="endList"></td>
            <td class="endList"></td>
            <td class="endList"></td>
            <td class="endList"></td>
          </tr>
        </table>
	  </div></td>
      </tr>
    </table>
</body>
</html>
