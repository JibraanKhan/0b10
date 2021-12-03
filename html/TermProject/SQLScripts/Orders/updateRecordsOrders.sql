UPDATE Orders 
   SET Cust_ID = ?,
       Inventory_ID = ?,
       Pokemon_Name = ?,
       Order_SoldFor = ?
 WHERE Order_ID = ?;