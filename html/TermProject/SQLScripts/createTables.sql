CREATE TABLE Customers(
    PRIMARY KEY (Cust_ID),
    Cust_ID      INT AUTO_INCREMENT NOT NULL,
    Cust_FirstName VARCHAR(25) NOT NULL,
    Cust_LastName VARCHAR(25) NOT NULL,
    Cust_Address VARCHAR(50) NOT NULL,
    Cust_Phone VARCHAR(20) DEFAULT NULL
);

CREATE TABLE Staff(
    PRIMARY KEY (Staff_ID),
    Staff_ID    INT AUTO_INCREMENT NOT NULL,
    Staff_FirstName VARCHAR(25) NOT NULL,
    Staff_LastName VARCHAR(25) NOT NULL
);

CREATE TABLE Pokemon(
    PRIMARY KEY (Pokemon_Name),
    Pokemon_Name VARCHAR(15) NOT NULL,
    Pokemon_Type VARCHAR(10) NOT NULL
);

CREATE TABLE Costume_Types(
    PRIMARY KEY (Costume_Type),
    Costume_Type VARCHAR(30) NOT NULL
);

CREATE TABLE Pokemon_Inventory(
    PRIMARY KEY (Inventory_ID),
    Inventory_ID INT AUTO_INCREMENT NOT NULL,
    Pokemon_Name VARCHAR(15),
    Pokemon_Price FLOAT NOT NULL,
    Pokemon_Active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (Pokemon_Name) REFERENCES Pokemon(Pokemon_Name) ON DELETE RESTRICT
);

CREATE TABLE Costumes_Inventory(
    PRIMARY KEY (Costume_ID),
    Costume_ID INT AUTO_INCREMENT NOT NULL,
    Costume_Type VARCHAR(30),
    Costume_Size VARCHAR(5) NOT NULL,
    FOREIGN KEY (Costume_Type) REFERENCES Costume_Types(Costume_Type)
);

CREATE TABLE Orders(
    PRIMARY KEY (Order_ID),
    Order_ID INT AUTO_INCREMENT NOT NULL,
    Pokemon_Name VARCHAR(15),
    Cust_ID INT,
    Inventory_ID INT,
    Order_SoldFor FLOAT DEFAULT NULL,
    FOREIGN KEY (Pokemon_Name) REFERENCES Pokemon(Pokemon_Name) ON DELETE RESTRICT,
    FOREIGN KEY (Cust_ID) REFERENCES Customers(Cust_ID) ON DELETE RESTRICT,
    FOREIGN KEY (Inventory_ID) REFERENCES Pokemon_Inventory(Inventory_ID) 
);

CREATE TABLE Sightings(
    PRIMARY KEY (Pokemon_Name, Sightings_Location, Sightings_Time),
    Pokemon_Name VARCHAR(15),
    Sightings_Location VARCHAR(40) NOT NULL,
    Sightings_Time TIMESTAMP NOT NULL,
    Sightings_NumPokemon INT NOT NULL,
    FOREIGN KEY (Pokemon_Name) REFERENCES Pokemon(Pokemon_Name)
);

CREATE TABLE Costumes_Rented(
    Costume_ID INT NOT NULL,
    Staff_ID INT NOT NULL,
    Rental_CheckoutDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Rental_DueDate TIMESTAMP,
    Rental_ReturnedDate TIMESTAMP,
    FOREIGN KEY (Staff_ID) REFERENCES Staff(Staff_ID),
    FOREIGN KEY (Costume_ID) REFERENCES Costumes_Inventory(Costume_ID) 
);