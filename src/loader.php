<?php

/**
 * This file is part of Zenify Framework
 *
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 *
 * For the full copyright and license information, please view
 * the file license.md that was distributed with this source code.
 */


if (file_exists(__DIR__ . '/../nette.min.php')) {
	require_once __DIR__ . '/../nette.min.php';

} else {
	require_once __DIR__ . '/../Nette/loader.php';
	require_once __DIR__ . '/../Nette/DI/CompilerExtension.php';
}

require_once __DIR__ . '/common/Configurator.php';
require_once __DIR__ . '/common/shortcuts.php';
