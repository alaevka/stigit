<?php  define("PROTECTED","OFF");

  include "security.php";
  include "utils.php";
  $text = iconv( "utf-8","windows-1251",$_POST['text'] );
  $Cnt = $_POST['size'];
  $num = $_POST['num'];

//  $Pers = str_replace( " ","%",$Pers );

  //echo '<textarea>'..'</textarea>';

  $SQL_Rep = "
   SELECT * FROM enterprise 
     WHERE 
	   REPLACE(UPPER(Name_Call||' '||Name_Small||' '||Name_Enterprise),'\"') LIKE 
	   REPLACE(UPPER('%' || TRIM('$text') || '%'),'\"')
   ORDER BY pop DESC NULLS LAST,Name_Call
  ";
  
//  echo  $SQL_Rep;
  $SQL_Rep = "SELECT * FROM (" . $SQL_Rep . ") WHERE ROWNUM <= $Cnt + 1";
//die($SQL_Rep);

  $Enterprise_Arr = QueryA($SQL_Rep);
  F_OCICommit( );  
  $text = htmlspecialchars($text,ENT_QUOTES);
?>
<table id="popupList" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" class="textField">
<? for ( $i = 0; $i < sizeof($Enterprise_Arr["ID_ENTERPRISE"]) && $i < $Cnt; $i++ ) { ?>
  <tr id="tr<? echo $i ?>" style="cursor:pointer" 
  onMouseOver="this.style.background = '#e4eaf2'; 
               if(cur >= 0)
                 document.getElementById('tr' + cur).style.background = '#ffffff'; 
	       cur=<? echo $i ?>;" 
  onMouseDown="chResult('<? echo htmlspecialchars($Enterprise_Arr["NAME_CALL"][$i],ENT_QUOTES) ?>','',<? echo $num ?>);"
  onClick="chResult('<? echo htmlspecialchars($Enterprise_Arr["NAME_CALL"][$i],ENT_QUOTES) ?>','',<? echo $num ?>);"
  >
    <td style="padding-left:20px; text-indent:-19px;" <? if(!$Enterprise_Arr['ACTION'][$i]) echo 'style="color:#999999"'; ?>>
      <? 
	    $s 		= htmlspecialchars($Enterprise_Arr["NAME_CALL"][$i],ENT_QUOTES);
		$text 	= trim($text);
		//$s 		= str_replace($text,'<b>'.$text.'</b>',$s);
		
	    if($text!='')$s = preg_replace("|(".trim($text).")|i",'<b>$1</b>',$s);
	    echo $s;
	  ?>
    </td>
  </tr>
<? }
if( !empty($Enterprise_Arr["NAME_CALL"][$Cnt]) ) { ?>
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