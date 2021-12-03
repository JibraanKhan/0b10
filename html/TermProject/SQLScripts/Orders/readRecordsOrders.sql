SELECT * 
  FROM Orders
 LEFT JOIN Customers USING (Cust_ID)
 LEFT JOIN Pokemon_Inventory USING (Inventory_ID);