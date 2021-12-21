<?php
require_once(__DIR__ . "/../../partials/nav.php");
is_logged_in(true);
$results2 = [];
$results3 = [];
$results4 = [];
$db = getDB();
//handle join
//handle page load
$item_id = 0;
$results = 0;
$user_id = get_user_id();
$item_id=se($_GET,"item_id", "",false);
$stmt = $db->prepare("SELECT id, category, description, unit_price, name FROM Products WHERE id = :iid");
try {
    $stmt->execute([":iid" => $item_id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    error_log(var_export($e, true));
    flash("<pre>" . var_export($e, true) . "</pre>");
}
?>

<div class="container-fluid">
    <h1>Detailed Product Information</h1>
    <table class="table text-light">
        <thead>
            <th>Name</th>
            <th>Unit Price</th>
            <th>Category</th>
            <th>Description</th>
        </thead>
        <tbody>
                <td><?php se($results, "name"); ?></td>
                <td><?php se($results, "unit_price"); ?></td>
                <td><?php se($results, "category"); ?></td>
                <td><?php se($results, "description"); ?></td>
                <a href="admin/edit_item.php?id=<?php se($results, "id"); ?>">Edit</a>
        </tbody>
    </table>
</div>
<div class="container-fluid">
    <h1>Leave a Review</h1>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label" for="email">Rating Value</label>
            <input class="form-control" type="number" id="ratingval" name="ratingval" required />
        </div>
        <div class="mb-3">
            <label class="form-label" for="username">Comment</label>
            <input class="form-control" type="text" name="comment"/>
        </div>
        <input type="submit" class="mt-3 btn btn-primary" value="Submit Comment" />
    </form>
</div>

<?php

if (isset($_POST["ratingval"]) && isset($_POST["comment"])) {
    $ratingval = se($_POST, "ratingval", "", false);
    $comment = se($_POST, "comment", "", false);

        $db = getDB();
        $stmt6 = $db->prepare("INSERT INTO Ratings (rating, comment, user_id, item_id) VALUES(:rating, :comment, :uid, :iid)");
        try {
            $stmt6->execute([":rating" => $ratingval, ":comment" => $comment, ":uid" => $user_id, ":iid" => $item_id]);
            flash("Successfully submitted your comment!");
        } catch (Exception $e) {
            flash("Oh no, there was an error!");
        }


$avg_query = "SELECT avg(rating) as `average` FROM Ratings WHERE item_id = :iid";
$newnewSTMT = $db->prepare($avg_query);
try {
    $newnewSTMT->execute([":iid" => $item_id]);
    $r3 = $newnewSTMT->fetch(PDO::FETCH_ASSOC);
    if ($r3) {
        $results4 = $r3;
    }
} catch (PDOException $e) {
    error_log(var_export($e, true));
    flash("<pre>" . var_export($e, true) . "</pre>");
}

$newSTMT4 = $db->prepare("UPDATE Products SET rating = :r WHERE id = :iid");
try {
    $newSTMT4->execute([":iid" => $item_id, ":r" => se($results4, "average", "", false)]);
} catch (PDOException $e) {
    error_log(var_export($e, true));
    flash("<pre>" . var_export($e, true) . "</pre>");
}

}
//EVERYTHING ABOVE THIS IS WORKING
//EVERYTHING ABOVE THIS IS WORKING

$base_query = "SELECT user_id, item_id, rating, comment FROM Ratings WHERE item_id = :iid ORDER BY created desc";
$total_query = "SELECT count(1) as total FROM Ratings WHERE item_id = :iid";

$params[":iid"] = $item_id;
$query = "";

$per_page = 10;
paginate($total_query, $params, $per_page);


$query .= " LIMIT :offset, :count";
$params[":offset"] = $offset;
$params[":count"] = $per_page;
$params[":iid"] = $item_id;
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

<div class="container-fluid">
    <h1>Previous Ratings For This Product</h1>
    <table class="table text-light">
        <thead>
            <th>User Name</th>
            <th>Rating</th>
            <th>Comment</th>
        </thead>
        <?php foreach ($results2 as $item) : ?>
        <tbody>
                <td><?php se(get_username($user_id))?></td>
                <td><?php se($item, "rating"); ?></td>
                <td><?php se($item, "comment"); ?></td>
        </tbody>
        <?php endforeach; ?>
        <h3> Average Rating: <?php se($results4, "average"); ?></h3>
    </table>
</div>

<?php
require(__DIR__ . "/../../partials/pagination.php");
require(__DIR__ . "/../../partials/footer.php");
?>