<?php
class GeometryDashBridge extends BridgeAbstract {
	const NAME = 'Geometry Dash Bridge';
	const URI = 'http://robtopgames.com'; // HTTP because server requests don't support HTTPS
	const DESCRIPTION = 'Returns user posts from the Geometry Dash servers';
	const MAINTAINER = 'SergioFLS';
	const PARAMETERS = array(array(
		'endpoint' => array(
			'name' => 'Server endpoint',
			'required' => false,
			'defaultValue' => 'www.boomlings.com/database/'
		)
	));

	const CACHE_TIMEOUT = 10800; // 3h

	public function getURI() {
		if ( null !== $this->getInput('endpoint') ) {
			return 'http://'
				. $this->getInput('endpoint');
		} else {
			return self::URI;
		}
	}

	public function collectData() {
		$responseData = getContents(
			$this->getURI() . 'getGJAccountComments20.php',
			array('Accept-Encoding: '),
			array(
				CURLOPT_POST => 1,
				CURLOPT_USERAGENT => '',
				CURLOPT_POSTFIELDS => 'gameVersion=20&binaryVersion=32&gdw=0&accountID=71&page=0&total=0&secret=Wmfd2893gb7'
			)
		);

		//$responseData = getInput('server');

		$rawComments = explode('#', $responseData)[0];
		foreach (explode('|', $rawComments) as $singleRawComment) {
			$item = array();
			$encodedCommentText = explode('~', $singleRawComment)[1];
			$item['title'] = base64_decode($encodedCommentText);
			$item['uid'] = explode('~', $singleRawComment)[7];

			$this->items[] = $item;
		}
	}
}
