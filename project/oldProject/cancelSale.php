<?php

if (isset($_POST['submitdetails'])) {

    try {

        $csaleid = $_POST['csaleid'];
    
        $pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');
    
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql_select = "SELECT CategoryID, Number_Of_Cat FROM salesitems WHERE SaleID = :csaleid";
        $stmt_select = $pdo->prepare($sql_select);
        $stmt_select->bindValue(':csaleid', $csaleid);
        $stmt_select->execute();
        $sales_items = $stmt_select->fetchAll(PDO::FETCH_ASSOC);
        
        $sql_delete_salesitems = "DELETE FROM salesitems WHERE SaleID = :csaleid";
        $stmt_delete_salesitems = $pdo->prepare($sql_delete_salesitems);
        $stmt_delete_salesitems->bindValue(':csaleid', $csaleid);
        $stmt_delete_salesitems->execute();

        $sql_delete_sales = "DELETE FROM sales WHERE SaleID = :csaleid";
        $stmt_delete_sales = $pdo->prepare($sql_delete_sales);
        $stmt_delete_sales->bindValue(':csaleid', $csaleid);
        $stmt_delete_sales->execute();

        foreach ($sales_items as $item) {
            $categoryID = $item['CategoryID'];
            $number_of_cat = $item['Number_Of_Cat'];

            $sql_update_categories = "UPDATE categories SET Number_Of = Number_Of + :number_of_cat WHERE CategoryID = :categoryID";
            $stmt_update_categories = $pdo->prepare($sql_update_categories);
            $stmt_update_categories->bindValue(':number_of_cat', $number_of_cat);
            $stmt_update_categories->bindValue(':categoryID', $categoryID);
            $stmt_update_categories->execute();
        }
        

        echo "<script>window.alert(\"Sale has been canceled\");</script>";

        include("processSales.php");
    
    } catch (PDOException $e) {

        $title = 'An error has occurred';
    
        $output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
    
        echo $output;

        echo "<script>window.alert(\"Sale does not exist\");</script>";

    }

}

?>
