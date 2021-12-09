<?php
require(__DIR__ . "/../../partials/nav.php");
$db = getDB();
$results = [];
if (!isset($user_id)) {
    $user_id = get_user_id();
}
error_log("inventory");
$stmt = $db->prepare("SELECT user_id, item_id, unit_price, name , quantity FROM Carts WHERE user_id = :uid");
try {
    $stmt->execute([":uid" => $user_id]);
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log(var_export($e, true));
    flash("<pre>" . var_export($e, true) . "</pre>");
}
//TODO
//display inventory output
//allow triggering effects for next game session
//store triggered items in a new table (so it persists between page loads and logouts)
?>

<h5>Your Cart</h5>
<div class="container-fluid">
    <h1>Your Items</h1>
    <div class="row row-cols-1 row-cols-md-5 g-4">
        <?php foreach ($results as $item) : ?>
            <div class="col">
                <div class="card bg-white">
                    <div class="card-header">
                        Items in your Cart
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Name: <?php se($item, "name"); ?></h5>
                        <p class="card-text">Quantity: <?php se($item, "quantity"); ?></p>
                        <p class="card-text">Unit Price: <?php se($item, "unit_price"); ?></p>
                    </div>
                    <div class="card-footer">
                        Total Cost: <?php se($item, "unit_price"); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>