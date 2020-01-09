<?php

	class AwsLab {

		private $_awsLab;

		private $_ec2;
		private $_config;

		public $_contextType;
		public $_contextId;

		private $_username;
		private $_password;
		private $_userData;

		private $_sdkVersion;
		private $_region;
		private $_key;
		private $_secret;

		private $_instanceId;
		private $_instanceState;
		private $_ipAddress;

		const LogCreate = 1;
		const LogTerminate = -1;

		function __construct() {

			load_plugin("aws");

			$this->_initialize();

		}

		static function killInstances() {

			if (empty(($deadInstances = db_query("SELECT * FROM aws_lab_instances WHERE expires_at < CURRENT_TIMESTAMP;")))) {
				return;
			}

			$awsLab = new AwsLab;
			foreach ($deadInstances as $deadInstance) {
				$awsLab->killInstance($deadInstance);
			}

		}

		static function instanceExists($contextType, $contextId) {

			self::killInstances();
			if (!empty(($instance = db_query("SELECT * FROM aws_lab_instances WHERE context_type = ".db_sanitize($contextType)." AND context_id = ".$context_id.";")))) {
				return $instance[0];
			}

			return false;

		}

		function createInstance($awsLabId, $username, $password, $contextType, $contextId) {

			AwsLab::killInstances();

			$this->_contextType = $contextType;
			$this->_contextId = $contextId;

			$this->_username = $username;
			$this->_password = $password;

			if (($activeInstance = $this->_getActiveInstance()) !== false) {
				return $this->_returnInstanceInfo($activeInstance);
			}

			$this->_setConfiguration($awsLabId);

			$this->prepareUserData();

			try {
				$model = $this->_ec2->runInstances(
					[
						"ImageId" => $this->_config["AWS_IMAGE_ID"],
						"MinCount" => intval($this->_config["AWS_INSTANCE_MIN_COUNT"]),
						"MaxCount" => intval($this->_config["AWS_INSTANCE_MAX_COUNT"]),
						"InstanceType" => $this->_config["AWS_INSTANCE_TYPE"],
						"KeyName" => $this->_config["AWS_KEY_NAME"],
						"UserData" => $this->_userData,
						"SubnetId" => $this->_config["AWS_SUBNETID"],
						"security_group_ids" => [$this->_config["AWS_SECURITY_GROUP_ID"]]
					]
				);
			}
			catch(Exception $e) {
				var_dump($e->getMessage()); die;
			}

			$instance_details = $model->toArray();

			$this->_instanceId = $instance_details["Instances"][0]["InstanceId"];
			$this->_instanceState = $instance_details["Instances"][0]["State"]["Name"];
			if (isset($instance_details["Instances"][0]["PublicIpAddress"])) {
				$this->_ipAddress = $instance_details["Instances"][0]["PublicIpAddress"];
			}

			return $this->_log();

		}

		function getInstanceId() {
			return $_instanceId;
		}

		function getInstanceInfo($instanceId) {

			$instances = $this->_ec2->describeInstances(["InstanceIds" => [$instanceId]]);
			$reservations = $instances["Reservations"];
			foreach ($reservations as $reservation) {

				foreach ($reservation["Instances"] as $instance) {

					if ($instance["InstanceId"] == $instanceId) {
						return [$instance["State"]["Name"], (isset($instance["PublicIpAddress"]) ? $instance["PublicIpAddress"] : "")];
					}

				}

			}

		}

		function checkInstanceState($instanceId) {
			return $this->getInstanceInfo($instanceId)[0];
		}

		function killInstance($awsLabInstanceId) {

			$awsLabInstance;

			if (is_numeric($awsLabInstanceId)) {

				if (empty(($awsLabInstance = db_query("SELECT * FROM aws_lab_instances WHERE id = ".$awsLabInstanceId.";")))) {
					return;
				}

				$awsLabInstance = $awsLabInstance[0];

			}
			else if (!empty($awsLabInstanceId["instance_id"])) {
				$awsLabInstance = $awsLabInstanceId;
			}
			else {
				return;
			}

			$this->_ec2->terminateInstances(['InstanceIds'=>array($awsLabInstance["instance_id"])]);

			$this->_instanceId = $awsLabInstance["instance_id"];

			$this->_log(self::LogTerminate, $awsLabInstanceId["id"]);

		}

		private function _initialize() {

			load_library("setting");
			if (($awsConfig = setting_get("aws.config")) !== false) {
				$awsConfig = json_decode($awsConfig);
			}

			$this->_sdkVersion = $awsConfig->sdkVersion;
			$this->_region = $awsConfig->region;
			$this->_key = $awsConfig->key;
			$this->_secret = $awsConfig->secret;

			$this->_ec2 = Aws\Ec2\Ec2Client::factory(
				[
					"version" => $this->_sdkVersion,
					"region" => $this->_region,
					"credentials" => [
						"key" => $this->_key,
						"secret" => $this->_secret
					]
				]
			);

		}

		private function _getActiveInstance() {

			if (empty(($activeInstance = db_query("SELECT * FROM aws_lab_instances WHERE context_type = ".db_sanitize($this->_contextType)." AND context_id = ".db_sanitize($this->_contextId).";")))) {
				return false;
			}

			$activeInstance = $activeInstance[0];
			$instanceInfo = $this->getInstanceInfo($activeInstance["instance_id"]);
			if ($instanceInfo[0] == "running") {
				return $instanceInfo[1];
			}

			return false;

		}

		private function _setConfiguration($awsLabId) {

			$this->_setAwsLab($awsLabId);
			$this->_config = json_decode($this->_awsLab["config"], true);

		}

		private function _setAwsLab($awsLabId) {

			if (is_numeric($awsLabId)) {
				$this->_awsLab = db_query("SELECT * FROM aws_labs WHERE id = ".db_sanitize($awsLabId))[0];
			}
			else if (is_array($awsLabId)) {
				$this->_awsLab = $awsLabId;
			}

		}

		private function prepareUserData() {

			$userData = '
				<powershell>
				$username = "'.$this->_username.'"
				$password = "'.$this->_password.'"
				mkdir "c:\$username\"
				Set-Location "c:\$username\"
				$cn=[ADSI]"WinNT://$env:computername"
				$computername=$env:computername
				$user=$cn.Create("User",$username)
				$user.SetPassword($password)
				$user.setinfo()
				$group_name = "Remote Desktop Users"
				$group = [ADSI]"WinNT://$computername/$group_name,group"
				$group.Add("WinNT://$computername/$username,user")
				</powershell>';

			$this->_userData = base64_encode($userData);

		}

		private function _log($type = self::LogCreate, $awsLabInstanceId = false) {

			if ($type == self::LogCreate) {

				$interval = "PT".$this->_awsLab["lifespan"]."S";
				$expiresAt = (new DateTime)->add(new DateInterval($interval));

				db_exec("INSERT INTO aws_lab_instances (aws_lab_id, instance_id, ip, context_type, context_id, expires_at) VALUES (".$this->_awsLab["id"].", ".db_sanitize($this->_instanceId).", ".db_sanitize($this->_ipAddress).", ".db_sanitize($this->_contextType).", ".$this->_contextId.", ".db_sanitize($expiresAt->format("Y-m-d H:i:s")).");");
				$awsLabInstanceId = db_get_last_insert_id();

				db_exec("INSERT INTO system_log (source, data) VALUES ('aws.lab.plugin.create', ".db_sanitize(json_encode(["instance_id" => $this->_instanceId, "state" => $this->_instanceState, "ip" => $this->_ipAddress])).");");

				return db_query("SELECT * FROM aws_lab_instances WHERE id  = ".$awsLabInstanceId)[0];

			}
			else {

				db_exec("DELETE FROM aws_lab_instances WHERE id = ".$awsLabInstanceId);
				db_exec("INSERT INTO system_log (source, data) VALUES ('aws.lab.plugin.terminate', ".db_sanitize($this->_instanceId).");");

			}

		}

	}

?>