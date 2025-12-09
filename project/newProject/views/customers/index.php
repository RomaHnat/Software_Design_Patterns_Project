<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link href='/project/newProject/public/css/style.css' rel='stylesheet' type='text/css'>
    <title>Aquaua - Manage Clients</title>
</head>
<body>

    <?php include __DIR__ . '/../layouts/navbar.php'; ?>
        <div class = "undernav">
            <input type = "button" value = "Add Client" id="addcl">
            <input type = "button" value = "Update Client" id="updatecl">
            <input type = "button" value = "List Clients" id="listcl">
        </div>

        <div id = "displayForm">

        <h1 class = "placeholder">Please make your selection above <br>(Manage Clients)</h1>
        </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="/project/newProject/public/js/app.js"></script>

</body>
</html>
