<?php

/**
 * This file is part of Schmutzka Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Schmutzka\Application\Routers;

use Nette;


trait TIdRoute
{
	/** @var string */
	private $mask;

	/** @var string */
	private $metadata;

	/** @var Schmutzka\Models\* */
	private $model;

	/** @var Nette\Caching\Cache */
	private $cache;

	/** @var string */
	private $keyName;

	/** @var string */
	private $valueName;


	/**
	 * Maps HTTP request to a Request object.
	 * @return HttpRequest|NULL
	 */
	public function match(Nette\Http\IRequest $httpRequest)
	{
		$appRequest = parent::match($httpRequest);

		if ( ! $appRequest) {
			return $appRequest;
		}

		$keyParam = $appRequest->parameters['id'];

		if (!empty($keyParam)) {
			$id = $this->getKeyByValue($keyParam);

			if ($id === NULL) {
				return NULL;
			}

			$params = $appRequest->parameters;
			$params['id'] = $id;
			$appRequest->parameters = $params;
		}

		return $appRequest;
	}


	/**
	 * Constructs absolute URL from Request object.
	 * @param  Request
	 * @param  Url
	 * @return string|NULL
	 */
	public function constructUrl(Nette\Application\Request $appRequest, Nette\Http\Url $refUrl)
	{
		if (isset($appRequest->parameters[$this->keyName])) {
			$params = $appRequest->parameters;
			$keyParam = $params[$this->keyName];

			// check if router matches (also router presenter)
			$mpv = explode(':', $this->metadata);
			$action = array_pop($mpv);

			if (($params['action'] == $action) && $keyParam) {
				$value = $this->getValueByKey($keyParam);

				if ($value === NULL) {
					return NULL;
				}

				$params = $appRequest->parameters;
				$params[$this->keyName] = $value;
				$appRequest->parameters = $params;
			}
		}

		return parent::constructUrl($appRequest, $refUrl);
	}


	/**
	 * @return array
 	 */
	private function getTranslateTable()
	{
		$cacheTag = 'route_' . sha1($this->mask . $this->metadata);
		$data = $this->cache->load($cacheTag);

		if ($data == NULL) {
			$data = $this->buildTranslateTable();

			$this->cache->save($cacheTag, $data, [
				'tag' => $cacheTag,
				'expire' => '30 mins'
			]);
		}

		return $data;
	}


	/**
	 * @param  int
	 * @return  string|NULL
	 */
	private function getValueByKey($key)
	{
		$data = $this->getTranslateTable();

		if (isset($data[$key])) {
			return $data[$key];
		}

		return NULL;
	}


	/**
	 * @param  string
	 * @return  int|NULL
	 */
	private function getKeyByValue($value)
	{
		$data = $this->getTranslateTableFlipped();

		if (isset($data[$value])) {
			return $data[$value];
		}

		return NULL;
	}


	/**
	 * @return  array [ value => key ]
	 */
	private function getTranslateTableFlipped()
	{
		$data = $this->getTranslateTable();
		return array_flip($data);
	}

}
