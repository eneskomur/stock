<?php 
namespace app;

use Flight;
use app\Sessionfaction as Session;
use app\Logar as Log;
use Exception;

class Customers {

    public function __construct($token)
    {
        $this->user_info = (new Session)->checkSession($token);

        if($this->user_info->success == "false") {
            die(Flight::json($this->user_info));
        }
    }
    
    public function getAllCustomers($offset = 0, $limit = 10, $search = '')
    {
        global $db;
        $response = [];
        $conditions = ['status' => '1', "LIMIT" => [$offset, $limit], "ORDER" => ["update_time" => "DESC"]];
        
        if(strlen(trim($search))>0) {
            $conditions["name[~]"] =  $search;
            $response["recordsFiltered"] = $db->count('customers', ['status' => '1', "name[~]" => $search]);
        }

        $response["data_raw"] = $db->select('customers', '*', $conditions);
        $response["recordsTotal"] = $db->count('customers', ['status' => '1']);
        $response["recordsFiltered"] = isset($response["recordsFiltered"]) ? $response["recordsFiltered"] : $response["recordsTotal"];
        $response["total_pages"] = ceil($response["recordsTotal"] / $limit);
        $response["current_page"] = floor($offset/$limit) + 1;
        
        $response["data"] = [];

        foreach($response["data_raw"] as $data_line) {
            $line = [];  

            $line[] = $data_line["name"];
            $line[] = $data_line["surname"];
            $line[] = $data_line["phone"];
            $line[] = $data_line["email"];
            $line[] = $data_line["update_time"];

            $response["data"][] = $line;
        }

        if(count((array)$response["data"])) {
            $response["success"] = "true";
        }
        else{
            $response["error_code"] = '200';
            $response["error_message"] = 'There is no customer.';
            $response["success"] = "true";
        }
        (new Log)->newLog($this->user_info->id,'list customers',json_encode($response));

        return $response;
    }

    public function getOneCustomer($customer_id)
    {
        global $db;
        $response = [];

        $response["data"] = (object)$db->get('customers', '*', ['id' => $customer_id, 'status' => '1']);

        if(count((array)$response["data"])) {
            $response["success"] = "true";
        }
        else{
            $response["error_code"] = '404';
            $response["error_message"] = 'Not Found';
            $response["success"] = "false";
        }
        (new Log)->newLog($this->user_info->id,'get customer',json_encode($response));
        
        return (object)$response;
    }

    public function editCustomer($customer_id,$query)
    {
        global $db;
        $response = [];
        $changed_datas = [];
        $filters = ['name', 'surname', 'phone', 'email'];

        foreach($filters as $filter) {
            if(isset($query->$filter)) {
                $changed_datas[$filter] = (string)$query->$filter;
            }
        }

        $data = (object)$db->update('customers', $changed_datas, ['id' => $customer_id]);

        if(count((array)$data)) {
            $response["changed_datas"] = (object)$changed_datas;
            $response["success"] = "true";
        }
        else{
            $response["error_code"] = '418';
            $response["error_message"] = 'I\'m a teapot';
            $response["success"] = "false";
        }
        (new Log)->newLog($this->user_info->id,'edit customer',json_encode($response));
        
        return (object)$response;
    }

    public function deleteCustomer($customer_id)
    {
        global $db;
        $response = [];

        $data = (object)$db->update('customers', ['status' => '0'], ['id' => $customer_id ]);

        if(count((array)$data)) {
            $response["success"] = "true";
            $response["customer_id"] = $customer_id;
        }
        else{
            $response["error_code"] = '418';
            $response["error_message"] = 'I\'m a teapot';
            $response["success"] = "false";
        }
        (new Log)->newLog($this->user_info->id,'delete customer',json_encode($response));
        
        return (object)$response;
    }

    public function addCustomer($query)
    {
        global $db;
        $response = [];
        $added_datas = [];
        $filters = ['name', 'surname', 'phone', 'email'];

        foreach($filters as $filter) {
            if(isset($query->$filter)) {
                $added_datas[$filter] = (string)$query->$filter;
            }
        }

        $data = (object)$db->insert('customers', $added_datas);

        if(count((array)$data)) {
            $response["success"] = "true";
            $response["message"] = "Customer added successfully.";
            $response["added_datas"] = (object)$added_datas;
        }
        else{
            $response["error_code"] = '418';
            $response["error_message"] = 'I\'m a teapot';
            $response["success"] = "false";
        }
        (new Log)->newLog($this->user_info->id,'add customer',json_encode($response));
        
        return (object)$response;
    }

}