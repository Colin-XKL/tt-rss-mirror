<?php
class Db
{
	/* @var Db $instance */
	private static $instance;

	private $link;

	/* @var PDO $pdo */
	private $pdo;

	private function __clone() {
		//
	}

	// this really shouldn't be used unless a separate PDO connection is needed
	// normal usage is Db::pdo()->prepare(...) etc
	public function pdo_connect() {

		$db_port = Config::get(Config::DB_PORT) ? ';port=' . Config::get(Config::DB_PORT) : '';
		$db_host = Config::get(Config::DB_HOST) ? ';host=' . Config::get(Config::DB_HOST) : '';

		try {
			$pdo = new PDO(Config::get(Config::DB_TYPE) . ':dbname=' . Config::get(Config::DB_NAME) . $db_host . $db_port,
				Config::get(Config::DB_USER),
				Config::get(Config::DB_PASS));
		} catch (Exception $e) {
			print "<pre>Exception while creating PDO object:" . $e->getMessage() . "</pre>";
			exit(101);
		}

		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if (Config::get(Config::DB_TYPE) == "pgsql") {

			$pdo->query("set client_encoding = 'UTF-8'");
			$pdo->query("set datestyle = 'ISO, european'");
			$pdo->query("set TIME ZONE 0");
			$pdo->query("set cpu_tuple_cost = 0.5");

		} else if (Config::get(Config::DB_TYPE) == "mysql") {
			$pdo->query("SET time_zone = '+0:0'");

			if (Config::get(Config::MYSQL_CHARSET)) {
				$pdo->query("SET NAMES " . Config::get(Config::MYSQL_CHARSET));
			}
		}

		return $pdo;
	}

	public static function instance() {
		if (self::$instance == null)
			self::$instance = new self();

		return self::$instance;
	}

	public static function pdo() : PDO {
		if (self::$instance == null)
			self::$instance = new self();

		if (!self::$instance->pdo) {
			self::$instance->pdo = self::$instance->pdo_connect();
		}

		return self::$instance->pdo;
	}

	public static function sql_random_function() {
		if (Config::get(Config::DB_TYPE) == "mysql") {
			return "RAND()";
		} else {
			return "RANDOM()";
		}
	}

}
