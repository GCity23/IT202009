CREATE TABLE IF NOT EXISTS Ratings(
    id int AUTO_INCREMENT PRIMARY KEY,
    user_id int,
    item_id int,
    rating int,
    comment text,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id)
)