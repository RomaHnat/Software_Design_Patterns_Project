<form action="updateCategory.php" method="post" class="allforms" onsubmit="return validateCatUpd()">
    <fieldset>
        <legend class="headerform">Update Category</legend>
    
    <div class = "justgrid">

        <div class="grid-item"><label>Category to Update:</label></div>
        <div class="grid-item"> <select name="category" id="selectcat">
                                    <option disabled selected value="">Select a Category</option>
                                    <?php include("retrieveCatForSelect.php"); ?>
                                </select></div>

        <div class="grid-item"><label>CategoryID:</label></div>
        <div class="grid-item"> <input type="text" name="cid" value = "" maxlength="2" id="cid"></div>
    
        <div class="grid-item"><label>Name:</label> </div>
        <div class="grid-item"><input type="text" name="cname" value = "" id="name"></div>
    
        <div class="grid-item"><label>Description:</label> </div>
        <div class="grid-item"><input type="text" name="cdescription" id="description"></div>

        <div class="grid-item"><label>Number of Items: </label></div>
        <div class="grid-item"><input type="text" name="cnumberof" id="numberof"></div>
    
        <div class="grid-item"><label>Price per Item: </label></div>
        <div class="grid-item"><input type="text" name="cpriceper" id="priceper"></div>
    
        <div class="gridbutton"><input type="submit" name="submitdetails" value="SUBMIT" ></div>
    
    </div>
    
    </fieldset>
</form>
