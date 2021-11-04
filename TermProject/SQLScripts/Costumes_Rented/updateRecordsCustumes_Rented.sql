UPDATE Costumes_Rented
    SET Staff_ID = ?
    SET Rental_CheckoutDate = ?
    SET Rental_DueDate= ?
    SET Rental_ReturnedDate
WHERE Costume_ID = ?;

