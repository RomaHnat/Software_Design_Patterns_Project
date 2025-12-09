<?php require_once __DIR__ . '/../../config/bootstrap.php'; ?>
<form action="/project/newProject/public/index.php/categories/warehouse" method="post" class="allforms" onsubmit="return validateAddToWare()">
    <fieldset>
        <legend class="headerform">Add to Warehouse</legend>

    <div class = "justgrid">

        <div class="grid-item"><label>Category:</label></div>
        <div class="grid-item"> <select name="category" id="selectcat">
                <option disabled selected value="">Select a Category</option>
            <?php
            try {
                $pdo = Database::getInstance()->getConnection();
                $stmt = $pdo->query("SELECT CategoryID, Name FROM categories");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $cat) {
                    echo "<option value='" . $cat["CategoryID"] . "'>" . $cat["CategoryID"] . " - " . $cat["Name"] . "</option>";
                }
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
            </select></div>

        <div class="grid-item"><label>Number to add:</label> </div>
        <div class="grid-item"><input type="text" name="ctoadd" value = "" id="toadd"></div>

        <div class="gridbutton"><input type="submit" name="submitdetails" value="SUBMIT" ></div>

    </div>

    </fieldset>
</form>
