
############################## QNS  NO 1 #######

SELECT cu.name AS CustomerName, COUNT(c.product_id) AS TotalNumberOfPurchases,SUM(p.cost) As TotalAmountSpent, AVG(p.cost) As AverageSpent
FROM customers cu 
LEFT JOIN customer_products c
ON c.customer_id = cu.customer_id
LEFT JOIN products p
ON c.product_id = p.product_id
GROUP BY CustomerName



############################## QNS NO 2 ##########

SELECT purchase_date AS PurchaseDate,COUNT(c.product_id) AS TotalNosOfPurchasesPerDay, sum(p.cost) As TotalAmountSpentPerday, MIN(p.cost) As MinAmoutSpentPerday,MAX(p.cost) As MaxAmoutSpentPerday 
FROM products p 
JOIN customer_products c ON c.product_id = p.product_id
GROUP BY PurchaseDate

############################## QNS NO 3 ####################################

SELECT p.product_category As Category,SUM(p.cost) As TotalSales,COUNT(c.product_id) As NumberOfItemsSold
FROM `products` p 
JOIN customer_products c ON c.product_id = p.product_id
GROUP BY category





