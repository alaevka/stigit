<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Выбор предприятия</title>
<link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/style_menu.css">
<script src="../../../../scripts/CalendarJS.js" type="text/javascript"></script> 
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"></head>
<? /*
	<link type="text/css" href="../../../../scripts/Datepicker/css/ui-lightness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../../scripts/Datepicker/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="../../../../scripts/Datepicker/js/jquery-ui-1.8.18.custom.min.js"></script>
	<script>
	$(function() {
		$.datepicker.setDefaults($.extend(
	    $.datepicker.regional["ru"])
	  );
		$( "#date_birthday" ).datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: 'dd.mm.yy' ,
			yearRange: '1920:2010',
			monthNamesShort: ["Янв","Фев","Мар","Апр","Май","Июн","Июл","Авг","Сен","Окт","Ноя","Дек"] ,
			autoSize: true,
			showButtonPanel: true
		});
	});
	</script> 
			<style type="text/css">
			body{ font: 62.5% "Trebuchet MS", sans-serif;}
			.demoHeaders { margin-top: 2em; }
			#dialog_link {padding: .4em 1em .4em 20px;text-decoration: none;position: relative;}
			#dialog_link span.ui-icon {margin: 0 5px 0 0;position: absolute;left: .2em;top: 50%;margin-top: -8px;}
			ul#icons {margin: 0; padding: 0;}
			ul#icons li {margin: 2px; position: relative; padding: 4px 0; cursor: pointer; float: left;  list-style: none;}
			ul#icons span.ui-icon {float: left; margin: 0 4px;}
		</style>
*/
	?>
<body>
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
		   » <a href="../ViewEmployee/ViewEmployee.php?id_enterprise=<? echo $id_enterprise ?>">Руководство</a>
		   » Добавление сотрудника </div>
		   <div style="font-size:12px; text-align:right;"><? echo $Enterprise_Arr['NAME_SMALL'][0] ?></div>
		   <div class="title2">
		   <a class='page_doc_current' href="../../Employee/ViewEmployee/ViewEmployee.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Руководство</a>
           <a class="page_doc" href="../../Address/ViewAddress/ViewAddress.php?id_enterprise=<? echo $Enterprise_Arr['ID_ENTERPRISE'][0] ?>">Контактная информация</a>
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
            <td colspan="5" class="headBorder">Информация по сотруднику </td>
          </tr>
            <tr class="rowList item">
              <td width="200">Фамилия</td>
              <td nowrap  ><input name="fam" type="text" class="textField" id="fam" size="40" maxlength="100"> 
              в дательном
              <input name="to_fam" type="text" class="textField" id="to_fam" size="40" maxlength="100">
              (кому? чему?)</td>
            </tr>
            <tr class="rowList item">
              <td nowrap >Имя</td>
              <td ><input name="imj" type="text" class="textField" id="imj" size="40" maxlength="100">
              в дательном
              <input name="to_imj" type="text" class="textField" id="to_imj" size="40" maxlength="100"></td>
            </tr>
            <tr class="rowList item">
              <td nowrap >Отчество</td>
              <td ><input name="otch" type="text" class="textField" id="otch" size="40" maxlength="100">
                в дательном
              <input name="to_otch" type="text" class="textField" id="to_otch" size="40" maxlength="100"></td>
            </tr>
            <tr class="rowList item">
              <td >Должность</td>
              <td nowrap><input name="post" type="text" class="textField" id="post" value="" size="100">
                <br>
                в дательном
                  	<input name="to_post" type="text" class="textField" id="to_post" value="" size="100"></td>
            </tr>

            <tr class="rowList item">
              <td >Телефон</td>
              <td><input name="phone" type="text" class="textField" id="phone" size="40" maxlength="100"></td>
            </tr>
            <tr class="rowList item">
            	<td >Мобильный телефон</td>
            	<td><input name="mobile_phone" type="text" class="textField" id="mobile_phone" size="80" maxlength="100"> 
            	+XXXXXXXXXXX (предпочтительный формат) </td>
            	</tr>
            <tr class="rowList item">
              <td >Е-mail</td>
              <td><input name="email" type="text" class="textField" id="email" size="40" maxlength="100"></td>
            </tr>
            <tr class="rowList item">
            	<td >Факс</td>
            	<td><input name="phone4" type="text" class="textField" id="phone4" size="40" maxlength="100"></td>
            	</tr>
            <tr class="rowList item">
              <td >Подразделение</td>
              <td><select name="id_department" id="id_department">
							  <option value=""></option>
			  <? for($i=0;$i<sizeof($Department_Arr['ID_DEPARTMENT']);$i++)
			  {
			  ?>
                <option value="<? echo $Department_Arr['ID_DEPARTMENT'][$i] ?>"
				><? echo $Department_Arr['NAME_DEPARTMENT'][$i] ?></option>
		      <?
			  } 
		      ?>
                </select>              </td>
            </tr>
            <tr class="rowList item">
              <td >День рождения </td>
			  <td >
			<input name="date_birthday" type="text" class="textField" id="date_birthday" style="cursor:hand"  value="" size="15" maxlength="10" >
      <? /*          <IMG title=Календарь style="CURSOR: hand" onClick="showCalendar('date_birthday','../../../../')"  src="../../../../styles/calendar.png"  width="16"  height="16" border=0 align="absmiddle">	     	  */ ?>дд.мм.гггг</td>
            </tr>
			<tr class="rowList item">
				<td >Руководитель</td>
				<td><input name="boss" type="checkbox" id="boss" value="1"></td>
			</tr>
			<tr class="rowList item">
              <td >Статус</td>
              <td><label>
                <input name="action" type="checkbox" id="action" value="0">
              уволен</label></td>
            </tr>
			<tr class="rowList item">
              <td colspan="2" align="center" ><span style=" margin-top:10px;">
              <input name="act" type="hidden" id="act" value="add">
              <input name="id_enterprise" type="hidden" id="id_enterprise" value="<? echo $id_enterprise ?>">
              <input name="Button" type="button" class="blueButton" onClick="
			  	if(form1.to_fam.value=='')
			  		if(confirm('ВНИМАНИЕ! Вы уверены что хотите сохранить Ф.И.О. без дательного падежа?'))
						form1.submit();
					else ;
				else form1.submit();" value="Сохранить">
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
