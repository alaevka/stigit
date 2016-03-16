<?php
session_start();
//if(isset($_SESSION['TAB_NUM'])) $_SESSION['tab_numb'] = $_SESSION['TAB_NUM'];
$_SESSION["SITE_PATH_ENTERPRISE"] = dirname($PHP_SELF);    
header("Location:http://$HTTP_HOST$PHP_SELF/../pages/SearchEnterprise/SearchEnterprise.php");
?><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
