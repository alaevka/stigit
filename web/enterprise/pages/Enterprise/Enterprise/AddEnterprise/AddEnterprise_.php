<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Выбор предприятия</title>
<link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/style_menu.css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<script type="text/javascript" src="../../../../scripts/najax2.js"></script>
<script>
var m_spisok = new Array(
	['name_call','name_call_id','../../../../include/showEnterprise.php',20],
	['name_ent_last_incarnation','id_ent_last_incarnation','../../../../include/showEnterprise_2.php',20]
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
</script>

<style type="text/css">
<!--
.style2 {font-size: 10}
-->
</style>
</head>
<body onLoad="Load();">
<table width="100%" height="100%" border="0" cellpadding="10" cellspacing="0"  id="bgTable">
      <tr height="10px">
        <td style="padding:0px">
           <? include "../../../../menu.php"; ?>		
		 </td>
      </tr>
      <tr class="tdInt" height="10px">
        <td height="36">
        <p><span class="title">Добавление предприятия (филиала/подразделения)</span></p>	 </td>
      </tr>
      <tr class="tdInt" height="%">
	  <form name="form1" method="post" action="">
        <td valign="top">
		<table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="2">
          <tr class="headSubList">
            <td colspan="5" class="headBorder">Общая информация </td>
          </tr>
            <tr class="rowList item">
              <td>Форма собственности </td>
              <td  >
			  <select name="id_department" id="id_department" onChange="form1.name_enterprise.value = this.options[this.selectedIndex].value;form1.name_call.value = this.options[this.selectedIndex].text;">
                  <option value=""></option>
                  <? for($i=0;$i<sizeof($form_owner['ID_FORM_OWNER']);$i++)
			  {
			  ?>
                  <option value="<? echo $form_owner['NAME_FORM'][$i] ?>"><? echo $form_owner['NAME_FORM_SMALL'][$i] ?></option>
                  <?
			  } 
		      ?>
                </select>              </td>
            </tr>
            <tr class="rowList item">
              <td width="200">Обращение</td>
              <td nowrap><input name="name_call" type="text" class="textFieldSelect" id="name_call" size="100">
                <input type="hidden" name="name_call_id" id="name_call_id">
                <span style=" font-size:9px">Пример: ООО "Наутилус"</span></td>
            </tr>
            <tr class="rowList item">
              <td nowrap >Краткое наименование</td>
              <td nowrap ><input name="name_small" type="text" class="textField" id="name_small" size="80">
              <span style=" font-size:9px">Пример: Наутилус</span></td>
            </tr>
            <tr class="rowList item">
              <td >Полное наименование</td>
              <td nowrap><textarea name="name_enterprise" cols="100" class="textField" id="name_enterprise"></textarea>
                
                <input name="Submit2" type="button" class="blueButton" value="Сформировать" 
				onClick="form1.name_enterprise.value = form1.name_call.value + ' ' + form1.name_small.value"><br>
<span style=" font-size:9px">Пример: Общество с ограниченной ответственностью "Наутилус"</span>              </td>
            </tr>

            <tr class="rowList item">
              <td >Телефон</td>
              <td><input name="phone" type="text" class="textField" id="phone" size="80"></td>
            </tr>
            <tr class="rowList item">
              <td >Факс</td>
              <td><input name="fax" type="text" class="textField" id="fax" size="80"></td>
            </tr>
            <tr class="rowList item">
              <td >Сайт</td>
              <td><input name="url" type="text" class="textField" id="url" size="80"></td>
            </tr>
            <tr class="rowList item">
              <td >E-mail</td>
              <td><input name="email" type="text" class="textField" id="email" size="80"></td>
            </tr>
            <tr class="rowList item">
              <td >Подчиняется</td>
              <td><input name="name_enterprise_parent" type="text" class="textField" id="name_enterprise_parent" size="80" readonly>
                <input name="id_enterprise_parent" type="hidden" id="id_enterprise_parent">
                  <a href="JavaScript: 
				  document.getElementById('name_enterprise_parent').value='';
				  document.getElementById('id_enterprise_parent').value='';
				  win = window.open( '../../../SelectEnterprise/SelectEnterprise.php?IdSend=id_enterprise_parent&NameSend=name_enterprise_parent','','height=600,width=850,toolbar=0,top = 50,left = 100,location=0,directoties=0,status=0,menubar=0,resizable=1,scrollbars=1'); win.focus();" class="link_upd">выбрать</a></td>
            </tr>
            <tr class="rowList item">
              <td >Позывной</td>
              <td><input name="callsign" type="text" class="textField" id="callsign" size="80"></td>
            </tr>
			<tr class="rowList item">
				<td ><p>Примечание</p>					</td>
				<td><input name="note" type="text" class="textField" id="note" size="80"></td>
				</tr>
			<tr class="rowList item">
				<td>Преобразовано из </td>
				<td nowrap><input name="name_ent_last_incarnation" type="text" class="textFieldSelect" id="name_ent_last_incarnation" size="100">
						<input type="hidden" name="id_ent_last_incarnation" id="id_ent_last_incarnation"></td>
				</tr>
			<tr class="rowList item">
              <td colspan="2" align="center" ><span style=" margin-top:10px;">
                <input name="act" type="hidden" id="act" value="add">
                <input name="Submit" type="submit" class="blueButton" value="Создать">
              </span></td>
            </tr>
          <tr>
            <td colspan="2" class="endList"></td>
          </tr>
        </table>
		<div align="center" style=" margin-top:10px;"></div>		</td>
        </form>
      </tr>
</table>
</body>
</html>
