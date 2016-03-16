<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Выбор предприятия</title>
<link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/style_menu.css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></head>
<script type="text/javascript" src="../../../../scripts/najax2.js"></script>
<script>
var m_spisok = new Array(
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
<body onLoad="Load();">
<table width="100%" height="100%" border="0" cellpadding="10" cellspacing="0"  id="bgTable">
      <tr height="10px">
        <td style="padding:0px">
           <? include "../../../../menu.php"; ?>		</td>
      </tr>
      <tr class="tdInt" height="10px">
        <td height="36">
        <div class="title"><a href="../ViewEnterprise/ViewEnterprise.php?id_enterprise=<? echo $id_enterprise ?>">Информация о предприятии</a> » Редактирование</div>
		<div style="font-size:12px; text-align:right;"><? echo $Enterprise_Arr['NAME_SMALL'][0] ?></div>
		</td>
      </tr>
      <tr class="tdInt" height="%">
	  <form name="form1" method="post" action="">
        <td valign="top">
		<table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="2">
          <tr class="headSubList">
            <td colspan="5" class="headBorder">Общая информация</td>
          </tr>
            <tr class="rowList item">
              <td width="200">Обращение</td>
              <td><input name="name_call" type="text" class="textField" id="name_call" value="<? echoML( $Enterprise_Arr["NAME_CALL"][0] ) ?>" size="100">              </td>
            </tr>
            <tr class="rowList item">
              <td nowrap >Краткое наименование</td>
              <td ><input name="name_small" type="text" class="textField" id="name_small" value="<? echoML( $Enterprise_Arr["NAME_SMALL"][0] )?>" size="80"></td>
            </tr>
            <tr class="rowList item">
              <td >Полное наименование</td>
              <td><textarea name="name_enterprise" cols="100" class="textField" id="name_enterprise"><? echoML( $Enterprise_Arr["NAME_ENTERPRISE"][0] ) ?></textarea></td>
            </tr>

            <tr class="rowList item">
              <td >Телефон</td>
              <td><input name="phone" type="text" class="textField" id="phone" value="<? echoML( $Enterprise_Arr["PHONE"][0] ) ?>" size="80"></td>
            </tr>
            <tr class="rowList item">
              <td >Факс</td>
              <td><input name="fax" type="text" class="textField" id="fax" value="<? echoML( $Enterprise_Arr["FAX"][0] ) ?>" size="80"></td>
            </tr>
            <tr class="rowList item">
              <td >Сайт</td>
              <td><input name="url" type="text" class="textField" id="url" value="<? echoML( $Enterprise_Arr["URL"][0] ) ?>" size="80"></td>
            </tr>
            <tr class="rowList item">
              <td >E-mail</td>
              <td><input name="email" type="text" class="textField" id="email" value="<? echoML( $Enterprise_Arr["EMAIL"][0] ) ?>" size="80"></td>
            </tr>
            <tr class="rowList item">
              <td >Подчиняется</td>
              <td><input name="name_enterprise_parent" type="text" class="textField" id="name_enterprise_parent" value="<? echoML( $Enterprise_Arr["NAME_SMALL_PARENT"][0] ) ?>" size="80" readonly>
                <input name="id_enterprise_parent" type="hidden" id="id_enterprise_parent" value="<? echo $Enterprise_Arr["ID_ENTERPRISE_PARENT"][0] ?>">
                  <a href="JavaScript: 
				  document.getElementById('name_enterprise_parent').value='';
				  document.getElementById('id_enterprise_parent').value='';
				  win = window.open( '../../../SelectEnterprise/SelectEnterprise.php?IdSend=id_enterprise_parent&NameSend=name_enterprise_parent','','height=600,width=850,toolbar=0,top = 50,left = 100,location=0,directoties=0,status=0,menubar=0,resizable=1,scrollbars=1'); win.focus();" class="link_upd">выбрать</a></td>
            </tr>
            <tr class="rowList item">
              <td >Позывной</td>
              <td><input name="callsign" type="text" class="textField" id="callsign" value="<? echoML( $Enterprise_Arr["CALLSIGN"][0] ) ?>" size="80"></td>
            </tr>
			<tr class="rowList item">
				<td >Примечание</td>
				<td><textarea name="note" cols="140" rows="2" class="textField" id="note"><? echoML( $Enterprise_Arr["NOTE"][0] ) ?></textarea></td>
				</tr>
			<tr class="rowList item">
				<td>Преобразовано из </td>
				<td nowrap><input name="name_ent_last_incarnation" type="text" class="textFieldSelect" id="name_ent_last_incarnation" value="<? echoML( $Enterprise_Arr["NAME_ENT_LAST_INCARNATION"][0] )?>" size="100">
						<input type="hidden" name="id_ent_last_incarnation" id="id_ent_last_incarnation"></td>
				</tr>
			<tr class="rowList item">
              <td >Действующее</td>
              <td><label>
                <input name="action" type="checkbox" id="action" value="1" <? if(!empty($Enterprise_Arr["ACTION"][0])) echo "checked";?>>
              </label></td>
            </tr>
			<tr class="rowList item">
				<td >Руководитель</td>
				<td><input name="guard_channel" type="checkbox" id="guard_channel" value="1" <? if(!empty($Enterprise_Arr["GUARD_CHANNEL"][0])) echo 'checked'?>></td>
				</tr>
			<tr class="rowList item">
              <td colspan="2" align="center" ><span style=" margin-top:10px;">
                <input name="act" type="hidden" id="act" value="upd">
                <input name="id_enterprise" type="hidden" id="id_enterprise" value="<? echo $Enterprise_Arr["ID_ENTERPRISE"][0] ?>">
                <input name="Submit" type="submit" class="blueButton" value="Сохранить">
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
