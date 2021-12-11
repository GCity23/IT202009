CREATE TABLE IF NOT EXISTS OrderItems(
    id int AUTO_INCREMENT PRIMARY KEY,
    item_id int,
    quantity int DEFAULT  1,
    unit_price int,
    order_id int,
    FOREIGN KEY (item_id) REFERENCES Products(id)
)