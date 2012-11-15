<?php
namespace Lal;

/**
 * Log comments that are added.
 *
 * This could be to a file, or sent by email,
 * or to an audit database, or wherever.
 *
 * Nothing that deals with comments actually
 * calls this directly. Instead, it is registered
 * to listen for "comment.saved" events. If we
 * decide to turn off comment logging, we only have
 * to unregister the listener in the bootstrap.
 */
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