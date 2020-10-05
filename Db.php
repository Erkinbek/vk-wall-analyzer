<?php


class Db
{
	protected $host = "localhost";
	protected $user = "root";
	protected $dbname = "vk";
	protected $dbpassword = "root";
	protected $db;

	public function __construct()
	{
		$this->db = new Mysqli($this->host, $this->user, $this->dbpassword, $this->dbname);
	}

	public function isNewUser(int $id) : bool
	{
		$sql = "SELECT id FROM users WHERE vk_id = " . $id;
		$res = $this->db->query($sql)->fetch_assoc();
		return is_null($res);
	}

	public function registerUser(array $user) : void
	{
		if ($this->isNewUser($user['id'])) {
			$full_name = $user['first_name'] . " " . $user['last_name'];
			$vk_id = $user['id'];
			$sql = "INSERT INTO users (full_name, vk_id) VALUES ('$full_name', $vk_id)";
			if (!$this->db->query($sql)) {
				echo $this->db->error;
			}
		}
	}

	public function registerPosts(array $posts, $counter = 0) : int
	{
		foreach ($posts as $post) {
			if ($this->isNewPost($post)) {
				$this->registerPost($post);
				$counter++;
			}
		}
		return $counter;
	}

	public function registerPost(array $post) : void
	{
		$comments = $post['comments']['count'];
		$likes = $post['likes']['count'];
		$reposts = $post['reposts']['count'];
		$post_id = $post['id'];
		$copy_history = json_encode($post['copy_history']);


		$stmt = $this->db->prepare('INSERT INTO posts (from_id, owner_id, posted, body, copy_history, comments, likes, reposts, post_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$stmt->bind_param("iiissiiii", $post['from_id'], $post['owner_id'], $post['date'], $post['text'], $copy_history, $comments, $likes, $reposts, $post_id);
		$stmt->execute();
		$stmt->close();
	}

	public function isNewPost(array $post) : bool
	{
		$sql = "SELECT id FROM posts WHERE post_id = " . $post['id'];
		$sql  .= " AND from_id = " . $post['from_id'];
		$sql  .= " AND owner_id = " . $post['owner_id'];
		$sql  .= " AND posted = " . $post['date'];
		$res = $this->db->query($sql)->fetch_assoc();
		return is_null($res);
	}

	public function getUsers() : array
	{
		$sql = "SELECT id, full_name, vk_id FROM users";
		$res = $this->db->query($sql)->fetch_all();
		return $res;
	}

	public function getUserPosts(int $userID, int $from, int $to) : array
	{
		for ($i = $from; $i <= $to; $i = $i + 86400) {
			$sql = "SELECT * FROM posts WHERE owner_id = " . $userID;
			$day1 = $i;
			$day2 = $day1 + 86400;
			$sql .= " AND posted >= " . $day1 . " AND posted <= " . $day2;
			$posts[] = $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
		}
		return array_filter($posts);
	}

	public function getUser(int $userID) : string
	{
		$sql = "SELECT full_name FROM users WHERE vk_id = " . $userID;
		$res = $this->db->query($sql)->fetch_assoc();
		return $res['full_name'];
	}
}