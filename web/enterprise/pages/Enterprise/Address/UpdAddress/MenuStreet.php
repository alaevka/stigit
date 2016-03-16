<?php  define("PROTECTED","OFF");

  include "../../../../include/security.php";

  $name_street = iconv( "utf-8","windows-1251",$_POST['text'] );
  $Cnt = $_POST['size'];

  $SQL_Rep = "SELECT *
                FROM street
               WHERE upper(name_street) LIKE upper('$name_street%')
	    ORDER BY LENGTH(name_street),name_street";
		
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM < $Cnt";

  $street_arr = QueryA($SQL_Rep); 
  
  F_OCICommit();  
?>
<table id="popupList" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="textField">
<? for ( $i = 0; $i < sizeof($street_arr["ID_STREET"]); $i++ ) { ?>
   <tr id="tr<? echo $i ?>" style="cursor:pointer" 
  onMouseOver="this.style.background = '#e4eaf2'; 
               if(cur >= 0)
                 document.getElementById('tr' + cur).style.background = '#ffffff'; 
	       cur=<? echo $i ?>;" 
  onMouseDown="chResult('<? echoML( $street_arr["NAME_STREET"][$i] ) ?>',<? echo $street_arr["ID_STREET"][$i] ?>,<? echo $num ?>)"
  onClick="chResult('<? echoML( $street_arr["NAME_STREET"][$i] ) ?>',<? echo $street_arr["ID_STREET"][$i] ?>,<? echo $num ?>)"
  >
    <td>
      <? echo $street_arr["NAME_STREET"][$i] ?>
    </td>
  </tr>
<? } ?>
</table>