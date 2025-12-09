<?php

if (isset($_POST['submitdetails'])) {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = $_POST['cclientsemail']; 
        $clientID_query = "SELECT customerID FROM Customers WHERE email = :email";
        $clientID_stmt = $pdo->prepare($clientID_query);
        $clientID_stmt->execute([':email' => $email]);
        $clientID_result = $clientID_stmt->fetch(PDO::FETCH_ASSOC);

        if ($clientID_result) {
            $clientID = $clientID_result['customerID'];

            $salesItems = explode("\n", $_POST['cart']); 
            foreach ($salesItems as $item) {
                $parts = explode(" - ", $item);
                $categoryID = substr($parts[0], 0, 2);
                $amount = (float) substr($parts[1], 0); 

                $number_of_query = "SELECT Number_Of FROM Categories WHERE CategoryID = :categoryID";
                $number_of_stmt = $pdo->prepare($number_of_query);
                $number_of_stmt->execute([':categoryID' => $categoryID]);
                $number_of_result = $number_of_stmt->fetch(PDO::FETCH_ASSOC);

                if ($number_of_result && $number_of_result['Number_Of'] < $amount) {
                    echo "<script> window.alert(\"Amount exceeds available quantity for category $categoryID. Please go to the previous page. \"); </script>";
                    exit();
                }
            }

            $sales_insert_query = "INSERT INTO Sales (Price, Date_Sold, CustomerID) VALUES (:price, NOW(), :clientID)";
            $sales_stmt = $pdo->prepare($sales_insert_query);
            $sales_stmt->bindValue(':price', 0); 
            $sales_stmt->bindValue(':clientID', $clientID);
            $price = 0; 

            if ($sales_stmt->execute()) {
                $salesID = $pdo->lastInsertId(); 

                foreach ($salesItems as $item) {
                    $parts = explode(" - ", $item);
                    $categoryID = substr($parts[0], 0, 2);
                    $amount = (float) substr($parts[1], 0); 

                    $price_per_query = "SELECT Price_Per FROM Categories WHERE CategoryID = :categoryID";
                    $price_per_stmt = $pdo->prepare($price_per_query);
                    $price_per_stmt->execute([':categoryID' => $categoryID]);
                    $price_per_result = $price_per_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($price_per_result) {
                        $price_per = $price_per_result['Price_Per'];
                        $price += $price_per * $amount; 
                    } else {
                        echo "<script>window.alert(\"Unable to retrieve price for category $categoryID \"); </script>";
                    }
                    
                    $salesItems_insert_query = "INSERT INTO SalesItems (SaleID, CategoryID, Number_Of_Cat) VALUES (:salesID, :categoryID, :amount)";
                    $salesItems_stmt = $pdo->prepare($salesItems_insert_query);
                    $salesItems_stmt->bindValue(':salesID', $salesID);
                    $salesItems_stmt->bindValue(':categoryID', $categoryID);
                    $salesItems_stmt->bindValue(':amount', $amount);
                    $salesItems_stmt->execute();

                    $update_category_query = "UPDATE Categories SET Number_Of = Number_Of - :amount WHERE CategoryID = :categoryID";
                    $update_category_stmt = $pdo->prepare($update_category_query);
                    $update_category_stmt->bindValue(':amount', $amount);
                    $update_category_stmt->bindValue(':categoryID', $categoryID);
                    $update_category_stmt->execute();
                }

                $sales_update_query = "UPDATE Sales SET Price = :price WHERE SaleID = :salesID";
                $sales_update_stmt = $pdo->prepare($sales_update_query);
                $sales_update_stmt->bindValue(':price', $price);
                $sales_update_stmt->bindValue(':salesID', $salesID);
                $sales_update_stmt->execute();

                echo "<script>window.alert('Sale has been placed.');</script>";
            } 
            
            else {
                echo "Error: Unable to place sale.";
            }
        } 
        
        else {
            echo "<script>window.alert('Client not found for the given email.');</script>";
        }
    } 
    
    catch (PDOException $e) {
        $title = 'An error has occurred';
        $output = 'Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine();
        echo "<script>window.alert(\"Sale was not placed.\");</script>";
    }
    include('processSales.php');
}

?>