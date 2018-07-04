-- Low stock levels
SELECT 
    sup.supplement_id, 
    supd.supplement_description,
    sup.cost_excl, 
    sup.cost_incl, 
    sup.perc_inc, 
    sup.cost_client, 
    supl.supplier_name,
    sup.min_levels,
    sup.stock_levels,
    sup.nappi_code
FROM
    supplements sup
        LEFT JOIN supplement_descriptions supd ON sup.description_id = supd.description_id
        LEFT JOIN suppliers supl ON sup.supplier_id = supl.supplier_id
WHERE
    sup.stock_levels <= sup.min_levels

-- Top 10 sold supplements
SELECT 
	inv.supplement_id, 
    supd.supplement_description,
    SUM(inv.quantity) AS total_quantity_sold,
    SUM(sup.cost_client) AS total_cost_client
FROM 
	invoice_lines inv 
		LEFT JOIN supplements sup ON inv.supplement_id = sup.supplement_id
        LEFT JOIN supplement_descriptions supd ON sup.description_id = supd.description_id
GROUP BY 
	supplement_id 
ORDER BY 
	total_quantity_sold DESC
LIMIT 
	10;

