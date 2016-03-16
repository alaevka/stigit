<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Untitled Document</title>
</head>
<script type="text/javascript" src="Temperat.js"></script>
<body>
<p>t = <span id="tempid"></span></p>
<p>&nbsp;</p>
<p style=" color:#0094FF;">drtydshgd</p>
<p style=" color:#FE6D6D">drtydshgd</p>
	<script>
	
	temper ='00,00';
if(temper=='00,00'){
	temper='нет данных';
}
else 
{
	if(temper.replace(',','.')*1<0)
		document.getElementById('tempid').style.color='#0094FF';
	else
		document.getElementById('tempid').style.color='#FE6D6D';
	temper=temper.replace('-','&ndash;')+'&deg;С';
}


document.getElementById('tempid').innerHTML=temper;


	</script>
</p>
</body>
</html>
