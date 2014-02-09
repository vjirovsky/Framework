<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */

namespace Zenify\Doctrine;

use Doctrine;
use Kdyby;


class Utils
{

	/**
	 * @param object
	 * @return array
	 */
	public static function toArray($entity)
	{
        $reflection = new \ReflectionClass($entity);
        $details = array();
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
            if ( ! $property->isStatic()) {
                $value = $entity->{$property->getName()};

                if ($value instanceof Kdyby\Doctrine\Entities\BaseEntity) {
                    $value = $value->getId();

                } elseif ($value instanceof ArrayCollection || $value instanceof PersistentCollection) {
                    $value = array_map(function (BaseEntity $entity) {
                        return $entity->getId();
                    }, $value->toArray());
                }

                $details[$property->getName()] = $value;
            }
        }

        return $details;
    }

}
