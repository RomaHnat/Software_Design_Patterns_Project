<?php

if (isset($_POST['submitdetails'])) {

    try {
    
        $selected_category = $_POST["category"];

        $cid = $_POST['cid'];
    
        $cname = $_POST['cname'];
    
        $cdescription = $_POST['cdescription'];
      
        $cnumberof = $_POST['cnumberof'];
      
        $cpriceper = $_POST['cpriceper'];
    
        $pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');
    
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $sql = "UPDATE Categories SET CategoryID = :cid, Name = :cname, Description = :cdescription, Number_Of = :cnumberof, Price_Per = :cpriceper WHERE CategoryID = '$selected_category';";
    
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(':cid', strtoupper($cid));
    
        $stmt->bindValue(':cname', ucwords($cname));
    
        $stmt->bindValue(':cdescription', $cdescription);
        
        $stmt->bindValue(':cnumberof', $cnumberof);
        
        $stmt->bindValue(':cpriceper', $cpriceper);
    
        $stmt->execute();

        echo "<script>window.alert(\"Category with ID " . $selected_category. " has been updated\");</script>";
    
    } catch (PDOException $e) {
    
        $title = 'An error has occurred';

        $output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();

        echo "<script>window.alert(\"Primary Key has been violated\");</script>";
    
    }

    include("manageCategories.php");
}

?>
