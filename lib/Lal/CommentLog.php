<?php
namespace Lal;

class CommentLog
{
	protected $fd;

	public function __construct($filePath)
	{
		$this->fd = fopen($filePath, 'a');
	}

	public function __destruct()
	{
		fclose($this->fd);
	}

	public function logComment(Comment $comment)
	{
		$authorName = $comment->getAuthor()->getUsername();
		$aboutName = $comment->getAbout()->getUsername();
		$content = $comment->getContent();
		$at = date('Y-m-d H:i:s');
		fprintf($this->fd, "[%s] %s commented about %s: %s\n", $at, $authorName, $aboutName, $content);
	}
}