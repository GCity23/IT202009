CREATE TABLE IF NOT EXISTS Orders(
    id int AUTO_INCREMENT PRIMARY KEY,
    user_id int,
    payment VARCHAR(50),
    address text,
    total_price int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id)
)