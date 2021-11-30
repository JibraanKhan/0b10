

CREATE FUNCTION num_costumes_rented (st_id INT)
RETURNS INT
RETURN (
SELECT COUNT(Staff_ID)
FROM Costumes_Rented
WHERE Staff_ID = st_id AND Rental_ReturnedDate ='0000-00-00 00:00:00'
);

CREATE FUNCTION is_costume_rented (c_id INT)
RETURNS INT 
RETURN (
SELECT COUNT(Costume_ID)
FROM Costumes_Rented
WHERE Costume_ID = c_id AND Rental_ReturnedDate ='0000-00-00 00:00:00'
);


DELIMITER //

--trigger to ensure each sighting has at least one pokemon
CREATE TRIGGER check_num_pokemon
BEFORE INSERT ON Sightings FOR EACH ROW
IF NEW.Sightings_NumPokemon <1 THEN 
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Each sighting must have at least one Pokemon!';
END IF;
//

--trigger to ensure sighting date is before today's date
CREATE TRIGGER check_sighting_date
BEFORE INSERT ON Sightings FOR EACH ROW
IF NEW.Sightings_Time > CURRENT_TIMESTAMP THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'This is an impossible date!';
END IF;
//


--trigger to ensure the costume rental checkout date is before the return date
CREATE TRIGGER check_due_date
BEFORE INSERT ON Costumes_Rented FOR EACH ROW
IF NEW.Rental_CheckoutDate > NEW.Rental_DueDate THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The checkout date must be before the due date!';
END IF;
//

CREATE TRIGGER check_return_date
BEFORE INSERT ON Costumes_Rented FOR EACH ROW
IF NEW.Rental_CheckoutDate > NEW.Rental_ReturnedDate AND NEW.Rental_ReturnedDate != '0000-00-00 00:00:00' THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'The checkout date must be before the returned date!';
END IF;
//


--trigger to ensure staff does not check out more than 5 costumes each at a time
CREATE TRIGGER check_max_costumes
BEFORE INSERT ON Costumes_Rented FOR EACH ROW
IF num_costumes_rented(NEW.staff_id) >= 5 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Each staff member may only check out 5 costumes at once!';
END IF;
//

--trigger to make sure a costume is only rented by one staff member at a time
CREATE TRIGGER one_staff_per_costume
BEFORE INSERT ON Costumes_Rented FOR EACH ROW
IF is_costume_rented(NEW.Costume_ID) = 1 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Costume is already rented!';
END IF;
//

--trigger to make sure the price of the pokemon is greater than zero
CREATE TRIGGER sold_for_positive
BEFORE INSERT ON Orders FOR EACH ROW
IF NEW.Order_SoldFor < 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Price must be at least zero!';
END IF;
//

CREATE TRIGGER price_positive
BEFORE INSERT ON Pokemon_Inventory FOR EACH ROW
IF NEW.Pokemon_Price < 0 THEN
SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Price must be at least zero!';
END IF;
//

INSERT INTO Pokemon (Pokemon_Name,Pokemon_Type)
    VALUES ('Charmander', 'fire');

INSERT INTO Sightings (Pokemon_Name, Sightings_Location, Sightings_Time,Sightings_NumPokemon)
    VALUES ('Charmander', 'louisville', '2021-02-03 00:00:01', 2);

SELECT * FROM Pokemon;  
SELECT * FROM Sightings;

INSERT INTO Sightings (Pokemon_Name, Sightings_Location, Sightings_Time,Sightings_NumPokemon)
    VALUES ('Charmander', 'lexington', '2021-02-03 00:00:01', 1);

INSERT INTO Staff (Staff_FirstName,Staff_LastName)
    VALUES ('Shelby','Young'),
           ('Jibraan', 'Kaan'),
           ('Tim','Smith');

INSERT INTO Costume_Types (Costume_Type)
    VALUES ('Shirt'),
           ('Hat'),
           ('Pants'),
           ('Shoes');

INSERT INTO Costumes_Inventory (Costume_Type,Costume_Size)
    VALUES ('Shirt','S'),
           ('Pants','L'),
           ('Hat','L'),
           ('Shirt','L'),
           ('Pants','S'),
           ('Shoes', 'S');

INSERT INTO Costumes_Rented(Costume_ID,Staff_ID,Rental_DueDate)
    VALUES (1,1,'2021-12-15 00:00:01'),
           (2,1,'2021-12-15 00:00:01'),
           (3,1,'2021-12-15 00:00:01'),
           (4,1,'2021-12-15 00:00:01');
           

SELECT * FROM Sightings;
SELECT * FROM Costumes_Rented;

SELECT num_costumes_rented(1);