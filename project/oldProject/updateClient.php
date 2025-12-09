<?php

if (isset($_POST['submitdetails'])) {

    try {
    
        $selected_email = $_POST["cclemail"];

        $cfname = $_POST['cfname'];

        $csname = $_POST['csname'];
  
        $cemail = $_POST['cemail'];

    $pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "UPDATE Customers SET F_Name = :cfname, S_Name = :csname, Email = :cemail WHERE Email = '$selected_email';";

    $stmt = $pdo->prepare($sql);

    $stmt->bindValue(':cfname', $cfname);

    $stmt->bindValue(':csname', $csname);
    
    $stmt->bindValue(':cemail', $cemail);

    $stmt->execute();

    echo "<script>window.alert(\"Client with email " . $selected_email. " has been updated\");</script>";

}

  catch (PDOException $e) {


    $title = 'An error has occurred';

    $output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();

    echo "<script> window.alert('Email already exist in the system. Client was not Updated.'); </script>";

}

include("manageClients.php");

}

?>
