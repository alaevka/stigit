<?php define("PROTECTED","OFF");

include "../../../../include/security.php";
  
if(!$_SESSION["acc_kanc"]) die("<script>alert('Только для канцелярии!');window.history.go(-1);</script>");  
 
if(isset($act)) {
  	$action = !empty($action)?1:0;
    $SQL_Rep = "INSERT INTO form_owner ( id_form_owner, name_form_small, name_form, action )     
                              VALUES ( seq_id_form_owner.NEXTVAL,'$name_form_small','$name_form','$action')";
    $Res = Query(0,$SQL_Rep);
    F_OCICommit();
	die("<script>alert('Запись добавлена!');window.location.href='../view/view.php'</script>");
}

include "add_.php";
?>
