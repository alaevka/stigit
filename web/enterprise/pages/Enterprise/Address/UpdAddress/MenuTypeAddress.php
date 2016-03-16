<?php  define("PROTECTED","OFF");

  include "../../../../include/security.php";

  $type_address = iconv( "utf-8","windows-1251",$_POST['text'] );
  $Cnt = $_POST['size'];

  $type_address_arr = array();

  $SQL_Rep = "SELECT *
                FROM type_address
               WHERE upper(name_type_address) LIKE upper('$type_address%')
	    ORDER BY name_type_address";
		
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM < $Cnt";

  $type_address_arr = QueryA($SQL_Rep); 
  F_OCICommit();  
?>
<table id="popupList" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="textField">
<? for ( $i = 0; $i < sizeof($type_address_arr["ID_TYPE_ADDRESS"]); $i++ ) { ?>
   <tr id="tr<? echo $i ?>" style="cursor:pointer" 
  onMouseOver="this.style.background = '#e4eaf2'; 
               if(cur >= 0)
                 document.getElementById('tr' + cur).style.background = '#ffffff'; 
	       cur=<? echo $i ?>;" 
  onMouseDown="chResult('<? echoML( $type_address_arr["NAME_TYPE_ADDRESS"][$i] ) ?>',<? echo $type_address_arr["ID_TYPE_ADDRESS"][$i] ?>,<? echo $num ?>)"
  onClick="chResult('<? echoML( $type_address_arr["NAME_TYPE_ADDRESS"][$i] ) ?>',<? echo $type_address_arr["ID_TYPE_ADDRESS"][$i] ?>,<? echo $num ?>)"
  >
    <td>
      <? echo $type_address_arr["NAME_TYPE_ADDRESS"][$i] ?>
    </td>
  </tr>
<? } ?>
</table>