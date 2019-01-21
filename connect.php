<?

$ip = $_SERVER['REMOTE_ADDR'];

$root = [
	'root ip'
];

$view = [
	'view ip'
];

$permission = in_array($ip, $root) ? 2 : ((in_array($ip, $view) or in_array(mb_substr($ip, 0, 8), $view)) ? 1 : 0);

if (isset($_GET['action'])) {
	if ($_GET['action'] === 'permission') {
		echo $permission;
	} elseif ($permission != 0) {
		$umk = new Umk();

		if ($_GET['action'] === 'get_all' and $permission > 1) {
			echo json_encode($umk->get_all());
		} elseif ($_GET['action'] === 'get_trash' and $permission > 1) {
			echo json_encode($umk->get_trash());
		} elseif ($_GET['action'] === 'get_short') {
			echo json_encode($umk->get_short());
		} elseif ($_GET['action'] === 'get_report' and $permission > 1) {
			echo json_encode($umk->get_report());
		} 
	}

	exit;
}

$post = $permission > 1 ? json_decode(file_get_contents('php://input')) : NULL;

if (isset($post->form)) {
	$umk = new Umk();

	echo json_encode($umk->edit($post->form));

	exit;
} elseif (isset($post->delete)) {
	$umk = new Umk();

	echo json_encode($umk->delete($post->delete));

	exit;
}

echo("OK!");

