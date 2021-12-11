CREATE TABLE IF NOT EXISTS Carts(
    id int AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    item_id int,
    quantity int DEFAULT  1,
    unit_price int,
    user_id int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id)
)