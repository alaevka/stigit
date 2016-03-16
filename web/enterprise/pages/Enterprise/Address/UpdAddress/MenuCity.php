<?php  define("PROTECTED","OFF");

  include "../../../../include/security.php";

  $name_city = iconv( "utf-8","windows-1251",$_POST['text'] );
  $Cnt = $_POST['size'];

  $SQL_Rep = "SELECT *
                FROM city
               WHERE upper(name_city) LIKE upper('$name_city%')
  	        ORDER BY LENGTH(name_city),name_city";
  
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM < $Cnt";

  $city_arr = QueryA($SQL_Rep); 
  
  F_OCICommit();  
?>
<table id="popupList" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="textField">
<? for ( $i = 0; $i < sizeof($city_arr["ID_CITY"]); $i++ ) { ?>
   <tr id="tr<? echo $i ?>" style="cursor:pointer" 
  onMouseOver="this.style.background = '#e4eaf2'; 
               if(cur >= 0)
                 document.getElementById('tr' + cur).style.background = '#ffffff'; 
	       cur=<? echo $i ?>;" 
  onMouseDown="chResult('<? echoML( $city_arr["NAME_CITY"][$i]) ?>',<? echo $city_arr["ID_CITY"][$i] ?>,<? echo $num ?>)"
  onClick="chResult('<? echoML( $city_arr["NAME_CITY"][$i]) ?>',<? echo $city_arr["ID_CITY"][$i] ?>,<? echo $num ?>)"
  >
    <td>
      <? echo $city_arr["TYPE_CITY"][$i] . '. ' . $city_arr["NAME_CITY"][$i] ?>
    </td>
  </tr>
<? } ?>
</table>