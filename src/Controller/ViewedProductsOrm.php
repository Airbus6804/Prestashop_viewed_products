<?php





class ViewedProductsOrm
{

    
    static public $tablename = "ps_product_chronology";


    static public function createTable()
    {
        $tablename = ViewedProductsOrm::$tablename;

        $sql = "CREATE TABLE IF NOT EXISTS $tablename (
            User INT(10) unsigned,
            id INT(11) unsigned,
            ROWID int NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (ROWID)
        );";

        return DB::getInstance()->execute($sql);

    }

    static public function deleteTable()
    {
        $tablename = ViewedProductsOrm::$tablename;
        $sql = "DROP TABLE $tablename";

        return DB::getInstance()->execute($sql);
        //return true;
    }

    static public function insertViewedProduct(int $customer, int $id)
    {
        $tablename = ViewedProductsOrm::$tablename; 
        
        $delete = "DELETE FROM $tablename WHERE User=$customer AND id=$id";
        $insert = "INSERT INTO $tablename (User, id) VALUES ($customer, $id);";


        $db = Db::getInstance();

        $db->execute($delete);

        return $db->execute($insert);

    }

    static public function getViewedProductsByCustomerId(int $customer)
    {
        $tablename = ViewedProductsOrm::$tablename;

        //get the products in a chronologic descending order
        $sql = "SELECT id FROM $tablename WHERE User=$customer ORDER BY ROWID DESC" ;
        
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql) ;

    }


}
;