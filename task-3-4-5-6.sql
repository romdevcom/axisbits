/* TASK 3 - sql for creation of tables */

/* parent_id - column, for column to implement the hierarchy */
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(250) NOT NULL,
    parent_id int DEFAULT 0
);

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(250) NOT NULL,
    price INT NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE attributes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(250) NOT NULL,
    value VARCHAR(250) NOT NULL
);

CREATE TABLE products_x_attributes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    attribute_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (attribute_id) REFERENCES attributes(id)
);

/* TASK 4 */
SELECT c.name AS category_name, p.name AS product_name, p.price
FROM products AS p
JOIN categories AS c ON p.category_id = c.id
ORDER BY p.price DESC LIMIT 1;

/* TASK 5 */
SELECT a.name AS attribute_name, COUNT(pa.product_id) AS product_count
FROM attributes AS a
JOIN products_x_attributes AS pa ON a.id = pa.attribute_id
GROUP BY a.name
ORDER BY product_count DESC;

/* TASK 6 */
SELECT p.name, p.price
FROM products AS p
JOIN categories AS c ON p.category_id = c.id
WHERE p.price >= 100
  AND p.price <= 200
  AND c.name LIKE '%ama';