<?php
namespace Lal;

/**
 * Domain object representing a comment
 *
 * Comments need a way to translate author and subject
 * ids into User objects, so the DI container injects
 * the UserRepository. Comments don't need to know
 * or care how users are stored or instantiated.
 */
class Comment
{
	protected $userRepo;

	protected $id;
	protected $authorId;
	protected $aboutId;
	protected $content;

	public function __construct(UserRepository $userRepo)
	{
		$this->userRepo = $userRepo;
	}

	public function setId($id) { $this->id = $id; return $this; }
	public function getId() { return $this->id; }

	public function setContent($content) { $this->content = $content; return $this; }
	public function getContent() { return $this->content; }

	public function setAuthorId($authorId) { $this->authorId = $authorId; return $this; }
	public function getAuthorId() { return $this->authorId; }
	public function getAuthor()
	{
		return $this->userRepo->find($this->getAuthorId());
	}

	public function setAboutId($aboutId) { $this->aboutId = $aboutId; return $this; }
	public function getAboutId() { return $this->aboutId; }
	public function getAbout()
	{
		return $this->userRepo->find($this->getAboutId());
	}
}