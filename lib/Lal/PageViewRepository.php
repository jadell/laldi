<?php
namespace Lal;

/**
 * Repository for retrieving/saving User page view data.
 *
 * The repository handles access to the pageview
 * specific data in the datastore.
 */
class PageViewRepository
{
	protected $datasource;

	public function __construct(Datasource $datasource)
	{
		$this->datasource = $datasource;
	}

	public function getCount(User $user)
	{
		return count($this->datasource->findBy('pageviews', 'userId', $user->getId()));
	}

	public function incrementCount(User $user)
	{
		$this->datasource->save('pageviews', array(
			'userId' => $user->getId(),
			'timestamp' => time(),
		));
		$this->datasource->commit();
	}
}