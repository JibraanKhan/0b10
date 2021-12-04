INSERT INTO Pokemon (Pokemon_Name,Pokemon_Type)
    VALUES ('Charmander', 'Fire'),
           ('Bulbasaur', 'Grass'),
           ('Tododile', 'Water'),
           ('Pikachu', 'Lightning'),
           ('Cyndaquil', 'Fire'),
           ('Geodude', 'Rock'),
           ('Onix', 'Rock'),
           ('Squirtle', 'Water');

INSERT INTO Costume_Types (Costume_Type) 
     VALUES ('Meowth_Costume'),
            ('Officer_Jenny_Costume'),
            ('Punk_Rocker_Costume'),
            ('Magikarp_Costume'),
            ('Gyrados_Costume'),
            ('Squirtle_Costume'),
            ('Bush_Costume'),
            ('Tree_Costume'),
            ('Wall_Costume');

INSERT INTO Costumes_Inventory (Costume_Type, Costume_Size)
     VALUES ('Meowth_Costume', 'XL'),
            ('Punk_Rocker_Costume', 'M'),
            ('Gyrados_Costume', 'S'),
            ('Officer_Jenny_Costume', 'L'),
            ('Bush_Costume', 'XXL'),
            ('Tree_Costume', 'XXXL');

INSERT INTO Staff (Staff_FirstName, Staff_LastName) 
     VALUES ('Jibraan', 'Khan'),
            ('Selam', 'Van_Voorhis'),
            ('Shelby', 'Young'),
            ('John', 'Smith'),
            ('William', 'Bailey'),
            ('Thomas', 'Allen');

INSERT INTO Customers (Cust_FirstName, Cust_LastName, Cust_Address, Cust_Phone) 
     VALUES ('Eli', 'Gooch', '500_West_Mainstreet,_Stanford,_KY', '502-993-1102'),
            ('Muhammad', 'Mujitaba', '600_West_Mainstreet,_Indianapolis,_IN', '492-331-1302'),
            ('John', 'Doe', '700_West_Mainstreet,_New_York_City,_NY', '600-212-1502'),
            ('AJ', 'Smith', '800_West_Mainstreet,_Kansas_City,_Missouri', '301-111-9572'),
            ('Lam', 'Le', '900_West_Mainstreet,_Asheville,_NC', '921-556-1251');

INSERT INTO Pokemon_Inventory(Pokemon_Name, Pokemon_Price)
    VALUES ('Charmander',150),
           ('Bulbasaur',150),
           ('Geodude',30),
           ('Cyndaquil',300),
           ('Geodude',40),
           ('Onix',900);

INSERT INTO Orders (Pokemon_Name,Cust_ID,Inventory_ID,Order_SoldFor)
    VALUES ('Bulbasaur',1,2,150),
           ('Pikachu',1,NULL,NULL),
           ('Tododile',1,NULL,NULL),
           ('Geodude',1,NULL,NULL),
           ('Cyndaquil',2,4,500),
           ('Onix',4,6,950);

INSERT INTO Costumes_Rented (Costume_ID, Staff_ID, Rental_CheckoutDate, Rental_DueDate, Rental_ReturnedDate)
     VALUES (1, 1, "2019-12-04 04:34:10", NULL, NULL),
            (4, 2, "2031-12-04 04:34:10", "2031-12-09 04:34:10", NULL),
            (5, 3, "2011-09-04 12:45:00", "2025-06-015 08:54:10", NULL),
            (6, 3, "2011-09-04 12:45:00", "2031-12-09 04:34:10", "2030-12-09 04:34:10");
            


INSERT INTO Sightings (Pokemon_Name, Sightings_Location, Sightings_Time,Sightings_NumPokemon)
    VALUES ('Charmander', 'Route_12', '2021-02-03 00:00:00', 2),
           ('Pikachu', 'Route_16', '2021-08-19 10:30:00', 9),
           ('Cyndaquil', 'Route_21', '2000-04-21 20:45:00', 4),
           ('Onix', 'Route_23', '2015-06-22 23:20:00', 1),
           ('Squirtle', 'Route_11', '2017-03-14 13:35:00', 2);