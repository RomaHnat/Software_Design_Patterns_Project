<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link href='style.css' rel='stylesheet' type='text/css'>
    <title>Aquaua - Manage Categories</title>
</head>
<body>

    <?php include("navbar.php"); ?>
        <div class = "undernav">
            <input type = "button" value = "Add Category" id="addcat">
            <input type = "button" value = "Update Category" id="updatecat">
            <input type = "button" value = "Add to Warehouse" id="addtoware">
            <input type = "button" value = "List Water" id="listwater">
        </div>

        <div id = "displayForm">

        <h1 class = "placeholder">Please make your selection above <br>(Manage Categories)</h1>

        </div>

    <?php include("footer.php"); ?>
    
    <script src="script.js"></script>

</body>
</html>