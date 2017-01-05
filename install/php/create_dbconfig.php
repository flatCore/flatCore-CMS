<?php
if(!defined('INSTALLER')) {
    header("location:../login.php");
    die("PERMISSION DENIED!");
}

if(isset($_POST['dbhost']) && isset($_POST['dbname']) && isset($_POST['dbpass']) && isset($_POST['dbuser']) && isset($_POST['dbpref'])){

        //config.php schreiben

        $dbhost = $_POST['dbhost'];
        $dbuser = $_POST['dbuser'];
        $dbpass = $_POST['dbpass'];
        $dbname = $_POST['dbname'];
        $dbpref = $_POST['dbpref'];

        try {
    $pdo = new PDO("mysql:host=".$dbhost.";dbname=".$dbname.";charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $error) {
        die("<strong>Unable to select MySQL database</strong><br />".$error->getMessage()."<br>mysql:host=".$dbhost.";dbname=".$dbname.";charset=utf8". $dbuser. $dbpass);
    }
    $pdo=null;

    $config = "<?php\n";
    $config .= "$"."db_host = "."\"".$_POST['dbhost']."\";\n";
    $config .= "$"."db_user = "."\"".$_POST['dbuser']."\";\n";
    $config .= "$"."db_pass = "."\"".$_POST['dbpass']."\";\n";
    $config .= "$"."db_name = "."\"".$_POST['dbname']."\";\n";
    $config .= "$"."dbpref = "."\"".$dbpref."\";\n";
    $config .= "define("."\""."DB_PREFIX"."\"".", "."\"_".$dbpref."\");\n";
    $config .= "?>";
    $temp = fopen("../dbconfig.php","w");
    fwrite($temp, $config);
    fclose($temp);
    chmod("../dbconfig.php",0755);

    echo "<script language='JavaScript' type='text/javascript'><!--
  window.location.replace('index.php?step4');
//--></script>";
    //header('location: index.php?step4'); // funktioniert nicht, da header in der index.php schon ausgegeben

}