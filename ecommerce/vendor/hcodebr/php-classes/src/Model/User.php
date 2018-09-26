<?php 

namespace Hcode\Model;

use \Hcode\Model;
use \Hcode\Model\User;
use \Hcode\DB\Sql;

class User extends Model {

	const SESSION = "User";
	const SECRET = "HcodePhp7_Secret";
	const SESSION_ERROR = "UserError";
	const SESSION_REGISTER_ERROR = "UserRegisterError";

	public static function getFromSession()
	{
		$user = new User();

		if (isset($_SESSION[User::SESSION]) && (int) $_SESSION[User::SESSION]['iduser']) {
			
			$user->setData($_SESSION[User::SESSION]);

		}

		return $user;
	}

	public static function checkLogin($inadmin = true)
	{
		if (
			!isset($_SESSION[User::SESSION])
			|| 
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		) {

			// Não está logado
			return false;

		} else {

			if ($inadmin === true && $_SESSION[User::SESSION]["iduser"] === true) {

				return true;

			} else if ($inadmin === false) {
				 
				return true;
				
			} else {

				return false;

			}

		}
	}

	public static function login($login, $password):User
	{

		$db = new Sql();

		$results = $db->select("
			select *
			from tb_users a
			inner join tb_persons b using(idperson)
			WHERE deslogin = :LOGIN", array(
			":LOGIN"=>$login
		));

		if (count($results) === 0) {
			throw new \Exception("Não foi possível fazer login.");
		}

		$data = $results[0];

		if (password_verify($password, $data["despassword"])) {

			$user = new User();
			
			$data['desperson'] = utf8_encode($data['desperson']);

			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else {

			throw new \Exception("Não foi possível fazer login.");

		}

	}

	public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}

	public static function verifyLogin($inadmin = true)
	{

		if (!User::checkLogin($inadmin)) {
			if ($inadmin) {
				header("Location: /admin/login");
			} else {
				header("Location: /login");
			}
			exit;
		}

	}

	public static function listAll() {
		$sql = new Sql();

		return $sql->select("select * from tb_users a inner join tb_persons b USING(idperson) order by b.desperson");
	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);
		
	}

	public function get($iduser)
	{
		$sql = new Sql();

		$results = $sql->select("select * from tb_users a inner join tb_persons b using(idperson) where a.iduser = :iduser", array(
			"iduser"=>$iduser
		));

		$data = $results[0];

		$data['desperson'] = utf8_encode($data['desperson']);

		$this->setData($data);
	}

	public function update()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
			":desperson"=>utf8_decode($this->getdesperson()),
			":deslogin"=>$this->getdeslogin(),
			":despassword"=>User::getPasswordHash($this->getdespassword()),
			":desemail"=>$this->getdesemail(),
			":nrphone"=>$this->getnrphone(),
			":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);
		

	}

	public function delete()
	{

		$sql = new Sql();

		$sql->query("call sp_users_delete(:iduser)", array(
			":iduser"=>$this->getiduser()
		));
		
	}

	public static function getForgot($email, $inadmin = true)
	{

		$sql = new Sql();

		$results = $sql->select("
			select *
			from tb_persons a
			inner join tb_users b using(idperson)
			where a.desemail = :email	
		", array(
			":email" => $email
		));

		if (count($results) === 0) {
			throw new \Exception("Não foi possível recuperar a senha.");
		} else {
			$results1 = $sql->select("call sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data["iduser"],
				":desip"=>$_SERVER["REMOTE_ADDR"]
			));

			if (count($results2) === 0)
			{
				throw new \Exception("Não foi possível recuperar a senha.");
			}
			else
			{
				$dataRecovery = $results2[0];
				$iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
				$code = openssl_encrypt($dataRecovery['idrecovery'], 'aes-256-cbc', User::SECRET, 0, $iv);
				$result = base64_encode($iv.$code);

				if ($inadmin === true) {
					$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$result";
				} else {
					$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$result";
				} 
				$mailer = new Mailer($data['desemail'], $data['desperson'], "Redefinir senha da Hcode Store", "forgot", array(
					"name"=>$data['desperson'],
					"link"=>$link
				)); 
				$mailer->send();
				return $link;
			}
		}
	}

	public static function validForgotDecrypt($result)
	{
		$result = base64_decode($result);
		$code = mb_substr($result, openssl_cipher_iv_length('aes-256-cbc'), null, '8bit');
		$iv = mb_substr($result, 0, openssl_cipher_iv_length('aes-256-cbc'), '8bit');;
		$idrecovery = openssl_decrypt($code, 'aes-256-cbc', User::SECRET, 0, $iv);
		$sql = new Sql();
		$results = $sql->select("
			SELECT *
			FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE
			a.idrecovery = :idrecovery
			AND
			a.dtrecovery IS NULL
			AND
			DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array(
			":idrecovery"=>$idrecovery
		));
		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{
			return $results[0];
		}
	}

	public static function setForgotUsed($idrecovery) {
		$sql = new Sql();

		$sql->query("update tb_userspasswordsrecoveries set dtrecovery = now() where idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
		));
	}

	public function setPassword($password)
	{

		$sql = new Sql();

		$sql->query("update tb_users set despassword = :password where iduser = :iduser", array(
			":password"=>$password,
			":iduser"=>$this->getiduser()
		));
	}

	public static function setError($msg)
	{
		$_SESSION[User::SESSION_ERROR] = $msg;
	}

	public static function getError()
	{
		$msg = isset($_SESSION[User::SESSION_ERROR]) ? $_SESSION[User::SESSION_ERROR] : "";
		User::clearError();
		return $msg;
	}

	public static function clearError()
	{
		$_SESSION[User::SESSION_ERROR] = NULL;
	}

	public static function setRegisterError($msg)
	{
		$_SESSION[User::SESSION_REGISTER_ERROR] = $msg;
	}

	public static function getRegisterError()
	{
		$msg = isset($_SESSION[User::SESSION_REGISTER_ERROR]) ? $_SESSION[User::SESSION_REGISTER_ERROR] : "";
		User::clearError();
		return $msg;
	}

	public static function clearRegisterError()
	{
		$_SESSION[User::SESSION_REGISTER_ERROR] = NULL;
	}

	public static function checkLoginExist($login)
	{
		$sql = new Sql();

		$results = $sql->select("select * from tb_users where deslogin = :deslogin", [
			':deslogin'=>$login
		]);

		return (count($results) > 0);
	}

	public static function getPasswordHash($password)
	{
		return password_hash($password, PASSWORD_DEFAULT, [
			'cost'=>12
		]);
	}

}

 ?>