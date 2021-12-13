<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
$user_id = get_user_id();

if (isset($_GET["id"]))
{
    $order_id = (int)se($_GET, "id", -1, false);
}

if($order_id < 1){
    flash("Order ID is incorrect", "danger");
    }

echo($order_id);

?>

<?php
require(__DIR__ . "/../../partials/footer.php");
?>