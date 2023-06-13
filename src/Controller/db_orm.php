<?php



$tablename = "ps_product_chronology";

class viewed_products_orm {

    
    static public function createTable(){
        global $tablename;

        $sql = "CREATE TABLE IF NOT EXISTS $tablename (
            User INT(10) unsigned,
            id INT(11) unsigned
        );";

        return DB::getInstance()->execute($sql);

    }

    static public function deleteTable(){
        //global $tablename;
        //$sql = "DROP TABLE $tablename";

        //return DB::getInstance()->execute($sql);
        return true;
    }


};