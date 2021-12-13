<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
$db = getDB();
$results = [];
$user_id = get_user_id();
if (isset($_POST["GoToCheckout"]))
{
    $totalCost=se($_POST,"total_cost", "",false);
    $user_id = se($_POST,"total_cost", "",false);
}

    $FirstName = se($_POST, "FirstName", "", false);
    $LastName = se($_POST, "LastName", "", false);
    $Address = se($_POST, "Address", "", false);
    $City = se($_POST, "City", "", false);
    $State = se($_POST, "State", "", false);
    $Country = se($_POST, "Country", "", false);
    $Zipcode = se($_POST, "Zipcode", "", false);
    $TotalCostCheck = se($_POST, "TotalCostCheck", "", false);
    $PaymentType = se($_POST, "PaymentType", "", false);
?>

<div class="container-fluid">
    <h1>Checkout</h1>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label" for="FirstName">First Name</label>
            <input class="form-control" type="text" id="FirstName" name="FirstName" required maxlength="70" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="LastName">Last Name</label>
            <input class="form-control" type="text" id = "LastName" name="LastName" required maxlength="70" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="Address">Address</label>
            <input class="form-control" type="text" id="Address" name="Address" required minlength="1" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="City">City</label>
            <input class="form-control" type="text" id ="City" name="City" required minlength="1" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="State">State/Province</label>
            <input class="form-control" type="text" id ="State" name="State" required minlength="1" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="Country">Country</label>
            <input class="form-control" type="text" id ="Country" name="Country" required minlength="1" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="Zipcode">Zipcode</label>
            <input class="form-control" type="number" id ="Zipcode" name="Zipcode" required minlength="4" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="TotalCostCheck">Total Cost</label>
            <input class="form-control" type="number" id ="TotalCostCheck" name="TotalCostCheck" value="<?php echo($totalCost)?>" required minlength="1" />
        </div>
        <div class="mb-3">
            <label class="form-label" for="PaymentType">Payment Type</label>
            <input class="form-control" type="text" id ="PaymentType" name="PaymentType" required minlength="1" />
        </div>
        <input type="submit" class="mt-3 btn btn-primary" value="Checkout" name = "ProceedWithCheckout"/>
    </form>
</div>

<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>

<?php
if (isset($_POST["ProceedWithCheckout"]))
{
$stmt = $db->prepare("SELECT Products.stock as PQ, Products.name as PN, Carts.name, Carts.item_id, Carts.name as CN, Products.unit_price as PP, Carts.quantity as CQ, Carts.unit_price as CP FROM Carts INNER JOIN Products ON Carts.item_id = Products.id WHERE Carts.user_id = :uid");
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

$isValid = true;
foreach ($results as $item){
    $PQ = intval(se($item, "PQ", 0, false));
    $PN = se($item, "PN", 0, false);
    if (intval(se($item, "PP", 0, false)) != intval(se($item, "CP", 0, false)))
    {
        $isValid = false;
        flash("Prices do not match!", "danger");
    }
    if (intval(se($item, "PQ", 0, false)) < intval(se($item, "CQ", 0, false)))
    {
        $isValid = false;
        flash("We don't have enough stock!. The item with the stock issue is ".$PN." and you can only buy ".$PQ." of it", "danger");
    }
}

if ($isValid != true)
{
    redirect("cart.php");
}
else
{
    $stmtNEW = $db->prepare("INSERT INTO Orders (user_id, payment, address, total_price) VALUES(:user, :payment, :address, :total_price)");
    try 
    {
        $stmtNEW->execute([":user" => $user_id, ":payment" => $PaymentType, ":address" => $Address , ":total_price" => $TotalCostCheck]);
        $order_id = $db->lastInsertId();
    } 
    catch (PDOException $e) 
    {
        error_log(var_export($e, true));
        flash("<pre>" . var_export($e, true) . "</pre>");
    }

    $stmtNEW2 = $db->prepare("INSERT INTO OrderItems(item_id, quantity, unit_price, order_id)
    SELECT item_id, quantity, unit_price, :order_id FROM Carts where user_id = :uid");
        try 
        {
            $stmtNEW2->execute([":uid" => $user_id, ":order_id" => $order_id]);
        } 
        catch (PDOException $e) 
        {
            error_log(var_export($e, true));
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
    
    $stmtNEW3 = $db->prepare("UPDATE Products set stock = stock - (select quantity from Carts where item_id = Products.id and user_id = :uid) WHERE id in (SELECT item_id from Carts WHERE Carts.user_id = :uid)");
        try 
        {
            $stmtNEW3->execute([":uid" => $user_id]);
        } 
        catch (PDOException $e) 
        {
            error_log(var_export($e, true));
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
    
    $newSTMT4 = $db->prepare("DELETE FROM Carts WHERE user_id = :user_id");
        try 
        {
            $newSTMT4->execute([":user_id" => $user_id]);
        }   
        catch (PDOException $e) 
        {
            error_log(var_export($e, true));
            flash("<pre>" . var_export($e, true) . "</pre>");
        }

    redirect("OrderConfirmation.php?id=$order_id");
}
}
require(__DIR__ . "/../../partials/footer.php");
?>