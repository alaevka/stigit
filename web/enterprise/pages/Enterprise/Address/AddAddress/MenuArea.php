<?php  define("PROTECTED","OFF");

  include "../../../../include/security.php";

  $name_area = iconv( "utf-8","windows-1251",$_POST['text'] );
  $Cnt = $_POST['size'];

  $SQL_Rep = "SELECT *
                FROM area
               WHERE upper(name_area) LIKE upper('%$name_area%')
	    ORDER BY LENGTH(name_area),name_area";
		
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM < $Cnt";

  $area_arr = QueryA($SQL_Rep); 
  
  F_OCICommit();  
?>
<table id="popupList" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="textField">
<? for ( $i = 0; $i < sizeof($area_arr["ID_AREA"]); $i++ ) { ?>
   <tr id="tr<? echo $i ?>" style="cursor:pointer" 
  onMouseOver="this.style.background = '#e4eaf2'; 
               if(cur >= 0)
                 document.getElementById('tr' + cur).style.background = '#ffffff'; 
	       cur=<? echo $i ?>;" 
  onMouseDown="chResult('<? echoML( $area_arr["NAME_AREA"][$i] ) ?>',<? echo $area_arr["ID_AREA"][$i] ?>,<? echo $num ?>)"
  onClick="chResult('<? echoML( $area_arr["NAME_AREA"][$i] ) ?>',<? echo $area_arr["ID_AREA"][$i] ?>,<? echo $num ?>)"
  >
    <td>
      <? echo  $area_arr["NAME_AREA"][$i] . ' ' . $area_arr["TYPE_AREA"][$i] ?>
    </td>
  </tr>
<? } ?>
</table>