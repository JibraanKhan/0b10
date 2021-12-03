SELECT *
  FROM Costumes_Rented
 LEFT JOIN Staff USING (Staff_ID)
 LEFT JOIN Costumes_Inventory USING (Costume_ID);