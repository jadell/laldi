<?php
namespace Lal;

/**
 * Simple JSON file-based data store
 */
class Datasource
{
	protected $filepath;
	protected $data;


	public function __construct($filepath)
	{
		$this->filepath = $filepath;
		$this->data = json_decode(file_get_contents($this->filepath), true);
	}

	public function commit()
	{
		return file_put_contents($this->filepath, json_encode($this->data));
	}

	public function save($table, $data)
	{
		if (empty($data['id'])) {
			$data['id'] = $this->nextId($table);
		}
		$this->data[$table][] = $data;
		return $data['id'];
	}

	protected function nextId($table)
	{
		if (!isset($this->data[$table])) {
			$this->data[$table] = array();
		}
		return count($this->data[$table]);
	}

	public function find($table, $id)
	{
		return $this->findOneBy($table, "id", $id);
	}

	public function findAll($table)
	{
		if (!isset($this->data[$table])) {
			return array();
		}
		return $this->data[$table];
	}

	public function findBy($table, $field, $value)
	{
		if (!isset($this->data[$table])) {
			return array();
		}

		$matches = array();
		foreach ($this->data[$table] as $check) {
			if ($check[$field] == $value) {
				$matches[] = $check;
			}
		}
		return $matches;
	}

	public function findOneBy($table, $field, $value)
	{
		if (!isset($this->data[$table])) {
			return null;
		}

		foreach ($this->data[$table] as $check) {
			if ($check[$field] == $value) {
				return $check;
			}
		}
		return null;
	}
}