<?php
namespace Lal;

use Evenement\EventEmitterInterface as EventEmitter;

/**
 * Main controller
 *
 * The front controller in index.php calls methods
 * on this object to handle requests. The purpose
 * of separating out the handling from the routing
 * framework is to make it easier to test the
 * controller in isolation.
 *
 * Since the controller is instantiated in the DI
 * container, it is completely isolated from the
 * rest of the app framework. Everything it requires
 * is explicitly defined in its constructor, which
 * means unit tests can easily pass in stub/mock
 * objects for testing purposes.
 */
class HelloController
{
	protected $userRepo;
	protected $commentRepo;
	protected $view;
	protected $event;

	public function __construct(UserRepository $userRepo, CommentRepository $commentRepo, View $view, EventEmitter $event)
	{
		$this->userRepo = $userRepo;
		$this->commentRepo = $commentRepo;
		$this->view = $view;
		$this->event = $event;
	}

	public function index()
	{
		$users = $this->userRepo->findAll();
		return $this->view->render('index', array('users'=>$users));
	}

	public function helloName($name)
	{
		$user = $this->userRepo->findByUsername($name);
		return $this->renderUser($user, $name);
	}

	public function helloId($id)
	{
		$user = $this->userRepo->find($id);
		return $this->renderUser($user, $id);
	}

	public function addComment($aboutId, $authorName, $content)
	{
		$author = $this->userRepo->findByUsername($authorName);
		$about = $this->userRepo->find($aboutId);
		if ($author && $about && $content) {
			$comment = $this->commentRepo->newComment()
				->setAuthorId($author->getId())
				->setAboutId($about->getId())
				->setContent($content);
			$this->commentRepo->save($comment);
		}
		return $this->view->redirect("/$aboutId");
	}

	protected function renderUser(User $user=null, $search)
	{
		if ($user) {
			$this->event->emit('user.viewed', array($user));
		}
		return $this->view->render('hello', array(
			'search' => $search,
			'user' => $user,
			'pairs' => $user ? $user->getPairs() : null,
			'comments' => $user ? $user->getCommentsAbout() : null,
		));
	}
}