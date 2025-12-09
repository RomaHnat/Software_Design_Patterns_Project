<form action="addToWarehouse.php" method="post" class="allforms" onsubmit="return validateAddToWare()">
    <fieldset>
        <legend class="headerform">Add to Warehouse</legend>
    
    <div class = "justgrid">

        <div class="grid-item"><label>Category:</label></div>
        <div class="grid-item"> <select name="category" id="selectcat">
                <option disabled selected value="">Select a Category</option>
            <?php include("retrieveCatForSelect.php"); ?>
            </select></div>
    
        <div class="grid-item"><label>Number to add:</label> </div>
        <div class="grid-item"><input type="text" name="ctoadd" value = "" id="toadd"></div>
    
        <div class="gridbutton"><input type="submit" name="submitdetails" value="SUBMIT" ></div>
    
    </div>
    
    </fieldset>
</form>
