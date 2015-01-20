<?php
class User {
	private $redis;
	// 10��������˲���½�Զ�ɾ��key
	public $timeout = 864000;
	function __construct() {
		require_once ('redis.class.php');
		$this->redis = redis_class::getInstance ();
	}
	function add($appkey, $secret) {
		$this->redis->setex ( $appkey, $this->timeout, $secret );
	}
	function dbsize(){
		return $this->redis->dbSize();
	}
	/**
	 * ������֤�Ƿ�ͨ��
	 *
	 * @param ID $appkey        	
	 * @param ��֤ $secret        	
	 * @return boolean
	 */
	function get($appkey, $secret) {
		if ($this->redis->get ( $appkey ) == $secret) {
			$this->add($appkey,$secret);
			return true;
		} else {
			return false;
		}
	}
}

$user = new User ();
if (isset ( $_REQUEST ['action'] )) {
	if ($_REQUEST ['action'] == "register") {
		if (! isset ( $_SESSION )) {
			session_start ();
		}
		$appkey = session_id ();
		//��������֤���ܣ�С����ǲ�Ҫ���ƻ�Ŷ��
		$secret = md5 ( substr ( session_id (), 3 ) . time () );
		$user->add ( $appkey, $secret );
		echo "{\"appkey\":\"$appkey\",\"secret\":\"$secret\"}";
	}
}

if (isset ( $_REQUEST ['appkey'] ) && isset ( $_REQUEST ['secret'] )) {
	if (! $user->get ( $_REQUEST ['appkey'], $_REQUEST ['secret'] )) {
		require_once (dirname ( __FILE__ ) . '/msg.php');
		$msg = new msg ();
		$msg->has_new_msg = - 1;
		$msg->is_online = - 1;
		$msg = json_encode ( $msg );
		echo "callback({data: $msg })";
		exit ();
	}
}

