<?php
namespace Lal;

/**
 * Main controller
 *
 * The front controller in index.php calls methods
 * on this object to handle requests. The purpose
 * of separating out the handling from the routing
 * framework is to make it easier to test the
 * controller in isolation.
 */
class HelloController
{
	protected $userRepo;
	protected $view;

	public function __construct()
	{
		$this->userRepo = new UserRepository();
		$this->view = new View();
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

	protected function renderUser(User $user=null, $search)
	{
		return $this->view->render('hello', array(
			'search' => $search,
			'user' => $user,
			'pairs' => $user ? $user->getPairs() : null,
		));
	}
}