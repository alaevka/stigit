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
        <td height="10px">
		   <div class="title"><a href="../ViewCountry/ViewCountry.php">
		   Города </a> 
		   » Добавить город  </div>
		   <div style="font-size:12px; text-align:right;"> &nbsp;</div>
		</td>
      </tr>
      <tr class="tdInt" height="%">
	  <form name="form1" method="post" action="">
        <td valign="top">
		<table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="2">
          <tr class="headSubList">
            <td colspan="5" class="headBorder">Информация</td>
          </tr>
            <tr class="rowList item">
              <td width="200" nowrap >Название</td>
              <td ><input name="name_country" type="text" class="textField" id="name_country" size="40"></td>
            </tr>
            <tr class="rowList item">
              <td >Код</td>
              <td><input name="code_m" type="text" class="textField" id="code_m" size="40"></td>
            </tr>

            <tr class="rowList item">
              <td >Зона</td>
              <td><input name="post_zone" type="text" class="textField" id="post_zone" size="40"></td>
            </tr>
            <tr class="rowList item">
              <td >Название (Eng) </td>
              <td><input name="name_country_eng" type="text" class="textField" id="name_country_eng" size="40"></td>
            </tr>
			<tr class="rowList item">
              <td colspan="2" align="center" ><span style=" margin-top:10px;">
              <input name="act" type="hidden" id="act" value="add">
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
