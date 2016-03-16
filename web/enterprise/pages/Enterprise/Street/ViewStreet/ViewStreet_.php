<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Выбор предприятия</title>
<link rel="stylesheet" type="text/css" href="../../../../styles/styles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="../../../../styles/style_menu.css">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<style type="text/css">
<!--
.style1 {font-size: 12px}
-->
</style>
</head>


<body">
<table width="100%" height="100%" border="0" cellpadding="10" cellspacing="0">
      <tr>
        <td style="padding:0px" height="10px">
           <? include "../../../../menu.php"; ?></td>
      </tr>
      <tr class="tdInt">
        <td height="10px">
           <div class="title"> Улицы</div>
		   <div style="font-size:12px; text-align:right;">&nbsp;</div>
		   <div align="left"><a class="link_upd" href="../AddStreet/AddStreet.php">
		     [ добавить улицу ]</a>
	      </div>
	      <div align="center">
	 <form name="form1" method="get" action="">
	        <span style="font-size:12px">Поиск по имени </span>
            <input name="name_street" type="text" class="textField" id="name_street" value="<? echo isset($name_street) ? $name_street : '' ?>" size="40" maxlength="30">

	        <input name="Submit" type="submit" class="blueButton" value="OK">
      </form>
          </div></td>
      </tr>
      <tr valign="top" class="tdInt" height="%">
         <td ><div id="pfc_online" style="border: #072a66 1px solid;">
	    <table class="borderList" width="100%" border="0" cellspacing="1" cellpadding="4" style="border-width:0px;">
          <tr class="headSubList" style="position: relative;top: expression(this.parentElement.parentElement.parentElement.scrollTop);" >
            <td width="7%" align="center" class="headBorder">Название</td>
            <td width="4%" align="center" class="headBorder">Тип</td>
            <td width="10%" align="center" class="headBorder">Код</td>

            <td width="15%" align="center" class="headBorder">Индекс</td>
            <td width="7%" align="center" class="headBorder">?</td>
          </tr>
          <? 
		  for ( $i = 0; $i < sizeof( $Street['ID_STREET']); $i++ ) 
		  { 
		  ?>
		  <a href="../UpdStreet/UpdStreet.php?id_street=<? echo $Street['ID_STREET'][$i] ?>">
           <tr class='rowList item' style="cursor:pointer" onMouseOver="this.style.background = '#f0f5fa';" onMouseOut="this.style.background = '#e4eaf2';">
            <td>&nbsp;<? echo $Street['NAME_STREET'][$i] ?></td>
              <td>&nbsp;<? echo $Street['TYPE'][$i] ?></td>
              <td>&nbsp;<? echo $Street['CODE'][$i] ?></td>
              <td>&nbsp;<? echo $Street['INDEX_STREET'][$i] ?></td>
			  <td nowrap>&nbsp;<? echo $Street['GNINMB'][$i] ?></td>
          </tr>
		  </a>
          <? } ?>
          <tr>
            <td colspan="17" class="endList"></td>
          </tr>
        </table>
	  </div></td>
      </tr>
    </table>
</body>
</html>
