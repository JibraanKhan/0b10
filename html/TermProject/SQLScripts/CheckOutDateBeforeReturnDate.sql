CREATE TRIGGER CheckedOutBeforeReturned
BEFORE INSERT ON Costumes_Rented FOR EACH ROW 
    IF( new.Rental_CheckoutDate < new.Rental_RetunedDate) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Checkout Date must come before Return Date';
    END IF;