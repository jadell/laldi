<?php
namespace Lal;

/**
 * Domain object representing a user
 *
 * Users need a way to retrieve their paired users
 * and comments about them, so the DI container injects
 * the User and Comment repositories. Users don't
 * need to know or care how users and comments are
 * stored or instantiated.
 */
class User
{
	protected $userRepo;
	protected $commentRepo;

	protected $id;
	protected $username;
	protected $email;

	public function __construct(UserRepository $userRepo, CommentRepository $commentRepo)
	{
		$this->userRepo = $userRepo;
		$this->commentRepo = $commentRepo;
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

	public function getCommentsAbout()
	{
		return $this->commentRepo->findByAbout($this->getId());
	}
}