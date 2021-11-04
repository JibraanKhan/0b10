CREATE TABLE Customers(
    PRIMARY KEY (Cust_ID),
    Cust_ID      INT AUTO_INCREMENT NOT NULL,
    Cust_firstname VARCHAR(25) NOT NULL,
    Cust_lastname VARCHAR(25) NOT NULL,
    Cust_Address VARCHAR(50) NOT NULL,
    Cust_Phone VARCHAR(20) NOT NULL
)

CREATE TABLE Staff(
    PRIMARY KEY (Staff_ID),
    Staff_ID    NUMERIC AUTO_INCREMENT NOT NULL,
    staff_firstName VARCHAR(25) NOT NULL,
    staff_lastName VARCHAR(25) NOT NULL
)

CREATE TABLE Pokemon_Inventory(
    PRIMARY KEY (Inventory_ID),
    Inventory_ID INT AUTO_INCREMENT, NOT NULL,
    Pokemon_Name VARCHAR(15),
    FOREIGN KEY (Pokemon_Name) REFERENCES Pokemon(Pokemon_Name),
    Pokemon_Price FLOAT NOT NULL
)
CREATE TABLE Costumes_Inventory(
    PRIMARY KEY (Costume_ID),
    Costume_ID INT AUTO_INCREMENT NOT NULL,
    Costume_Type VARCHAR(30),
    FOREIGN KEY (Custome_Type) REFERENCES Costume_Type(Custome_Type),
    Costume_Size VARCHAR(5) NOT NULL
)
CREATE TABLE Orders(
    PRIMARY KEY (Order_ID),
    Order_ID INT AUTO_INCREMENT NOT NULL,
    Pokemon_Name VARCHAR(15),
    FOREIGN KEY (Pokemon_Name) REFERENCES Pokemon(Pokemon_Name),
    Customer_ID INT,
    FOREIGN KEY (Customer_ID) REFERENCES Costumes_Rented(Costume_ID),
    Order_SoldFor FLOAT NOT NULL
)
CREATE TABLE Sightings(
    Pokemon_Name VARCHAR(15),
    FOREIGN KEY (Pokemon_Name) REFERENCES Pokemon(Pokemon_Name),
    Sightings_Location VARCHAR(40) NOT NULL,
    Sightings_Time TIMESTAMP NOT NULL,
    Sightings_NumPokemon int NOT NULL
)
CREATE TABLE Pokemon(
    PRIMARY KEY (Pokemon_name),
    Pokemon_name VARCHAR(15) NOT NULL,
    Pokemon_type VARCHAR(10) NOT NULL
)
CREATE TABLE Costumes_Types(
    PRIMARY KEY (Costume_Type),
    Costume_Type VARCHAR(30) NOT NULL
)
CREATE TABLE Costumes_Rented(
    Costume_ID INT,
    Staff_ID INT,
    FOREIGN KEY (Staff_ID) REFERENCES Staff(Staff_ID),
    FOREIGN KEY (Costume_ID) REFERENCES Costumes_Inventory(Costume_ID),
    Rental_CheckoutDate TIMESTAMP NOT NULL,
    Rental_DueDate TIMESTAMP NOT NULL,
    Rental_ReturnedDate TIMESTAMP NOT NULL
)
CREATE TABLE Fulfilled_Orders(
    Inventory_ID INT,
    FOREIGN KEY (Inventory_ID) REFERENCES Pokemon_Inventory(Inventory_ID)
    Order_ID INT,
    FOREIGN KEY (Order_ID) REFERENCES Orders(Order_ID)
)