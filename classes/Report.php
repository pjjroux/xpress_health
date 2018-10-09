<?php
/*
|--------------------------------------------------------------------------
| Report class - Represents a report on the website 
|--------------------------------------------------------------------------
|
| Author:         Jaco Roux
| Studentnumber:  5376-553-2
| Date:           2018-10-05
|
*/

// Include database class  
require_once('Database.php'); 

class Report {
    protected $report_data, $database; 

    public function __construct($report_type = null) {
        $this->database = new Database(); 
    }

    /**
     * Low stock levels
     * Get supplements with stock levels below the min levels
     * 
     * @return array $report_data Report data
     */
    public function get_low_stock_levels() {
        $this->database->query('
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
            ORDER BY
            sup.min_levels - sup.stock_levels DESC;
        ');
        $rows = $this->database->resultset();

        $this->report_data = $rows;
        
        return $this->report_data;
    }

    /**
     * Top 10 sold supplements
     * 
     * @return array $report_data Report data
     */
    public function get_top_10_sold() {
        $this->database->query('
            SELECT 
                inv.supplement_id, 
                supd.supplement_description,
                SUM(inv.quantity) AS total_quantity_sold,
                sup.cost_client,
                (sup.cost_client * SUM(inv.quantity)) AS total_sold
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
        ');
        $rows = $this->database->resultset();

        $this->report_data = $rows;  

        return $this->report_data;
    }


    /**
     * Transactions for range from - to in format: (YYYY-MM-DD)
     * 
     * @param string $date_from Starting date of range
     * @param string $date_to Ending date of range
     * @return array $report_data Report data
     */
    public function get_sold_in_range($date_from, $date_to) {
        $this->database->query('
            SELECT
                inv.inv_num,
                inv.inv_date,
                inv.client_id,
                cl.client_name,
                cl.client_surname,
                inv.grand_total,
                invl.supplement_id,
                supd.supplement_description,
                invl.quantity,
                invl.total
            FROM
                invoice_lines invl
                    LEFT JOIN invoices inv ON invl.inv_num = inv.inv_num
                    LEFT JOIN supplements sup ON invl.supplement_id = sup.supplement_id
                    LEFT JOIN clients cl ON inv.client_id = cl.client_id
                    LEFT JOIN supplement_descriptions supd ON sup.description_id = supd.description_id
            WHERE
                inv.inv_date >= :date_from AND inv.inv_date <= :date_to
            ORDER BY 
                inv.inv_date
        ');
        $this->database->bind(':date_from', $date_from);
        $this->database->bind(':date_to', $date_to);
        $rows = $this->database->resultset();

        $this->report_data = $rows;  

        return $this->report_data;
    }

    /**
     * Suppliers by most supplied supplements
     * 
     * @return array $report_data Report data
     */
    public function top_suppliers() {
        $this->database->query('
        SELECT	
            sup.supplier_id,
            sup.supplier_name,
            COUNT(supl.supplier_id) as number_supplements_supplied    
        FROM	
            suppliers sup
                LEFT JOIN supplements supl ON sup.supplier_id = supl.supplier_id
        GROUP BY
            supl.supplier_id
        ORDER BY
            number_supplements_supplied DESC
        ');
        $rows = $this->database->resultset();

        $this->report_data = $rows;  

        return $this->report_data;
    }

    /**
     * Top 10 most profitable supplements
     * 
     * @return array $report_data Report data
     */
    public function top_10_profit() {
        $this->database->query('
        SELECT
            supl.supplement_id,
            supd.supplement_description,
            supl.cost_incl,
            supl.cost_client,
            supl.perc_inc AS profit
        FROM
            supplements supl
                LEFT JOIN supplement_descriptions supd ON supl.description_id = supd.description_id
        ORDER BY 
            profit DESC,
            supl.cost_client DESC
        LIMIT 
            10
        ');
        $rows = $this->database->resultset();

        $this->report_data = $rows;  

        return $this->report_data;
    }


}


?>
