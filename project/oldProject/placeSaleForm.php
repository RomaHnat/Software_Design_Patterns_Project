
<?php include("listWater.php"); ?>

<form action="placeSale.php" method="post" class="allforms" onsubmit="return validateSale()" style = "margin-bottom: 12%">
    <fieldset>
        <legend class="headerform">Place Sale</legend>
    
    <div class = "justgrid">

        <div class="grid-item"><label>Category to Buy:</label></div>
        <div class="grid-item"> <select name="category" id="selectcat">
                                    <option disabled selected value="">Select a Category</option>
                                    <?php include("retrieveCatForSelect.php"); ?>
                                </select></div>

        <div class="grid-item"><label>Client's email:</label></div>
        <div class="grid-item"> <input type="text" name="cclientsemail" value = ""  id="clientsemail"></div>
    
        <div class="grid-item"><label>Amount:</label> </div>
        <div class="grid-item"><input type="text" name="camount" value = "" id="amount"></div>
    
        <div class="gridbutton"><input type="button" name="addtocart" value="ADD TO CART" id = "addtocart" onclick="addToCart()">
    
    </div>

        <div class="gridbutton"><textarea id="cart" readonly name="cart"></textarea></div>

        <div class="gridbutton"><input type="submit" name="submitdetails" value="SUBMIT" ></div>
    
    </div>
    
    </fieldset>

</form>

