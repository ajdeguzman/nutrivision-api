<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/db_connect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }


    /**
     * Fetching all restaurants
     * 
     */
    public function getAllRestaurants() {
        $stmt = $this->conn->prepare("SELECT * from tbl_resto");
        $stmt->execute();
        $restos = $stmt->get_result();
        $stmt->close();
        return $restos;
    }


    /**
     * Fetching all menus of a restaurant
     * 
     */
    public function getRestaurantMenus($resto_id) {
        $stmt = $this->conn->prepare("SELECT * from tbl_menu WHERE resto_id = ?");
        $stmt->bind_param('i', $resto_id);
        if ($stmt->execute()) {
            $menus = $stmt->get_result();
            $stmt->close();
            return $menus;
        } else {
            return NULL;
        }
    }




}

?>
