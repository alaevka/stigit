<?php  define("PROTECTED","OFF");

  include "security.php";
  include "utils.php";
  $text = iconv( "utf-8","windows-1251",$_POST['text'] );
  $Cnt = $_POST['size'];
  $num = $_POST['num'];

//  $Pers = str_replace( " ","%",$Pers );

  
  $SQL_Rep = "
   SELECT * FROM city WHERE UPPER(Name_city) LIKE UPPER('' || TRIM('$text') || '%')
   ORDER BY Name_city
  ";
  
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM <= $Cnt + 1";
//die($SQL_Rep);

  $Enterprise_Arr = QueryA($SQL_Rep);
  F_OCICommit( );  
?>
<table id="popupList" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="textField">
<? for ( $i = 0; $i < sizeof($Enterprise_Arr["ID_CITY"]) && $i < $Cnt; $i++ ) { ?>
  <tr id="tr<? echo $i ?>" style="cursor:pointer" 
  onMouseOver="this.style.background = '#e4eaf2'; 
               if(cur >= 0)
                 document.getElementById('tr' + cur).style.background = '#ffffff'; 
	       cur=<? echo $i ?>;" 
  onMouseDown="chResult('<? echo htmlspecialchars($Enterprise_Arr["NAME_CITY"][$i],ENT_QUOTES) ?>',<? echo $Enterprise_Arr["ID_CITY"][$i] ?>,<? echo $num ?>);"
  onClick="chResult('<? echo htmlspecialchars($Enterprise_Arr["NAME_CITY"][$i],ENT_QUOTES) ?>',<? echo $Enterprise_Arr["ID_CITY"][$i] ?>,<? echo $num ?>);"
  >
    <td>
      <? echo $Enterprise_Arr["TYPE_CITY"][$i].'. '.htmlspecialchars($Enterprise_Arr["NAME_CITY"][$i],ENT_QUOTES) ?>
    </td>
  </tr>
<? }
if( !empty($Enterprise_Arr["NAME_CITY"][$Cnt]) ) { ?>
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