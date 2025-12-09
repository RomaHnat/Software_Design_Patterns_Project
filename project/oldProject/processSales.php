<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <link href='style.css' rel='stylesheet' type='text/css'>
    <title>Aquaua - Process Sales</title>
</head>
<body>

    <?php include("navbar.php"); ?>
        <div class = "undernav">
            <input type = "button" value = "Place Sale" id="placesale">
            <input type = "button" value = "Cancel Sale" id="cancelsale">
        </div>

        <div id = "displayForm">

        <h1 class = "placeholder">Please make your selection above <br>(Process Sales)</h1>
        </div>

    <?php include("footer.php"); ?>
    
    <script src="script.js"></script>

    <script> 
    function addToCart() {
    let item = document.getElementById("selectcat").value;
    let amount = document.getElementById("amount").value;

    if (item !== "" && amount !== "") {
        const cart = document.getElementById("cart");

        let cartItems = cart.value.split('\n').filter(Boolean); 
        let itemExists = false;
        cartItems.forEach((cartItem, index) => {
            let itemData = cartItem.split(' - ');
            if (itemData[0] === item) {
                cartItems[index] = item + " - " + amount;
                itemExists = true;
            }
        });

        if (!itemExists) {
            cartItems.push(item + " - " + amount);
        }

        cart.value = cartItems.join('\n');
    } else {
        window.alert("You did not select a category or did not enter the amount.");
    }
}

</script>

</body>
</html>