<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Выбор предприятия</title>
<link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/style_menu.css">
<script type="text/javascript" src="../../../../scripts/najax2.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></head>
<script>
var m_spisok = new Array(
['name_area','id_area','MenuArea.php',20],
['name_city','id_city','MenuCity.php',20],
['name_street','id_street','MenuStreet.php',20]
 );
 
function sendRequest(id)
{ 
  doptext="";
  sendReq( m_spisok[id.alt][2],"size=" + m_spisok[id.alt][3] + "&text=" + id.value + "&num=" + id.alt + doptext );
}


</script>
<body onLoad="Load()">
<table width="100%" height="100%" border="0" cellpadding="10" cellspacing="0" id="bgTable">
      <tr>
        <td style="padding:0px" height="10px">
           <? include "../../../../menu.php"; ?>		
		 </td>
      </tr>
      <tr class="tdInt">
        <td height="10px">
		   <div class="title"><a href="../../Enterprise/ViewEnterprise/ViewEnterprise.php?id_enterprise=<? echo $id_enterprise ?>">
		   Информация о предприятии</a> 
		   » <a href="../ViewAddress/ViewAddress.php?id_enterprise=<? echo $id_enterprise ?>">Контактная информация</a>
		   » Добавление адреса предприятия (филиала/подразделения)</div>
		   <div style="font-size:12px; text-align:right;"><? echo $Enterprise_Arr['NAME_SMALL'][0] ?></div>
		   <div class="title2">
		   <a class='page_doc' href="../../Employee/ViewEmployee/ViewEmployee.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Руководство</a>
           <a class="page_doc_current" href="../../Address/ViewAddress/ViewAddress.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Контактная информация</a>
           <a class="page_doc" href="../../Department/ViewDepartment/ViewDepartment.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Подразделения</a>
		   </div>
           <div>&nbsp;</div>
		</td>
      </tr>
      <tr class="tdInt" height="%">
	  <form name="form1" method="post" action="">
        <td valign="top">
		<table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="2">
          <tr class="headSubList">
            <td colspan="5" class="headBorder">Адрес</td>
          </tr>
            <tr class="rowList item">
              <td width="200">Тип</td>
              <td  ><select name="id_type_address" id="id_type_address">
                <? for($i=0;$i<sizeof($type_address_arr['ID_TYPE_ADDRESS']);$i++)
			  {
			  ?>
                <option value="<? echo $type_address_arr['ID_TYPE_ADDRESS'][$i] ?>"><? echo $type_address_arr['NAME_TYPE_ADDRESS'][$i] ?></option>
                <?
			  } 
		      ?>
              </select></td>
            </tr>
            <tr class="rowList item">
              <td nowrap >Страна</td>
              <td ><select name="id_country" id="id_country">
			  <? for($i=0;$i<sizeof($country_arr['ID_COUNTRY']);$i++)
			  {
			  ?>
                <option value="<? echo $country_arr['ID_COUNTRY'][$i] ?>"
				<? if($country_arr['NAME_COUNTRY'][$i] == 'Россия') echo 'selected'; ?>
				><? echo $country_arr['NAME_COUNTRY'][$i] ?></option>
		      <?
			  } 
		      ?>
              </select>              </td>
            </tr>
            <tr class="rowList item">
              <td nowrap >Административная единица</td>
              <td ><input name="name_area" type="text" class="textField menuField" id="name_area" size="40" maxlength="30">
              <input name="id_area" type="hidden" id="id_area"></td>
            </tr>
            <tr class="rowList item">
              <td >Город</td>
              <td><input name="name_city" type="text" class="textField menuField" id="name_city" size="40" maxlength="30">
                <input name="id_city" type="hidden" id="id_city"></td>
            </tr>

            <tr class="rowList item">
              <td >Улица</td>
              <td><input name="name_street" type="text" class="textField menuField" id="name_street" size="40" maxlength="30">
                <input name="id_street" type="hidden" id="id_street"></td>
            </tr>
            <tr class="rowList item">
              <td >Номер дома </td>
              <td><input name="home_num" type="text" class="textField" id="home_num" size="40" maxlength="30"></td>
            </tr>
            <tr class="rowList item">
              <td >Почтовый индекс </td>
              <td><input name="mail_index" type="text" class="textField" id="mail_index" size="40"></td>
            </tr>
			<tr class="rowList item">
			  <td nowrap >Уточнение по населен. пункту:</td>
			   <td ><input name="d_city" type="text" class="textField" id="d_city" size="100"></td>
		  </tr>
			<tr class="rowList item">
			  <td >а/я</td>
			   <td ><input name="abon_box" type="text" class="textField" id="abon_box" size="40"></td>
		  </tr>
			<tr class="rowList item">
				<td nowrap >Примечание</td>
				<td ><input name="note" type="text" class="textField" id="note" size="100" maxlength="500"></td>
				</tr>
			<tr class="rowList item">
				<td align="left" >Подразделение</td>
				<td align="left" ><select name="id_department" id="id_department">
					<option value=""></option>
					<? for($i=0;$i<sizeof($Department_Arr['ID_DEPARTMENT']);$i++)
			  {
			  ?>
					<option value="<? echo $Department_Arr['ID_DEPARTMENT'][$i] ?>"
				><? echo $Department_Arr['NAME_DEPARTMENT'][$i] ?></option>
					<?
			  } 
		      ?>
				</select></td>
			</tr>
			<tr class="rowList item">
              <td colspan="2" align="center" ><span style=" margin-top:10px;">
              <input name="act" type="hidden" id="act" value="add">
              <input name="id_enterprise" type="hidden" id="id_enterprise" value="<? echo $id_enterprise ?>">
              <input name="Button" type="button" class="blueButton" onClick="form1.submit();" value="Сохранить">
			  <input name="Button" type="button" class="blueButton" value="Отмена" onClick="history.go(-1)">
              </span></td>
            </tr>
          <tr>
            <td colspan="2" class="endList"></td>
          </tr>
        </table>
	</td>
        </form>
      </tr>
</table>
</body>
</html>
