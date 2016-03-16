<? if(0){ ?>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" /> 
<link rel="stylesheet" type="text/css" href="styles/styles.css">
<link rel="stylesheet" type="text/css" href="styles/nstyles.css">
<link rel="stylesheet" type="text/css" href="styles/style_menu.css"> 
<? } ?>

<a name="top" id="top"></a>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin-bottom:4px">
  <tr>
    <td width=""><span id="siteName">Справочник предприятий</span></td>
    <td width="" align="right" nowrap="nowrap"><? //echo $fio ?></td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" style=" margin-bottom:10px; font-size:12px;">
  <tr>
    <td width="%" id="mainMenuLeft2"><div style="width:4px"></div></td>
    <td width="90%" id="mainMenuNav2"><a href="<? echo $_SESSION["SITE_PATH_ENTERPRISE"] ?>/pages/SearchEnterprise/SearchEnterprise.php" class="mainMenuItem" target="_top">Поиск 
	</a><?
	if($_SESSION["acc_kanc"])
	{
	?><a href="<? echo $_SESSION["SITE_PATH_ENTERPRISE"] ?>/pages/Enterprise/Enterprise/AddEnterprise/AddEnterprise.php" class="mainMenuItem" target="_top">Добавить предприятие
    </a><a href="<? echo $_SESSION["SITE_PATH_ENTERPRISE"] ?>/pages/Enterprise/Area/ViewArea/ViewArea.php" class="mainMenuItem" target="_top">Административные единицы
    </a><a href="<? echo $_SESSION["SITE_PATH_ENTERPRISE"] ?>/pages/Enterprise/Country/ViewCountry/ViewCountry.php" class="mainMenuItem" target="_top">Страны
    </a><a href="<? echo $_SESSION["SITE_PATH_ENTERPRISE"] ?>/pages/Enterprise/City/ViewCity/ViewCity.php" class="mainMenuItem" target="_top">Города
    </a><a href="<? echo $_SESSION["SITE_PATH_ENTERPRISE"] ?>/pages/Enterprise/Street/ViewStreet/ViewStreet.php" class="mainMenuItem" target="_top">Улицы
    </a><a href="<? echo $_SESSION["SITE_PATH_ENTERPRISE"] ?>/pages/Enterprise/form_owner/view/view.php" class="mainMenuItem" target="_top">Формы собственности
    </a>
	<?
	}
	?></td>
    <td width="10%" align="right" id="mainMenuNav2"><a href="http://<? echo $HTTP_HOST ?>/globallogin/systems/systems.php" id="mainMenuHome" target="_top">&nbsp;</a>
    </td>
    <td width="%" id="mainMenuRight2"><div style="width:4px"></div></td>
  </tr>
</table>
