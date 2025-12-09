<?php
$pdo = new PDO('mysql:host=localhost;dbname=aquaua; charset=utf8', 'root', '');
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
