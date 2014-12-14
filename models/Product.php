<?php

class Product{
    public $id;
    public $name;
    public $configuration;
    
    public function __construct($id, $name, $configuration) {
        $this->id = $id;
        $this->name = $name;
        $this->configuration = $configuration;
    }
    
    public static function ProductExists($idProduct) {
            $query = "SELECT COUNT(*) ";
            $query .= " FROM product";
            $query .= " WHERE id=:id";

            $stmt = $GLOBALS["dbconnec"]->prepare($query);

            $stmt->execute(array(":id" => $idProduct));
            $row = $stmt->fetch(PDO::FETCH_COLUMN, 0);
            $stmt = NULL;

            return (intval($row[0]) ? TRUE : FALSE);
    }
    
    public static function getProduct($ids){
        $result = array();
        
        // On vérifie le type d'arguement passé en paramètre
        if (!is_array($ids)) {
            $ids = array($ids);
            $return_array = FALSE;
        } else {
            if (count($ids) < 1) {
                    return $result;
            }
            // On supprime les doublons
            $ids = array_unique($ids);
            $return_array = TRUE;
        }
        
        $query = "SELECT ";
        $query .= "id";
        $query .= ",name";
        $query .= ",configuration";
        $query .= " FROM product ";
        $query .= " WHERE id=:id";
        
        $stmt = $GLOBALS["dbconnec"]->prepare($query);
        
        // On récupère les élèments
        foreach ($ids as $id) {
            $params = array(":id"	=> $id);
            $stmt->execute($params);
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $tmp_product = new Product($row['id'],$row['name'],$row['configuration']);

                    $result[] = $tmp_product;
                    $tmp_product = NULL;
            }
        }
        $stmt = NULL;
        return ($return_array ? $result : $result[0]);
    }
    
    /**
    * @desc Renvoie tous les Products
    */
    public static function getProducts() {
        $query = "SELECT id FROM product ORDER BY name";
        
        $stmt = $GLOBALS["dbconnec"]->query($query);

        return self::getProduct($stmt->fetchAll(PDO::FETCH_COLUMN, 0));
    }
}
?>
