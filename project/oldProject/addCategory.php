<?php

if (isset($_POST['submitdetails'])) {

try {

  $cid = $_POST['cid'];

  $cname = $_POST['cname'];

  $cdescription = $_POST['cdescription'];
  
  $cnumberof = $_POST['cnumberof'];
  
  $cpriceper = $_POST['cpriceper'];

    $pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO Categories (categoryId, name, description, number_of, price_per) VALUES(:cid, :cname, :cdescription, :cnumberof, :cpriceper);";

    $stmt = $pdo->prepare($sql);
    
    $stmt->bindValue(':cid', strtoupper($cid));

    $stmt->bindValue(':cname', ucwords($cname));

    $stmt->bindValue(':cdescription', $cdescription);
    
    $stmt->bindValue(':cnumberof', $cnumberof);
    
    $stmt->bindValue(':cpriceper', $cpriceper);

    $stmt->execute();

    echo "<script>window.alert(\"Category with ID " . $cid . " has been added\");</script>";

}

  catch (PDOException $e) {

    $title = 'An error has occurred';

    $output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();

    echo "<script>window.alert(\"Primary Key has been violated\");</script>";

}
include("manageCategories.php");
}

?>
