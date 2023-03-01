<?php 
namespace app;

use Flight;
use app\Sessionfaction as Session;
use app\Logar as Log;
use Exception;

class Sales {

    public function __construct($token)
    {
        $this->user_info = (new Session)->checkSession($token);

        if($this->user_info->success == "false") {
            die(Flight::json($this->user_info));
        }
    }
    
    public function getAllSales($offset = 0, $limit = 10, $search = '')
    {
        global $db;
        $response = [];
        $conditions = ['sales.status' => '1', "LIMIT" => [$offset, $limit], "ORDER" => ["sales.create_time" => "DESC"]];
        
        if(strlen(trim($search))>0) {
            $conditions["customers.name[~]"] =  $search;
            $response["recordsFiltered"] = $db->count('sales', ['[>]customers' => ['customer_id' => 'id']], 'customers.name', ['sales.status' => '1', "customers.name[~]" => $search]);
        }

        $response["data_raw"] = $db->select('sales', ['[>]products' => ['product_id' => 'id'], '[>]customers' => ['customer_id' => 'id']], ['sales.id','sales.quantity','sales.create_time','customers.id(customer_id)','customers.name(customer_name)','customers.surname(customer_surname)','products.id(product_id)','products.name(product_name)'], $conditions);
        //$response["data_raw"] = $db->select('sales', '*', $conditions);
        $response["recordsTotal"] = $db->count('sales', ['status' => '1']);
        $response["recordsFiltered"] = isset($response["recordsFiltered"]) ? $response["recordsFiltered"] : $response["recordsTotal"];
        $response["total_pages"] = ceil($response["recordsTotal"] / $limit);
        $response["current_page"] = floor($offset/$limit) + 1;
        
        $response["data"] = [];

        foreach($response["data_raw"] as $data_line) {
            $line = [];  

            $line[] = $data_line["customer_name"];
            $line[] = $data_line["customer_surname"];
            $line[] = $data_line["product_name"];
            $line[] = $data_line["quantity"];
            $line[] = $data_line["create_time"];

            $response["data"][] = $line;
        }

        if(count((array)$response["data"])) {
            $response["success"] = "true";
        }
        else{
            $response["error_code"] = '200';
            $response["error_message"] = 'There is no product.';
            $response["success"] = "true";
        }
        (new Log)->newLog($this->user_info->id,'list sales',json_encode($response));

        return $response;
    }

    public function getOneSale($sale_id)
    {
        global $db;
        $response = [];

        $response["data"] = (object)$db->get('sales', '*', ['id' => $sale_id, 'status' => '1']);

        if(count((array)$response["data"])) {
            $response["success"] = "true";
        }
        else{
            $response["error_code"] = '404';
            $response["error_message"] = 'Not Found';
            $response["success"] = "false";
        }
        (new Log)->newLog($this->user_info->id,'get sale',json_encode($response));
        
        return (object)$response;
    }

    public function deleteSale($sale_id)
    {
        global $db;
        $response = [];

        $data = (object)$db->update('sales', ['status' => '0'], ['id' => $sale_id]);

        if(count((array)$data)) {
            $response["success"] = "true";
            $response["product_id"] = $sale_id;
        }
        else{
            $response["error_code"] = '418';
            $response["error_message"] = 'I\'m a teapot';
            $response["success"] = "false";
        }
        (new Log)->newLog($this->user_info->id,'delete sale',json_encode($response));
        
        return (object)$response;
    }

    public function addSale($query)
    {
        global $db;
        $response = [];
        $added_datas = [];
        $filters = ['customer_id', 'product_id', 'quantity'];

        foreach($filters as $filter) {
            if(isset($query->$filter)) {
                $added_datas[$filter] = (string)$query->$filter;
            }
        }

        $data = (object)$db->insert('sales', $added_datas);        

        if(count((array)$data)) {
            $product = $db->get('products', ['quantity'], ['id' => $added_datas['product_id']]);
            $db->update('products', ['quantity' => $product['quantity'] - $added_datas['quantity']], ['id' => $added_datas['product_id']]);

            $response["success"] = "true";
            $response["message"] = "Your sale added successfully.";
            $response["added_datas"] = (object)$added_datas;
        }
        else{
            $response["error_code"] = '418';
            $response["error_message"] = 'I\'m a teapot';
            $response["success"] = "false";
        }
        (new Log)->newLog($this->user_info->id,'add sale',json_encode($response));
        
        return (object)$response;
    }

}