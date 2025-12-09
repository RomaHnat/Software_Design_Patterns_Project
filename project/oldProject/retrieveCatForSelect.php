<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT CategoryID, Name FROM categories";
    $result = $pdo->query($sql);

    if ($result->rowCount() > 0) {
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='" . $row["CategoryID"] . "'>" . $row["CategoryID"] . " - " . $row["Name"] . "</option>";
        }
    }
    
    $pdo = null;
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
