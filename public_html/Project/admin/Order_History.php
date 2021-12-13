<?php
//note we need to go up 1 more directory
require(__DIR__ . "/../../../partials/nav.php");
$db = getDB();

$stmt = $db->prepare("SELECT id, address, total_price, payment, created FROM Orders ORDER BY created desc LIMIT 10");
try {
    $stmt->execute([]);
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log(var_export($e, true));
    flash("<pre>" . var_export($e, true) . "</pre>");
}
?>

<div class="container-fluid">
    <h1>Your Order History</h1>
    <table class="table text-light">
        <thead>
            <th>Total Price</th>
            <th>Payment Visa</th>
            <th>Order Date/Time</th>
            <th>Address</th>
            <th>View More Details</th>
        </thead>
        <?php foreach ($results as $item) : ?>
        <tbody>
                <td><?php se($item, "total_price"); ?></td>
                <td><?php se($item, "payment"); ?></td>
                <td><?php se($item, "created"); ?></td>
                <td><?php se($item, "address"); ?></td>
                <td>     
                    <form method = "POST" action="Order_History_Extra_Info.php">
                    <input class="btn btn-primary" type="submit" value="More Info" name="Order-History-Extra-Info-ShowOwner"/>
                    <input type="hidden" name="order_id" value="<?php se($item, "id"); ?>"/>
                    </form>
                </td>
        </tbody>
        <?php endforeach; ?>
    </table>
</div>

<?php
require_once(__DIR__ . "/../../../partials/footer.php");
?>