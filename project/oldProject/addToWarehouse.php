<?php

if (isset($_POST['submitdetails'])) {

    try {
    
        $selected_category = $_POST["category"];

        $ctoadd = $_POST['ctoadd'];

        $pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');
    
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $sql = "UPDATE Categories SET Number_Of = Number_Of + :ctoadd WHERE CategoryID = '$selected_category'";
    
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(':ctoadd', $ctoadd);
    
        $stmt->execute();
    
    } catch (PDOException $e) {
    
        $title = 'An error has occurred';
    
        $output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    
    }

    include("manageCategories.php");
}

?>
