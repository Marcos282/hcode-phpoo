<?php


namespace Hcode\Model;

use Hcode\DB\Sql;
use Hcode\Model;

class OrderStatus extends Model {
    
 
    Const ST_EMABERTO = 1;
    Const ST_AGUARDANDOPAGAMENTO = 2;
    Const ST_PAGO = 3;
    Const ST_ENTREGUE = 4;

    public static function listAll()  {
        
        $sql = new Sql(CONFIG_DB_ECOMERCE);
        
        return $sql->select("SELECT * from tb_ordersstatus");
        
    }

} 


?>

