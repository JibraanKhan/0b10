CREATE TRIGGER Positive_Price_Value
BEFORE INSERT ON Orders FOR EACH ROW 
    IF( new.Order_SoldFor < 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Price cannot be below Zero';
    END IF;