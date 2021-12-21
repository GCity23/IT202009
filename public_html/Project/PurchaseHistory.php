<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
$user_id = get_user_id();
$db = getDB();
$results2 = [];

$col = se($_GET, "col", "created", false);
//allowed list
if (!in_array($col, ["total_price", "created", "payment"])) {
    $col = "created"; //default value, prevent sql injection
}

$order = se($_GET, "order", "asc", false);
//allowed list
if (!in_array($order, ["asc", "desc"])) {
    $order = "asc"; //default value, prevent sql injection
}
$name = se($_GET, "name", "", false);
$name2 = se($_GET, "name2", "", false);


$base_query = "SELECT id, address, total_price, payment, created FROM Orders where user_id = :uid";
$total_query = "SELECT count(1) as total FROM Orders where user_id = :uid";

$query = ""; //1=1 shortcut to conditionally build AND clauses
$params = []; //define default params, add keys as needed and pass to execute
$params[":uid"] = $user_id;

if (!empty($name)) {
    $query .= " AND created >= :d1 AND created <=:d2";
    $params[":d1"] = "$name";
    $params[":d2"] = "$name2 23:59:59";
}

if (!empty($col) && !empty($order)) {
    $query .= " ORDER BY $col $order"; //be sure you trust these values, I validate via the in_array checks above
}

$per_page = 10;
paginate($total_query . $query, $params, $per_page);

$query .= " LIMIT :offset, :count";
$params[":offset"] = $offset;
$params[":count"] = $per_page;
$params[":uid"] = $user_id;

$stmt = $db->prepare($base_query . $query);
foreach ($params as $key => $value) {
    $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $stmt->bindValue($key, $value, $type);
}
$params = null;

try {
    $stmt->execute($params); //dynamically populated params to bind
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $results2 = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}
?>

<form class="row row-cols-auto g-3 align-items-center">
        <div class="col">
            <div class="input-group">
                <div class="input-group-text">Date Start</div>
                <input class="form-control" name="name" value="<?php se($name); ?>" />
            </div>
        </div>
        <div class="col">
            <div class="input-group">
                <div class="input-group-text">Date End</div>
                <input class="form-control" name="name2" value="<?php se($name2); ?>" />
            </div>
        </div>
        <div class="col">
            <div class="input-group">
                <div class="input-group-text">Sort</div>
                <!-- make sure these match the in_array filter above-->
                <select class="form-control" name="col" value="<?php se($col); ?>">
                    <option value="total_price">Total</option>
                    <option value="created">Date Purchased</option>
                    <option value="payment">Payment Type</option>
                </select>
                <script>
                    //quick fix to ensure proper value is selected since
                    //value setting only works after the options are defined and php has the value set prior
                    document.forms[0].col.value = "<?php se($col); ?>";
                </script>
                <select class="form-control" name="order" value="<?php se($order); ?>">
                    <option value="asc">Up</option>
                    <option value="desc">Down</option>
                </select>
                <script>
                    //quick fix to ensure proper value is selected since
                    //value setting only works after the options are defined and php has the value set prior
                    document.forms[0].order.value = "<?php se($order); ?>";
                </script>
            </div>
        </div>
        <div class="col">
            <div class="input-group">
                <input type="submit" class="btn btn-primary" value="Apply" />
            </div>
        </div>
    </form>

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
        <?php foreach ($results2 as $item) : ?>
        <tbody>
                <td><?php se($item, "total_price"); ?></td>
                <td><?php se($item, "payment"); ?></td>
                <td><?php se($item, "created"); ?></td>
                <td><?php se($item, "address"); ?></td>
                <td>     
                    <form method = "POST" action="ExtraInfoForOrder.php">
                    <input class="btn btn-primary" type="submit" value="More Info" name="id"/>
                    <input type="hidden" name="order_id" value="<?php se($item, "id"); ?>"/>
                    </form>
                </td>
        </tbody>
        <?php endforeach; ?>
    </table>
</div>

<?php
require(__DIR__ . "/../../partials/pagination.php");
require(__DIR__ . "/../../partials/footer.php");
?>