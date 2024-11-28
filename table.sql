-- Products Table
CREATE TABLE Products (
    Product_ID INT PRIMARY KEY AUTO_INCREMENT,
    P_Name VARCHAR(255) NOT NULL,
    P_Description VARCHAR(255) NOT NULL,
    P_Price INT NOT NULL,
    Image_URL VARCHAR(255)
    );

-- Users Table
CREATE TABLE Users (
    User_ID INT PRIMARY KEY AUTO_INCREMENT,
    Name VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL
    );

-- Orders Table
CREATE TABLE Orders (
    Order_ID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    User_ID INT,
    Total_Amount INT NOT NULL,
    Order_Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (User_ID) REFERENCES Users(User_ID)
    );

-- Order Items Table
CREATE TABLE Order_Items (
    OrderItem_ID INT NOT NULL,
    Order_ID INT NOT NULL,
    Product_ID INT NOT NULL,
    Quantity INT NOT NULL,
    FOREIGN KEY (Order_ID) REFERENCES Orders(Order_ID),
    FOREIGN KEY (Product_ID) REFERENCES Products(Product_ID)
    );