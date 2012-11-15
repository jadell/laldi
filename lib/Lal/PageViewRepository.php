<?php
namespace Lal;

/**
 * Repository for retrieving/saving User page view data.
 *
 * The repository handles access to the pageview
 * specific data in the datastore.
 *
 * User objects call this object directly in order to
 * get specific counts, but nothing directly calls
 * the method to update the page view count. This
 * happens via an event. If we decide to turn off
 * page view counting, we only have to unregister
 * the listener in the bootstrap.
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