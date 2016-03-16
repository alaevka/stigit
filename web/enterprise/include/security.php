<?php if ( !defined("PROTECTED") ) { echo "Hacking attention"; die(); }
  ini_set( "max_execution_time","12000" );
  session_start();
  include __DIR__.'../../../../INCLUDE/db.php';
 //********************************************************************************//
//                           Подключение к БД                                     //
//********************************************************************************//
  $conn = OCILogon( 'osk','osk','oxydb','CL8MSWIN1251');
   if ( !$conn )
  {
    echo "Connect error<br>";
    die();
  }

  function TryTAB_NUM()
  { //print_r($_SESSION);
    if ( empty( $_SESSION['tab_numb'] ) )
    {
      echo "User not login";
      die();
    }
    else 
      return $_SESSION['tab_numb'];
  }

/*
function QError($error){
	echo '<strong>Произошла ошибка: <font color="#889999">'.$error["message"].'</font>';
		//.'<br>Позиция ошибки: '. substr($error["sqltext"],$error["offset"]-20,40);//.'</font><br>Запрос:'. ShowSqlError($error["sqltext"]); 
	$a_err = debug_backtrace();
	echo '<br>Файл: <font color="#889999">'.$a_err[1]['file'].'</font>';
	echo '<br>Строка №: <font color="#889999">'.$a_err[1]['line'].'</font>';
	$text = substr($error["sqltext"],$error["offset"]-20,20);	
	$s = $error["sqltext"];
	$s = str_replace($text,'<b style="background-color:#FFCC99">'.$text.'</b>',$s);
	echo "<br>Запрос:<pre style=\"border:#9999CC solid 1px; font:'Courier New', Courier, monospace; background:#FFFFCC;\">".
			$s
		."</pre>";	
}   */


  function Query($b,$sql)      // $b = 1 OCIFetch
  {
    GLOBAL $conn;
    $stmt = OCIParse($conn ,$sql);          
    $err  = OCIExecute($stmt,OCI_DEFAULT);    
    if ( !$err ) 
    {
      $error = OCIError($stmt);
      //echo '<strong>Произошла ошибка: <font color="#889999">'.$error["message"].'</font><br>Запрос: <font color="#889999">'.$error["sqltext"].'</font></strong>'; 
	  QError($error);
      die();
    }
    if( $b )OCIFetch($stmt);
  
    return $stmt;                
  }
  
  function QueryA($sql)      
  {
    GLOBAL $conn;
    $stmt = OCIParse($conn ,$sql);          
    $err  = OCIExecute($stmt,OCI_DEFAULT);    
    if ( !$err ) 
    {
      $error = OCIError($stmt);
      //echo '<strong>Произошла ошибка: <font color="#889999">'.$error["message"].'</font><br>Запрос: <font color="#889999">'.$error["sqltext"].'</font></strong>'; 
	  QError($error);
      die();
    }         
     OCIFetchStatement($stmt,$Arr);    
     return $Arr;              
  }
  
  function QueryB($sql)      
  {
    GLOBAL $conn;
    
    $stmt = OCIParse( $conn,$sql );
    $DBody = OCINewDescriptor( $conn,OCI_D_LOB );
    OCIBindByName( $stmt,":Body_Loc",$DBody,-1,OCI_B_BLOB );
    $err  = OCIExecute($stmt,OCI_DEFAULT);    
    if ( !$err ) 
    {
      $error = OCIError($stmt);
      //echo '<strong>Произошла ошибка: <font color="#889999">'.$error["message"].'</font><br>Запрос: <font color="#889999">'.$error["sqltext"].'</font></strong>'; 
	  QError($error);
      die();
    }     
    return $DBody;    
 }
  
  function F_OCIFetchStatement($Res)
  {
     OCIFetchStatement($Res,$Arr);    
     return $Arr;
  }
  function F_OCIFetch($Res)
  {
    return OCIFetch($Res);
  }    
  
  function F_OCIResult($Res,$s)
  {
    return OCIResult($Res,$s);
  }
  
  function F_OCICommit( )
  {
    GLOBAL $conn;
    OCICommit( $conn );
  }
  
  function echoML($str)
 {
   echo htmlspecialchars($str);
 }

 include "convert.php";
 ?>
