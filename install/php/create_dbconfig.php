<?php
if(!defined('INSTALLER')) {
    header("location:../login.php");
    die("PERMISSION DENIED!");
}

if($_POST['dbhost']!='' && $_POST['dbname']!='' && $_POST['dbpass']!='' && $_POST['dbuser']!='' && $_POST['dbpref']!=''){

        //config.php schreiben

        $dbhost = $_POST['dbhost'];
        $dbuser = $_POST['dbuser'];
        $dbpass = $_POST['dbpass'];
        $dbname = $_POST['dbname'];
        $dbpref = $_POST['dbpref'];

        try {
    $pdo = new PDO("mysql:host=".$db_host.";dbname=".$db_name.";charset=utf8", $db_user, $db_pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $error) {
        die("<strong>Unable to select MySQL database</strong><br />".$error->getMessage());
    }
    $pdo=null;

    $config = "<?php\n";
    $config .= "$"."db_host = "."\"".$_POST['dbhost']."\";\n";
    $config .= "$"."db_user = "."\"".$_POST['dbuser']."\";\n";
    $config .= "$"."db_pass = "."\"".$_POST['dbpass']."\";\n";
    $config .= "$"."db_name = "."\"".$_POST['dbname']."\";\n";
    $config .= "$"."dbpref = "."\"".$dbpref."\";\n";
    $config .= "define("."\""."DB_PREFIX"."\"".", "."\"".$dbpref."\");\n";
    $config .= "?>";
    $temp = fopen("../dbconfig.php","w");
    fwrite($temp, $config);
    fclose($temp);
    chmod("../dbconfig.php",0544);

    header('location: index.php?step4');

}
