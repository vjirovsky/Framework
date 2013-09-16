<?php

namespace Schmutzka\Models;


class Gallery extends Base
{

	/**
	 * @param int
	 * @return  NotORM_Result
	 */
	public function fetch($id)
	{
		$item = parent::fetch($id);
		$item['files'] = $item->gallery_file();

		return $item;
	}


	/**
	 * @param  array
	 * @return  NotORM_Result
	 */
	public function fetchAll($key = array())
	{
		$result = parent::fetchAll($key);

		foreach ($result as $id => $row) {
			$row['files'] = $row->gallery_file();
		}

		return $result;
	}

}
