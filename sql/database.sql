USE my_store;

CREATE TABLE category (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT
);

CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    category_id INT,
    quantity INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES category(id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

CREATE TABLE account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_role VARCHAR(10) DEFAULT 'user'
);

INSERT INTO category (name, description) VALUES
('Iphone', 'Danh mục sản phẩm điện thoại thông minh của Apple'),
('Ipad', 'Danh mục máy tính bảng cao cấp từ Apple'),
('Macbook', 'Danh mục laptop hiệu suất cao của Apple'),
('PC', 'Danh mục máy tính để bàn và linh kiện'),
('PlayStation', 'Danh mục máy chơi game từ Sony');

/*
Postman: http://localhost/QUANLYBANHANG/api/product
For example:
{
    "name": "Iphone20MaxPro",
    "description": "Siêu ngon với cơ chế camera hiện đại",
    "price": "300.00",
    "category_id": 1,
    "quantity": 10,
    "image": "Iphone01.jpg"
}
*/