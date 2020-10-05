<?php

class Main
{
	protected $endpoint = 'https://api.vk.com/method/';
	protected $apiVersion = '5.26';
	protected $token = "f4eac44ef4eac44ef4eac44e48f49ef529ff4eaf4eac44eab9fb4725717e1daa1d33a96";
	public function getWallPosts($id, $offset)
	{
		$data = $this->performRequest('wall.get',
			[
				'owner_id' => $id,
				'count' => 100,
				'access_token' => $this->token,
        'offset' => $offset
			]
		);
		return $data['response']['items'];
	}

	public function getUser($user) {
		$data = $this->performRequest('users.get',
			[
				'user_ids' => $user,
				'access_token' => $this->token
			]
		);
		return $data['response'][0];
	}

	protected function performRequest($method, $params)
	{
		$params['v'] = $this->apiVersion;
		$url = $this->endpoint . $method . '?' . http_build_query($params);
		return json_decode(file_get_contents($url), true);
	}

}