<?php $pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');
echo "OK";
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$selected_category = $_POST["category"];

echo $selected_category;

$sql = "SELECT * FROM categories WHERE CategoryID = '$selected_category'"; 
$stmt = $pdo->query($sql);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC); 

$cid = $category['CategoryID'];
$cname = $category['Name'];
$cdescription = $category['Description'];
$cnumberof = $category['Number_of_Items'];
$cpriceper = $category['Price_per_Item'];

?>