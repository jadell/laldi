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
 */
class UserRepository
{
	protected $datasource;

	public function __construct()
	{
		$this->datasource = new Datasource();
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
		$user = new User();
		return $user
			->setId($data['id'])
			->setUsername($data['username'])
			->setEmail($data['email']);
	}
}