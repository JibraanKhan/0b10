UPDATE Costumes_Rented
    SET Rental_CheckoutDate = ?,
        Rental_DueDate= ?,
        Rental_ReturnedDate = ?
  WHERE Costume_ID = ? AND Staff_ID = ?;

