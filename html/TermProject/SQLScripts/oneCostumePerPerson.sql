--triggere for costume to only be rented by one person at a time

 CREATE TRIGGER onePerCostume
    RETURN trigger
    RETURN (
     BEFORE INSERT ON Costumes_Rented FOR EACH row
        IF SELECT COUNT(Costume_ID) FROM Costumes_Rented = 1
            RAISE_APPLICATION_ERROR('Error 45000 The costume is already being rented')
        );