SELECT *
  FROM Costumes_Rented
 INNER JOIN Staff USING (Staff_ID)
 INNER JOIN Costumes_Inventory USING (Costume_ID);