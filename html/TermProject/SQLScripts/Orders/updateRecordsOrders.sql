UPDATE Orders 
   SET Pokemon_Name = ?,
       Cust_ID = ?,
       Inventory_ID = ?,
       Order_SoldFor = ?
 WHERE Order_ID = ?;