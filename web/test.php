<?php

putenv("ORACLE_HOME=/u01/app/oracle/product/11.2.0/xe");
putenv("LD_LIBRARY_PATH=/u01/app/oracle/product/11.2.0/xe/lib:/lib:/usr/lib");
putenv("NLS_LANG=RUSSIAN_RUSSIA.CL8MSWIN1251");

try{
//    $conn = new PDO('oci:dbname=192.168.51.111/xe;charset=CL8MSWIN1251', 'DEV03', 'Jd1zQBL2');
//    $conn = new PDO('oci:dbname=192.168.51.112/xe', 'DEV03', 'Jd1zQBL2');
    $conn = new PDO('oci:dbname=192.168.51.112/xe;charset=CL8MSWIN1251', 'DEV03', 'Jd1zQBL2');

    echo 'Connected to database';

//    $count = $conn->exec("INSERT INTO t(id, msg) VALUES ('4', 'блаблабла')");

//    echo $count;


    $sql = "SELECT * FROM t";
    foreach ($conn->query($sql) as $row)
        {
        print $row['ID'] . $row['MSG'] . '<br />';
        }

    $conn = null;


} catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
}


?>
