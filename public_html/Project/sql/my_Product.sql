CREATE TABLE IF NOT EXISTS Products(
    id int AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    description TEXT,
    category VARCHAR(50),
    stock int DEFAULT  0,
    unit_price int,
    visibility TINYINT(1),
    user_id int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    check (stock >= 0)
)