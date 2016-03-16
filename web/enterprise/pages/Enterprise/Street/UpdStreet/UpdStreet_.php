<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Выбор предприятия</title>
<link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/style_menu.css">

<script src="../../../../scripts/CalendarJS.js" type="text/javascript"></script> 
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></head>
<body>
<table width="100%" height="100%" border="0" cellpadding="10" cellspacing="0" id="bgTable">
      <tr>
        <td style="padding:0px" height="10px">
           <? include "../../../../menu.php"; ?>		
		 </td>
      </tr>
      <tr class="tdInt">
        <td height="10px"><div class="title"><a href="../ViewStreet/ViewStreet.php"> Улицы </a> » Редактирование улицы </div>
          <div style="font-size:12px; text-align:right;">&nbsp;</div>
		</td>
      </tr>
      <tr class="tdInt" height="%">
	  <form name="form1" method="post" action="">
        <td valign="top">
		<table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="2">
          <tr class="headSubList">
            <td colspan="5" class="headBorder">Информация по сотруднику </td>
          </tr>
            <tr class="rowList item">
              <td width="200" nowrap >Название</td>
              <td ><input name="name_street" type="text" class="textField" id="name_street" value="<? echoML( $Street_Arr["NAME_STREET"][0] ) ?>" size="40"></td>
            </tr>
            <tr class="rowList item">
              <td >Тип</td>
              <td><input name="type" type="text" class="textField" id="type" value="<? echoML( $Street_Arr["TYPE"][0] ) ?>" size="40"></td>
            </tr>
            <tr class="rowList item">
              <td >Код</td>
              <td><input name="code" type="text" class="textField" id="code" value="<? echoML( $Street_Arr["CODE"][0] ) ?>" size="40"></td>
            </tr>
            <tr class="rowList item">
              <td >Индекс</td>
              <td><input name="index_street" type="text" class="textField" id="index_street" value="<? echoML( $Street_Arr["INDEX_STREET"][0] ) ?>" size="40"></td>
            </tr>

            <tr class="rowList item">
              <td >?</td>
              <td><input name="gninmb" type="text" class="textField" id="gninmb" value="<? echoML( $Street_Arr["GNINMB"][0] ) ?>" size="40"></td>
            </tr>
			<tr class="rowList item">
              <td colspan="2" align="center" ><span style=" margin-top:10px;">
              <input name="act" type="hidden" id="act" value="upd">
              <input name="id_street" type="hidden" id="id_street" value="<? echo $id_street ?>">
			  <input name="Button2" type="button" class="blueButton" value="Удалить"
			  onClick="javascript:
      if(confirm('Вы уверены что хотите удалить улицу!')) 
      window.location.href='UpdStreet.php?id_street=<? echo $id_street ?>&act=del'">
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
