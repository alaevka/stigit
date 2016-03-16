<?php
//phpinfo(); die();
//putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe");
//putenv("LD_LIBRARY_PATH=/u01/app/oracle/product/11.2.0/xe/lib:/lib:/usr/lib");
//putenv("NLS_LANG=CL8MSWIN1251");
//putenv("NLS_LENGTH_SEMANTICS=CHAR");

try{
    $conn = new PDO('oci:dbname=192.168.51.112/xe;charset=AL32UTF8', 'DEV03', 'Jd1zQBL2');

    echo 'Connected to database';

    $count = $conn->exec("INSERT INTO FAILED_ENTRIES(LOGIN, PASSWORD, TRACT_DATETIME, USER_IP) VALUES ('ыфвавыавыа', 'блаблабла', SYSDATE, '123.123.345.546')");

    //echo $count; die();


    $sql = "SELECT * FROM FAILED_ENTRIES";
    foreach ($conn->query($sql) as $row)
        {
        print iconv('UTF-8', 'Windows-1251', $row['LOGIN']) . $row['PASSWORD'] . '<br />';
        }

    $conn = null;


} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}


?>
