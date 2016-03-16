<?php  define("PROTECTED","OFF");

  include "security.php";
  include "utils.php";
  $text = iconv( "utf-8","windows-1251",$_POST['text'] );
  $Cnt = $_POST['size'];
  $num = $_POST['num'];


//  $Pers = str_replace( " ","%",$Pers );
  
  $SQL_Rep = "
   SELECT * FROM enterprise 
     WHERE 
	   REPLACE(UPPER(Name_Call||' '||Name_Small||' '||Name_Enterprise),'\"') LIKE 
	   REPLACE(UPPER('%' || TRIM('$text') || '%'),'\"')
   ORDER BY pop DESC NULLS LAST,Name_Call
  ";
  
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM <= $Cnt + 1";
//die($SQL_Rep);

  $enterprise_arr = QueryA($SQL_Rep);
  F_OCICommit( );  
?>
<table id="popupList" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="textField">
<? for ( $i = 0; $i < sizeof($enterprise_arr["ID_ENTERPRISE"]) && $i < $Cnt; $i++ ) { ?>
  <tr id="tr<? echo $i ?>" style="cursor:pointer" 
  onMouseOver="this.style.background = '#e4eaf2'; 
               if(cur >= 0)
                 document.getElementById('tr' + cur).style.background = '#ffffff'; 
	       cur=<? echo $i ?>;" 
  onMouseDown="chResult('<? echo htmlspecialchars(str_replace ("\r\n", ' ', $enterprise_arr["NAME_CALL"][$i]),ENT_QUOTES) ?>',<? echo $enterprise_arr["ID_ENTERPRISE"][$i] ?>,<? echo $num ?>);
  <? if(!empty($_POST['Click'])) echo "Click($num);" ?>"
  onClick="chResult('<? echo htmlspecialchars(str_replace ("\r\n", ' ', $enterprise_arr["NAME_CALL"][$i]),ENT_QUOTES) ?>',<? echo $enterprise_arr["ID_ENTERPRISE"][$i] ?>,<? echo $num ?>);
  <? if(!empty($_POST['Click'])) echo "Click($num);" ?>"
  >
    <td style="padding-left:20px; text-indent:-19px; border-bottom:#C5DEF5 solid 1px;">
      <? 
	    $s = htmlspecialchars($enterprise_arr["NAME_CALL"][$i],ENT_QUOTES);
	    if($text!='')$s = preg_replace("/(".str_replace("/","\/",trim($text)).")/i",'<b>$1</b>',$s);
	    echo $s;
	  ?>
    </td>
  </tr>
<? }
if( !empty($enterprise_arr["NAME_CALL"][$Cnt]) ) { ?>
   <tr><td>...</td></tr>
<? 
} 
else { 
?>
  <tr><td height="3px"></td></tr>
<? 
} 
?>
</table>
