<?php
require(__DIR__ . "/../../partials/nav.php");
$db = getDB();
$results = [];
if (!isset($user_id)) {
    $user_id = get_user_id();
}

if (isset($_POST["ClearCart"]))
{
    $newSTMT1 = $db->prepare("DELETE FROM Carts WHERE user_id = :user_id");
    try {
        $newSTMT1->execute([":user_id" => $user_id]);
    } catch (PDOException $e) {
        error_log(var_export($e, true));
        flash("<pre>" . var_export($e, true) . "</pre>");
    }
}

if (isset($_POST["RemoveItem"]))
{
    $cart_id2=se($_POST,"cart_id2", "",false);

    $newSTMT6 = $db->prepare("DELETE FROM Carts WHERE id = :cart_id");
    try {
        $newSTMT6->execute([":cart_id" => $cart_id2]);
    } catch (PDOException $e) {
        error_log(var_export($e, true));
        flash("<pre>" . var_export($e, true) . "</pre>");
}
}

error_log("inventory");
if (isset($_POST["submit"]))
{
    $cart_id=se($_POST,"cart_id", "",false);
    $quantity=se($_POST,"quantity", "",false);
    
    if((($quantity == 0) == false))
    {
        $newSTMT4 = $db->prepare("UPDATE Carts SET quantity = :q WHERE id = :uid");
        try {
            $newSTMT4->execute([":uid" => $cart_id, ":q" => $quantity]);
        } catch (PDOException $e) {
            error_log(var_export($e, true));
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
    }
    else
    {
        $newSTMT5 = $db->prepare("DELETE FROM Carts WHERE id = :cart_id");
        try {
            $newSTMT5->execute([":cart_id" => $cart_id]);
        } catch (PDOException $e) {
            error_log(var_export($e, true));
            flash("<pre>" . var_export($e, true) . "</pre>");
    }
}
}

$stmt = $db->prepare("SELECT id, user_id, item_id, unit_price, name , quantity FROM Carts WHERE user_id = :uid");
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
                        <form method = "POST">
                            <label for="quantity">Update Quantity:</label><br>
                            <input type="text" id="quantity" name="quantity"><br>
                            <input class="btn btn-primary" type="submit" value="Update" name="submit" />
                            <input type="hidden" name="cart_id" value="<?php se($item, "id"); ?>"/>
                        </form> 
                        <form method = "POST">
                            <input class="btn btn-primary" type="submit" value="Remove Item" name="RemoveItem"/>
                            <input type="hidden" name="cart_id2" value="<?php se($item, "id"); ?>"/>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <form method = "POST">
        <input class="btn btn-primary" type="submit" value="Clear Cart" name="ClearCart"/>
        </form> 
    </div>
</div>

<?php
require(__DIR__ . "/../../partials/footer.php");
?>