SELECT * 
  FROM Orders
 INNER JOIN Customers USING (Cust_ID)
 INNER JOIN Pokemon_Inventory USING (Inventory_ID);