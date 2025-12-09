<?php

if (isset($_POST['submitdetails'])) {

try {

  $cfname = $_POST['cfname'];

  $csname = $_POST['csname'];
  
  $cemail = $_POST['cemail'];

    $pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO customers (f_name, s_name, email) VALUES(:cfname, :csname, :cemail);";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':cfname', $cfname);

    $stmt->bindValue(':csname', $csname);
    
    $stmt->bindValue(':cemail', $cemail);

    $stmt->execute();

    echo "<script>window.alert(\"Client with email " . $cemail. " has been added\");</script>";

}

  catch (PDOException $e) {

  $title = 'An error has occurred';

  $output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();

  echo "<script> window.alert('Email already exist in the system. Client was not registered.'); </script>";

}

}

include("manageClients.php")

?>
