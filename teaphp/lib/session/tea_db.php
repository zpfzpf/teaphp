<?php 

class tea_db extends session_apt
{
	private $db;
	private $table = 'session';

	function __construct($options = array())
	{
		parent::__construct($options);
		$this->register();
	}

	function open($save_path, $session_name)
	{
        $this->_connect();
        $this->gc(ini_get("session.gc_maxlifetime"));
		return true;
	}

	function close()
	{
		return $this->gc(ini_get("session.gc_maxlifetime"));
	}

	function read($id)
	{
		$sdb = $this->db->query("SELECT `data` FROM $this->table WHERE `sessionid`='$id'");
		if($r = $sdb->fetch()){
			return $r['data'];
		}
		return false;
	}

	function write($id, $data)
	{
		$res = $this->db->query("REPLACE INTO $this->table (`sessionid`, `lastvisit`, `data`) VALUES('$id', '".time()."', '$data')");
		return true;
	}

	function destroy($id)
	{
		$res = $this->db->query("DELETE FROM $this->table WHERE `sessionid`='$id'");
		return true;
	}

	function gc($maxlifetime)
	{
		$expiretime = time() - $maxlifetime;
		$res = $this->db->query("DELETE FROM $this->table WHERE `lastvisit`<'$expiretime'");
		return true;
	}
	
	function _connect()
	{
		global $tea;
		//如果没设定数据库名，那么复用原来的db
		if (!isset($this->options['dbname']))
		{
			$this->db = $tea->db;
		}//否则新建db链接实例
		else
		{
			$this->db = load::classes('core.db',TEA_PATH,$this->options);
		}
		if (isset($this->options['dbtable'])){
			$this->table = $this->options['dbtable'];
		}
	}
}