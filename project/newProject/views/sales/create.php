<?php
require_once __DIR__ . '/../../config/bootstrap.php';

$pdo = Database::getInstance()->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT * FROM categories";
$stmt = $pdo->query($sql);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="headerfortable">Categories Data</h2>
<table class="tablewithdata">
    <thead>
        <tr>
            <?php foreach ($rows[0] as $key => $value): ?>
                <th><?php echo $key; ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($row as $value): ?>
                    <td><?php echo $value; ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<form action="/project/newProject/public/index.php/sales" method="post" class="allforms" onsubmit="return validateSale()" style = "margin-bottom: 12%">
    <fieldset>
        <legend class="headerform">Place Sale</legend>

    <div class = "justgrid">

        <div class="grid-item"><label>Category to Buy:</label></div>
        <div class="grid-item"> <select name="category" id="selectcat">
                                    <option disabled selected value="">Select a Category</option>
                                    <?php
                                    try {
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
