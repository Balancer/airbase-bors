<?php

/*
 * Example echobot application using Jaxl library
 * Read more: http://bit.ly/bz9KXb
*/

// Initialize Jaxl Library
$jaxl = new JAXL();

// Include required XEP's
jaxl_require(array(
	'JAXL0115', // Entity Capabilities
	'JAXL0085', // Chat State Notification
	'JAXL0092', // Software Version
	'JAXL0203', // Delayed Delivery
	'JAXL0199'  // XMPP Ping
), $jaxl);



// Sample Echobot class
class echobot
{
	var $gm_client;
	var $gm_worker;

	function gm_init()
	{
		$this->gm_client= new GearmanClient();
		$this->gm_client->addServer();

		$this->gm_worker= new GearmanWorker();
		$this->gm_worker->addServer();
		$this->gm_worker->addFunction("balabot.jabber.send", array($this, "gm_send"));
		$this->gm_worker->setTimeout(100);
	}

	function startStream()
	{
		global $jaxl;
		$jaxl->startStream();
	}

		function doAuth($mechanism) {
			global $jaxl;
			switch(TRUE) {
				case in_array("ANONYMOUS",$mechanism):
					$jaxl->auth("ANONYMOUS");
					break;
				case in_array("DIGEST-MD5",$mechanism):
					$jaxl->auth("DIGEST-MD5");
					break;
				case in_array("PLAIN",$mechanism):
					$jaxl->auth("PLAIN");
					break;
				case in_array("X-FACEBOOK-PLATFORM",$mechanism):
					/*
					 * Facebook chat connect using Jaxl library
					 * Read more: http://bit.ly/dkdFjL
					*/
					$jaxl->auth("X-FACEBOOK-PLATFORM");
					break;
				default:
					die("No prefered auth method exists...");
					break;
			}
		}
		
		function postAuth() {
			global $jaxl;
			$jaxl->setStatus(FALSE, FALSE, FALSE, TRUE);
			$jaxl->getRosterList(array($this, 'handleRosterList'));
		}
		
		function handleRosterList($payload) {
			if(is_array($payload['queryItemJid'])) {
				foreach($payload['queryItemJid'] as $key=>$jid) {
					$group = $payload['queryItemGrp'][$key];
					$subscription = $payload['queryItemSub'][$key];
				}
			}
		}

	function getMessage($payloads)
	{
		global $jaxl;
		foreach($payloads as $payload)
		{
			if($payload['offline'] != JAXL0203::$ns
				&& (!$payload['chatState'] || $payload['chatState'] = 'active')
			)
			{
				if(strlen($payload['body']) > 0)
				{
					// echo back the incoming message
//					$jaxl->sendMessage($payload['from'], 'ответ: '.$payload['body']);
//					$jaxl->sendMessage($payload['from'], 'Я занят, позвоните позже');
					$data = array(
						'worker_class_name' => 'balancer_balabot_parser',
						'payload' => $payload,
					);

					echo "Send work to gearman\n";
					$this->gm_client->doBackground("balabot.work", serialize($data));
				}
			}
		}
	}

	function getPresence($payloads)
	{
			global $jaxl;	
			foreach($payloads as $payload) {
				if($payload['type'] == "subscribe") {
					// accept subscription
					$jaxl->subscribed($payload['from']);

					// go for mutual subscription
					$jaxl->subscribe($payload['from']);
				}
				else {
					if($payload['type'] == "unsubscribe") {
						// accept subscription
						$jaxl->unsubscribed($payload['from']);

						// go for mutual subscription
						$jaxl->unsubscribe($payload['from']);
					}
				}
			}
	}

	function gm_ticker()
	{
		echo substr(time(), -1)."+\r";
		@$this->gm_worker->work();
	}

	function gm_send($job)
	{
		global $jaxl;
		$workload = $job->workload();
		$data = unserialize($workload);
//		echo "Получено указание на отсылку ".print_r($data, true)."\n";
		$jaxl->sendMessage($data['to'], $data['message']);
		echo "{$data['to']} <= {$data['message']}\n";
	}
}

// Add callbacks on various event handlers
$echobot = new echobot();

$echobot->gm_init();

JAXLPlugin::add('jaxl_post_connect', array($echobot, 'startStream'));
JAXLPlugin::add('jaxl_get_auth_mech', array($echobot, 'doAuth'));
JAXLPlugin::add('jaxl_post_auth', array($echobot, 'postAuth'));
JAXLPlugin::add('jaxl_get_message', array($echobot, 'getMessage'));
JAXLPlugin::add('jaxl_get_presence', array($echobot, 'getPresence'));

JAXLCron::add(array($echobot, 'gm_ticker'), 1);
