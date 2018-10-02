<?php

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Cart;

class Order extends Model {

    const SESSION_ERROR = "OrderError";
	const SESSION_SUCCESS = "OrderSuccess";

    public function save() {
        $sql = new Sql();

        $results = $sql->select("
            call sp_orders_save(
                :idorder,
                :idcart,
                :iduser,
                :idstatus,
                :idaddress,
                :vltotal)
            ", [
                'idorder'=>$this->getidorder(),
                'idcart'=>$this->getidcart(),
                'iduser'=>$this->getiduser(),
                'idstatus'=>$this->getidstatus(),
                'idaddress'=>$this->getidaddress(),
                'vltotal'=>$this->getvltotal()
            ]);

        if (count($results) > 0) {
            $this->setData($results[0]);
        }
    }

    public function get($idorder) {
        $sql = new Sql();

        $results = $sql->select("
            select *
            from tb_orders a
            inner join tb_ordersstatus b using(idstatus)
            inner join tb_carts c using(idcart)
            inner join tb_users d on d.iduser = a.iduser
            inner join tb_addresses e using(idaddress)
            inner join tb_persons f on f.idperson = d.idperson
            where a.idorder = :idorder
        ", [
            ':idorder'=>$idorder
        ]);

        if (count($results) > 0) {
            $this->setData($results[0]);
        }
    }

    public static function listAll() {
        $sql = new Sql();

        return $sql->select("
            select *
            from tb_orders a
            inner join tb_ordersstatus b using(idstatus)
            inner join tb_carts c using(idcart)
            inner join tb_users d on d.iduser = a.iduser
            inner join tb_addresses e using(idaddress)
            inner join tb_persons f on f.idperson = d.idperson
            order by a.dtregister desc
        ");
    }

    public function  delete() {
        $sql = new Sql();

        $sql->query("
            delete from tb_orders
            where idorder = :idorder
        ", [
            ':idorder'=>$this->getidorder()
        ]);
    }

    public function getCart() : Cart {

        $cart = new Cart();

        $cart->get((int) $this->getidcart());

        return $cart;

    }

    public static function setError($msg)
	{
		$_SESSION[Order::SESSION_ERROR] = $msg;
	}

	public static function getError()
	{
		$msg = isset($_SESSION[Order::SESSION_ERROR]) ? $_SESSION[Order::SESSION_ERROR] : "";
		Order::clearError();
		return $msg;
	}

	public static function clearError()
	{
		$_SESSION[Order::SESSION_ERROR] = NULL;
	}

	public static function setSuccess($msg)
	{
		$_SESSION[Order::SESSION_SUCCESS] = $msg;
	}

	public static function getSuccess()
	{
		$msg = isset($_SESSION[Order::SESSION_SUCCESS]) ? $_SESSION[Order::SESSION_SUCCESS] : "";
		Order::clearSuccess();
		return $msg;
	}

	public static function clearSuccess()
	{
		$_SESSION[Order::SESSION_SUCCESS] = NULL;
    }
    
    public static function getPage($page = 1, $itemsPerPage = 8)
	{
		$start = ($page - 1) * $itemsPerPage;
		$sql = new Sql();

		$results = $sql->select("
			select sql_calc_found_rows *
			from tb_orders a
            inner join tb_ordersstatus b using(idstatus)
            inner join tb_carts c using(idcart)
            inner join tb_users d on d.iduser = a.iduser
            inner join tb_addresses e using(idaddress)
            inner join tb_persons f on f.idperson = d.idperson
            order by a.dtregister desc
			limit $start, $itemsPerPage
		");

		$resultTotal = $sql->select("select found_rows() as nrtotal");

		return [
			'data'=>$results,
			'total'=>(int) $resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}

	public static function getPageSearch($search, $page = 1, $itemsPerPage = 8)
	{
		$start = ($page - 1) * $itemsPerPage;
		$sql = new Sql();

		$results = $sql->select("
			select sql_calc_found_rows *
			from tb_orders a
            inner join tb_ordersstatus b using(idstatus)
            inner join tb_carts c using(idcart)
            inner join tb_users d on d.iduser = a.iduser
            inner join tb_addresses e using(idaddress)
            inner join tb_persons f on f.idperson = d.idperson
            where a.idorder = :id
               or f.desperson like :search
            order by a.dtregister desc
			limit $start, $itemsPerPage
		", [
            'id'=>$search,
			':search'=>'%'.$search.'%'
		]);

		$resultTotal = $sql->select("select found_rows() as nrtotal");

		return [
			'data'=>$results,
			'total'=>(int) $resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}

}

?>