Class Umk {

    public $link;
    public $data;

    public function __construct() {
    	$this->link = mssql_connect('name', 'user', 'passw');
    	if (!$this->link) die('Unable to connect!');
    	if (!mssql_select_db('trassir_gosnomer', $this->link)) die('Unable to select database!');
    }

    public function get_all() {
    	return self::get_array("SELECT * FROM [dbo].[Table] ORDER BY [department]");
    }

    public function get_short() {
    	return self::get_array("SELECT nomer,surname,name,fathername,department FROM [dbo].[Table] ORDER BY [department]");
    }

    public function get_trash() {
    	return self::get_array("SELECT * FROM [dbo].[Trash] ORDER BY [department]");
    }

    public function get_report() {
		self::clean_folder();
		$search_uniq = mssql_query("SELECT [department] FROM [dbo].[table] GROUP BY [department]");

		while ($row_com = mssql_fetch_array($search_uniq))	{
			$search2 = mssql_query("SELECT * FROM [dbo].[table] WHERE [department]='" . $row_com[0] . "'");
			while ($row = mssql_fetch_assoc($search2))	{
				$text = trim($row['nomer']);
				if (trim($row['surname']) != "") $text .= " " . trim($row['surname']);
				if (trim($row['name']) != "") $text .= " " . trim($row['name']);
				if (trim($row['fathername']) != "") $text .= " " . trim($row['fathername']);
				if (trim($row['date_birth']) != "") $text .= " " . trim($row['date_birth']);
				if (trim($row['subdivision']) != "") $text .= " " . trim($row['subdivision']);
				//if (trim($row['department']) != "") $text .= " ".trim($row['department']); Департамент
				//if (trim($row['date_creation']) != "") $text .= " ".trim($row['date_creation']); Дата создания
				if (trim($row['comments']) != "") $text .= " " . trim($row['comments']);
				$text .= "\r\n";
			}
			$file = $_SERVER['DOCUMENT_ROOT'] . "/parkovka/text/" . trim($row_com[0]) . ".txt";
			file_put_contents($file, $text);
			continue;
		}

		return self::create_zip();
    }

	public function edit($form) {
		$result['msg'] = 'Запись не добавлена';
		$result['type'] = 'danger';

		if (!empty($form)){
			$old_nomer = isset($form->old_nomer) ? self::clean($form->old_nomer) : '';
			$nomer = isset($form->nomer) ? self::clean($form->nomer) : '';
			$surname = isset($form->surname) ? self::clean($form->surname) : '';
			$name = isset($form->name) ? self::clean($form->name) : '';
			$fathername = isset($form->fathername) ? self::clean($form->fathername) : '';
			$date_birth = isset($form->date_birth) ? "'" . $form->date_birth . "'" : 'NULL';
			$subdivision = isset($form->subdivision) ? self::clean($form->subdivision) : '';
			$department = isset($form->department) ? self::clean($form->department) : '';
			$comments = isset($form->comments) ? self::clean($form->comments) : '';
			$now = date("Y-m-d");

			$result['msg'] = "Номер $nomer внесен в базу.";
			$result['type'] = 'success';

			$old = mssql_fetch_array(mssql_query("SELECT * FROM [Table] WHERE [nomer]='" . $nomer . "'"));

			if (!empty($old_nomer)) {
				self::remove($old);
				$now = $old['date_creation'];
				$result['msg'] = "Номер $nomer изменен.";
			}

			$query = mssql_query(
				"INSERT INTO [dbo].[Table] VALUES ('" . $nomer .
				"','" . $surname .
				"','" . $name .
				"','" . $fathername .
				"'," . $date_birth .
				",'" . $subdivision .
				"','" . $department .
				"','" . $now .
				"','" . $comments . "')");

			$result['data'] = self::get_all();
		}

		return $result;
	}

	public function delete($nomer) {
		$old = mssql_fetch_array(mssql_query("SELECT * FROM [Table] WHERE [nomer]='" . $nomer . "'"));
		self::remove($old);

		$result['msg'] = "Номер $nomer удален.";
		$result['type'] = 'success';
		$result['data'] = self::get_all();

		return $result;
	}

	private function remove($old) {
		if ($old['date_birth'] != NULL) $old['date_birth'] = "'" . $old['date_birth'] . "'";

		$query = mssql_query(
			"INSERT INTO [dbo].[Trash] VALUES ('" . $old['nomer'] .
			"','" . $old['surname'] .
			"','" . $old['name'] .
			"','" . $old['fathername'] .
			"'," . $old['date_birth'] .
			",'" . $old['department'] .
			"','" . $ip .
			"','" . $old['date_creation'] .
			"','" . date("Y-m-d") . "')");

		$query = mssql_query("DELETE FROM [dbo].[Table] WHERE [nomer]='" . $old['nomer'] . "'");
	}

	private static function get_array($value) {
		$data = mssql_query($value);
		$i = 0;

		while ($row = mssql_fetch_assoc($data)){
		    foreach ($row as $key => $value) {
		        $result[$i][$key] = $value;
		    }
		    $i++;
		}

		mssql_free_result($data);

		return $result;
	}

	private static function clean($value = "") {
		$value = trim($value);
		$value = stripslashes($value);
		$value = strip_tags($value);
		$value = htmlspecialchars($value);
		return $value;
	}

	private static function clean_folder() {
		if (file_exists('/var/www/parkovka/text/'))
			foreach (glob('/var/www/parkovka/text/*') as $file)
				unlink($file);

		if (file_exists('/var/www/parkovka/zip/'))
			foreach (glob('/var/www/parkovka/zip/*') as $file)
				unlink($file);
	}

	private function create_zip() {
		$source_dir = "/var/www/parkovka/text/";
		$log_file = $_SERVER['DOCUMENT_ROOT'] . "/parkovka/log/log.txt";
		$log_text = date("Y-m-d H:i:s") . " " . $_SERVER['REMOTE_ADDR'] . "\r\n";

		file_put_contents($log_file, $log_text, FILE_APPEND | LOCK_EX);
		$name = 'Выгрузка_' . date("d.m.y") . '.zip';
		$zip_file = "/var/www/parkovka/zip/$name";

		$file_list = self::listDirectory($source_dir);
	 	$zip = new ZipArchive();

		if ($zip->open($zip_file, ZIPARCHIVE::CREATE) === true) {
			foreach ($file_list as $file_zip) {
				if ($file_zip !== $zip_file) {
					$zip->addFile($file_zip, substr(iconv('UTF-8', 'CP866', $file_zip), strlen($source_dir)));
				}
			}
			$zip->close();
		}

		return "http://srv1/parkovka/zip/$name";
	}

	private static function listDirectory($dir){
		$result = array();
		$root = scandir($dir);

		foreach($root as $value){
			if($value === '.' || $value === '..'){
				continue;
			}

			if(is_file("$dir$value")){
				$result[] = "$dir$value";
				continue;
			}

			if(is_dir("$dir$value")) {
				$result[] = "$dir$value/";
			}

			foreach(self::listDirectory("$dir$value/") as $value){
				$result[] = $value;
			}
		}

		return $result;
	}

}