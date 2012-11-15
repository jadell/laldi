<?php
namespace Lal;

use Evenement\EventEmitterInterface as EventEmitter;

/**
 * Repository for retrieving/saving User domain data.
 *
 * The repository handles access to the comment
 * specific data in the datastore. This way,
 * client objects that need Comment domain objects
 * do not need to know the details of retrieving
 * or saving them.
 *
 * Since the repository needs to turn raw data
 * into comment domain objects, it is injected with
 * the comment factory from the DI container. The
 * repository does not need to know the specifics
 * of how to instantiate a comment object, it just
 * needs a way to get them on demand.
 */
class CommentRepository
{
	protected $datasource;
	protected $commentFactory;
	protected $event;

	public function __construct(Datasource $datasource, EventEmitter $event, $commentFactory)
	{
		$this->datasource = $datasource;
		$this->commentFactory = $commentFactory;
		$this->event = $event;
	}

	/**
	 * This method is simply a convienience wrapper
	 * around the comment factory. It is public so that
	 * any clients of the repository do not need their
	 * own copy of the comment factory method. This way,
	 * the repository is the canonical place to
	 * instantiate new Comment objects.
	 */
	public function newComment()
	{
		return call_user_func($this->commentFactory);
	}

	public function findByAbout($aboutId)
	{
		$comments = $this->datasource->findBy('comments', 'aboutId', $aboutId);
		foreach ($comments as $i => $data) {
			$comments[$i] = $this->makeComment($data);
		}
		return $comments;
	}

	public function save(Comment $comment)
	{
		$id = $this->datasource->save('comments', array(
			'id' => $comment->getId(),
			'authorId' => $comment->getAuthorId(),
			'aboutId' => $comment->getAboutId(),
			'content' => $comment->getContent(),
		));
		$comment->setId($id);
		$this->datasource->commit();
		$this->event->emit('comment.saved', array($comment));
		return $comment;
	}

	protected function makeComment($data)
	{
		if (!$data) {
			return null;
		}
		return $this->newComment()
			->setId($data['id'])
			->setAuthorId($data['authorId'])
			->setAboutId($data['aboutId'])
			->setContent($data['content']);
	}
}