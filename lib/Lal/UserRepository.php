<?php
namespace Lal;

/**
 * Repository for retrieving User domain data.
 *
 * The repository handles access to the user
 * specific data in the datastore. This way,
 * client objects that need User domain objects
 * do not need to know the details of retrieving
 * them.
 *
 * Since the repository needs to turn raw data
 * into user domain objects, it is injected with
 * the user factory from the DI container. The
 * repository does not need to know the specifics
 * of how to instantiate a user object, it just
 * needs a way to get them on demand.
 */
class UserRepository
{
	protected $datasource;
	protected $userFactory;

	public function __construct(Datasource $datasource, $userFactory)
	{
		$this->datasource = $datasource;
		$this->userFactory = $userFactory;
	}

	/**
	 * This method is simply a convienience wrapper
	 * around the user factory. It is public so that
	 * any clients of the repository do not need their
	 * own copy of the user factory method. This way,
	 * the repository is the canonical place to
	 * instantiate new User objects.
	 */
	public function newUser($data=array())
	{
		return call_user_func($this->userFactory);
	}

	public function find($id)
	{
		return $this->makeUser($this->datasource->find('users', $id));
	}

	public function findAll()
	{
		$users = $this->datasource->findAll('users');
		foreach ($users as $i => $data) {
			$users[$i] = $this->makeUser($data);
		}
		return $users;
	}

	public function findByUsername($username)
	{
		return $this->makeUser($this->datasource->findOneBy('users', 'username', $username));
	}

	public function findPairs($userId)
	{
		$pairs = $this->datasource->findBy('pairs', 0, $userId);
		foreach ($pairs as $i => $pairData) {
			$pairs[$i] = $this->find($pairData[1]);
		}
		return $pairs;
	}

	protected function makeUser($data)
	{
		if (!$data) {
			return null;
		}
		return $this->newUser()
			->setId($data['id'])
			->setUsername($data['username'])
			->setEmail($data['email']);
	}
}