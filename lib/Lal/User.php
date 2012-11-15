<?php
namespace Lal;

/**
 * Domain object representing a user
 */
class User
{
	protected $userRepo;

	protected $id;
	protected $username;
	protected $email;

	public function __construct()
	{
		$this->userRepo = new UserRepository();
	}

	public function setId($id) { $this->id = $id; return $this; }
	public function getId() { return $this->id; }

	public function setUsername($username) { $this->username = $username; return $this; }
	public function getUsername() { return $this->username; }

	public function setEmail($email) { $this->email = $email; return $this; }
	public function getEmail() { return $this->email; }

	public function getPairs()
	{
		return $this->userRepo->findPairs($this->getId());
	}
}