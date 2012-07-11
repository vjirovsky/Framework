<?php

namespace Schmutzka\Diagnostics\Panels;

use Nette,
	Nette\Templating\FileTemplate,
	Nette\Latte\Engine;

/**
 * XDebug Trace panel for Nette 2.0 framework.
 *
 * @author  Miloslav Hůla
 * @version 0.3-beta4
 * @see     http://github.com/milo/XDebugTracePanel
 * @licence LGPL
 */
class XDebugTrace extends Nette\Object implements Nette\Diagnostics\IBarPanel
{
	/** Tracing states */
	const
		STATE_STOP = 0,
		STATE_RUN = 1,
		STATE_PAUSE = 2;


	/** Filter callback action bitmask */
	const
		STOP = 0x01,
		SKIP = 0x02;


	/** Adding filter bitmask flags */
	const
		FILTER_ENTRY = 1,
		FILTER_EXIT = 2,
		FILTER_BOTH = 3,
		FILTER_APPEND_ENTRY = 4,
		FILTER_APPEND_EXIT = 8,
		FILTER_APPEND = 12,
		FILTER_REPLACE_ENTRY = 16,
		FILTER_REPLACE_EXIT = 32,
		FILTER_REPLACE = 48;


	/**
	 * @var int maximal length of line in trace file
	 */
	public static $traceLineLength = 4096;


	/**
	 * @var bool delete trace file in destructor or not
	 */
	public $deleteTraceFile = FALSE;


	/**
	 * @var \Panel\XDebugTrace
	 */
	private static $instance;


	/**
	 * @var int tracing state
	 */
	private $state = self::STATE_STOP;


	/**
	 * @var string path to trace file
	 */
	private $traceFile;


	/**
	 * @var array of stdClass
	 */
	protected $traces = array();


	/**
	 * @var reference to $this->traces
	 */
	protected $trace;


	/**
	 * @var array of string trace titles
	 */
	protected $titles = array();


	/**
	 * @var array of level => indent size
	 */
	protected $indents = array();


	/**
	 * @var reference to $this->indents
	 */
	protected $indent;


	/**
	 * @var bool internal class error occured, error template will be rendered
	 */
	protected $isError = FALSE;


	/**
	 * @var string
	 */
	protected $errMessage = '';


	/**
	 * @var string
	 */
	protected $errFile;


	/**
	 * @var int
	 */
	protected $errLine;


	/**
	 * @var array of callbacks called when parsing entry record from trace file
	 */
	protected $filterEntryCallbacks = array();


	/**
	 * @var array of callbacks called when parsing exit record from trace file
	 */
	protected $filterExitCallbacks = array();


	/**
	 * @var array of bool  default filtering callback setting
	 */
	protected $skipOver = array(
		'phpInternals' => TRUE,
		'XDebugTrace' => TRUE,
		'Nette' => TRUE,
		'callbacks' => TRUE,
		'includes' => TRUE,
	);


	/**
	 * @var \Nette\Templating\FileTemplate
	 */
	protected $lazyTemplate;


	/**
	 * @var \Nette\Templating\FileTemplate
	 */
	protected $lazyErrorTemplate;



	/**
	 * @param  string path to trace file
	 * @param  bool skip PHP internal functions when parsing trace file
	 * @throws \Nette\InvalidStateException
	 */
	public function __construct($traceFile = NULL)
	{
		if (self::$instance !== NULL) {
			throw new \Nette\InvalidStateException('Class ' . get_class($this) . ' can be instantized only once, xdebug_start_trace() can run only once.');
		}
		self::$instance = $this;

		if ($traceFile == NULL) {
			$traceFile = TEMP_DIR . "/debug_trace";
		}

		if (substr_compare($traceFile, '.xt', -3, 3, TRUE) === 0) {
			$traceFile = substr($traceFile, 0, -3);
		}

		if (!extension_loaded('xdebug')) {
			$this->setError('XDebug extension is not loaded');

		} elseif (@file_put_contents($traceFile . '.xt', '') === FALSE) {
			$this->setError("Cannot create trace file '$traceFile.xt'", error_get_last());

		} else {
			$this->traceFile = $traceFile;

		}

		$this->addFilterCallback(array($this, 'defaultFilterCb'));
	}



	public function __destruct()
	{
		if ($this->deleteTraceFile && is_file($this->traceFile . '.xt')) {
			@unlink($this->traceFile . '.xt');
		}
	}



	/**
	 * Shortcut for \Panel\XDebugTrace::getInstance()->method()
	 * as \Panel\XDebugTrace::callMethod();
	 */
	public static function __callStatic($name, $args)
	{
		$instance = self::getInstance();

		if (preg_match('/^call([A-Z].*)/', $name, $match)) {
			$method = lcfirst($match[1]);
			if (method_exists($instance, $method)) {
				return call_user_func_array(array($instance, $method), $args);
			}
		}

		parent::__callStatic($name, $args);
	}



	/**
	 * Access to class instance.
	 *
	 * @return \Panel\XDebugTrace
	 * @throws \Nette\InvalidStateException
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			throw new \Nette\InvalidStateException(get_called_class() . ' has not been instantized yet.');
		}

		return self::$instance;
	}



	/* ~~~ Start/Stop tracing part ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	/**
	 * Start or continue tracing.
	 *
	 * @param  string|NULL trace title
	 */
	public function start($title = NULL)
	{
		if (!$this->isError) {
			if ($this->state === self::STATE_RUN) {
				$this->pause();
			}

			if ($this->state === self::STATE_STOP) {
				$this->titles = array($title);
				xdebug_start_trace($this->traceFile, XDEBUG_TRACE_COMPUTERIZED);

			} elseif ($this->state === self::STATE_PAUSE) {
				$this->titles[] = $title;
				xdebug_start_trace($this->traceFile, XDEBUG_TRACE_COMPUTERIZED | XDEBUG_TRACE_APPEND);
			}

			$this->state = self::STATE_RUN;
		}
	}



	/**
	 * Pause tracing.
	 */
	public function pause()
	{
		if ($this->state === self::STATE_RUN) {
			xdebug_stop_trace();
			$this->state = self::STATE_PAUSE;
		}
	}



	/**
	 * Stop tracing.
	 */
	public function stop()
	{
		if ($this->state === self::STATE_RUN) {
			xdebug_stop_trace();
		}

		$this->state = self::STATE_STOP;
	}



	/*~~~ Rendering part ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	/**
	 * Lazy error template cooking.
	 */
	public function getErrorTemplate()
	{
		if ($this->lazyErrorTemplate === NULL) {
			$this->lazyErrorTemplate = new FileTemplate(__DIR__ . '/error.latte');
			$this->lazyErrorTemplate->registerFilter(new Engine);
		}

		return $this->lazyErrorTemplate;
	}



	/**
	 * Lazy content template cooking.
	 */
	public function getTemplate()
	{
		if ($this->lazyTemplate === NULL) {
			$this->lazyTemplate = new FileTemplate(__DIR__ . '/content.latte');
			$this->lazyTemplate->registerFilter(new Engine);

			// Before [https://github.com/nette/nette/commit/ba80a1923e39cd56c3c35a6bbe26d44f1c52ff04] compatibility
			$helpersClass = class_exists('Nette\Templating\Helpers') ? 'Nette\Templating\Helpers::loader' : 'Nette\Templating\DefaultHelpers::loader';
			$this->lazyTemplate->registerHelperLoader($helpersClass);

			$this->lazyTemplate->registerHelper('time', array($this, 'timeHelper'));
			$this->lazyTemplate->registerHelper('timeClass', array($this, 'timeClassHelper'));
			$this->lazyTemplate->registerHelper('basename', array($this, 'basenameHelper'));
		}

		return $this->lazyTemplate;
	}



	/**
	 * Template helper converts seconds to ns, us, ms, s.
	 *
	 * @param  float time interval in seconds
	 * @param  decimal part precision
	 * @return string formated time
	 */
	public function timeHelper($time, $precision = 0)
	{
		$units = 's';
		if ($time < 0.000001) {	// <1us
			$units = 'ns';
			$time *= 1000000000;

		} elseif ($time < 0.001) { // <1ms
			$units = "\xc2\xb5s";
			$time *= 1000000;

		} elseif ($time < 1) { // <1s
			$units = 'ms';
			$time *= 1000;
		}

		return round($time, $precision) . ' ' . $units;
	}



	/**
	 * Template helper converts seconds to HTML class.
	 *
	 * @param  float time interval in seconds
	 * @param  float over this value is interval classified as slow
	 * @param  float under this value is interval classified as fast
	 * @return string
	 */
	public function timeClassHelper($time, $slow = NULL, $fast = NULL)
	{
		$slow = $slow ?: 0.02;	// 20ms
		$fast = $fast ?: 0.001;	//  1ms

		if ($time <= $fast) {
			return 'timeFast';

		} elseif ($time <= $slow) {
			return 'timeMedian';
		}

		return 'timeSlow';
	}



	/**
	 * Template helper extracts base filename from file path.
	 *
	 * @param  string path to file
	 * @return string
	 */
	public function basenameHelper($path)
	{
		return basename($path);
	}



	/**
	 * Sets internal error variables.
	 *
	 * @param  string error message
	 * @param  array error_get_last()
	 */
	protected function setError($message, array $lastError = NULL)
	{
		$this->isError = TRUE;
		$this->errMessage = $message;

		if ($lastError !== NULL) {
			$this->errMessage .= ': ' . $lastError['message'];
			$this->errFile = $lastError['file'];
			$this->errLine = $lastError['line'];
		}
	}



	/**
	 * Render error message.
	 *
	 * @return  string rendered error template
	 */
	protected function renderError()
	{
		$template = $this->getErrorTemplate();
		$template->errMessage = $this->errMessage;
		$template->errFile = $this->errFile;
		$template->errLine = $this->errLine;

		ob_start();
		$template->render();
		return ob_get_clean();
	}



	/**
	 * Implements Nette\Diagnostics\IBarPanel
	 */
	public function getTab()
	{
		$dataUri = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAA7DAAAOwwHHb6hkAAAB2ElEQVQ4jaWS309SYRjHvemura2fE8tQJA/ojiBEoQ3n2uqm2mytixot10VbF7W6a5ZmukiWoUaGGCXnMAacAxM6tIPZD50X9ld94rSBMU4/lhffd++zd89nz/f7Pi1Ay25Uv4g2S7Uyrn9vsuzbQwNg6m6Q71qKK34b7m7rbyH2I3u5dSmAEn/JSMBFHTAo2tn+rLEuR3h17zJD3p4mSF/HIbKJKJW1dTR5kSGPcwdgyG1rZaOskow8YcGAeBx1iGg9iLoSQynqqEsznO7tbLRQU7+9jW+lDInwI6IPruJzWnEc3U9ejpMpaCiLzxhwCQ3TNfl0d1nQlSTS7GNSU7fJJpcplCrkY8+bmk0BNciq9AatXKFU2SAVmcDrtJmGawrwO9tRpTgl/StqLkf6dYh+4fi/AbzdbajJJQrlT+RiYZTJGyTmQ2RjL/CLXX+2cMrRTjH9DrXq+ePKHK7Ow3gEKzN3RpibHiMdDTHYZzcP0Scco5iRyH9YQ5cWMOram/Gly2M3mX54n7ezTxn4ZZKfx7DXwfYXna3NzWrzPP7e5sDOuAXkiVHkyDjx8DhnfT07ixS8EGBr9T3B8ycRT5iHZaij9QDXzvlQo5NcvzjcuIn/q10DfgAvvbB+PnEGHAAAAABJRU5ErkJggg==';
		return "<img src=\"$dataUri\">";
	}



	/**
	 * Implements Nette\Diagnostics\IBarPanel
	 */
	public function getPanel()
	{
 		$this->stop();

		if ($this->isError) {
			return $this->renderError();
		}

		$parsingStart = microtime(TRUE);

		$fd = @fopen($this->traceFile . '.xt', 'rb');
		if ($fd === FALSE) {
			$this->setError("Cannot open trace file '$this->traceFile.xt'", error_get_last());

		} elseif (!($traceFileSize = filesize($this->traceFile . '.xt'))) {
			$this->setError("Trace file '$this->traceFile.xt' is empty");

		} elseif (!preg_match('/^Version: 2\..*/', (string) fgets($fd, self::$traceLineLength))) {
			$this->setError('Trace file version line mischmasch');

		} elseif (!preg_match('/^File format: 2/', (string) fgets($fd, self::$traceLineLength))) {
			$this->setError('Trace file format line mischmasch');

 		} else {
			while (($line = fgets($fd, self::$traceLineLength)) !== FALSE) {
				if (strncmp($line, 'TRACE START', 11) === 0) {	// TRACE START line
					$this->openTrace();

				} elseif (strncmp($line, 'TRACE END', 9) === 0) {	// TRACE END line
					$this->closeTrace();

				} elseif ($this->isTraceOpened()) {
					$line = rtrim($line, "\r\n");

					$cols = explode("\t", $line);
					if (!strlen($cols[0]) && count($cols) === 5) {	// last line before TRACE END
/*
						$record = (object) array(
							'time' => (float) $cols[3],
							'memory' => (float) $cols[4],
						);
						$this->addRecord($record, TRUE);
*/
						continue;

					} else {
						$record = (object) array(
							'level' => (int) $cols[0],
							'id' => (float) $cols[1],
							'isEntry' => !$cols[2],
							'exited' => FALSE,
							'time' => (float) $cols[3],
							'exitTime' => NULL,
							'deltaTime' => NULL,
							'memory' => (float) $cols[4],
							'exitMemory' => NULL,
							'deltaMemory' => NULL,
						);

						if ($record->isEntry) {
							$record->function = $cols[5];
							$record->isInternal = !$cols[6];
							$record->includeFile = strlen($cols[7]) ? $cols[7] : NULL;
							$record->filename = $cols[8];
							$record->line = $cols[9];
							$record->evalInfo = '';

							if (strcmp(substr($record->filename, -13), "eval()'d code") === 0) {
								preg_match('/(.*)\(([0-9]+)\) : eval\(\)\'d code$/', $record->filename, $match);
								$record->evalInfo = "- eval()'d code ($record->line)";
								$record->filename = $match[1];
								$record->line = $match[2];
							}
						}

						$this->addRecord($record);
					}
				}
 			}

 			$this->closeTrace();	// in case of non-complete trace file

			$template = $this->getTemplate();
			$template->traces = $this->traces;
			$template->indents = $this->indents;
			$template->titles = $this->titles;
		}

		if ($this->isError) {
			return $this->renderError();
		}

		$template->parsingTime = microtime(TRUE) - $parsingStart;
		$template->traceFileSize = $traceFileSize;

		ob_start();
		$template->render();
		return ob_get_clean();
	}



	/**
	 * Sets trace and indent references.
	 */
	protected function openTrace()
	{
		$index = count($this->traces);

		$this->traces[$index] = array();
		$this->trace =& $this->traces[$index];

		$this->indents[$index] = array();
		$this->indent =& $this->indents[$index];
	}



	/**
	 * Unset trace and indent references and compute indents.
	 */
	protected function closeTrace()
	{
		if ($this->trace !== NULL) {
			foreach ($this->trace AS $id => $record) {
				if (!$record->exited) {	// last chance to filter non-exited records by FILTER_EXIT callback
					$remove = FALSE;
					foreach ($this->filterExitCallbacks AS $callback) {
						$result = (int) call_user_func($callback, $record, FALSE, $this);
						if ($result & self::SKIP) {
							$remove = TRUE;
						}

						if ($result & self::STOP) {
							break;
						}
					}

					if ($remove) {
						unset($this->trace[$id]);
						continue;
					}
				}

				$this->indent[$record->level] = 1;
			}

			if (count($this->indent)) {
				ksort($this->indent);
				$this->indent = array_combine(array_keys($this->indent), range(0, count($this->indent) - 1));
			}

			$null = NULL;
			$this->trace =& $null;
			$this->indent =& $null;
		}
	}



	/**
	 * Check if internal references are sets.
	 * @return bool
	 */
	protected function isTraceOpened()
	{
		return $this->trace !== NULL;
	}



	/**
	 * Push parsed trace file line into trace stack.
	 *
	 * @param  stdClass parsed trace file line
	 */
	protected function addRecord(\stdClass $record)
	{
		if ($record->isEntry) {
			$add = TRUE;
			foreach ($this->filterEntryCallbacks AS $callback) {
				$result = (int) call_user_func($callback, $record, TRUE, $this);
				if ($result & self::SKIP) {
					$add = FALSE;
				}

				if ($result & self::STOP) {
					break;
				}
			}

			if ($add) {
				$this->trace[$record->id] = $record;
			}

		} elseif (isset($this->trace[$record->id])) {
			$entryRecord = $this->trace[$record->id];

			$entryRecord->exited = TRUE;
			$entryRecord->exitTime = $record->time;
			$entryRecord->deltaTime = $record->time - $entryRecord->time;
			$entryRecord->exitMemory = $record->memory;
			$entryRecord->deltaMemory = $record->memory - $entryRecord->memory;

			$remove = FALSE;
			foreach ($this->filterExitCallbacks AS $callback) {
				$result = (int) call_user_func($callback, $entryRecord, FALSE, $this);
				if ($result & self::SKIP) {
					$remove = TRUE;
				}

				if ($result & self::STOP) {
					break;
				}
			}

			if ($remove) {
				unset($this->trace[$record->id]);
			}
		}
	}



	/* ~~~ Trace records filtering ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	/**
	 * Setting of default filtering callback.
	 *
	 * @param  bool skip or not
	 * @param  string
	 * @return Panel\XDebugTrace
	 */
	public function skip($type, $skip)
	{
		if (!array_key_exists($type, $this->skipOver)) {
			throw new Nette\InvalidArgumentException("Unknown skip type '$type'. Use one of [" . implode(', ', array_keys($this->skipOver)) . ']');
		}

		$this->skipOver[$type] = (bool) $skip;
		return $this;
	}



	/**
	 * Shortcut to self::skip('phpInternals', bool)
	 *
	 * @param  bool skip PHP internal functions?
	 * @return Panel\XDebugTrace
	 */
	public function skipInternals($skip)
	{
		return $this->skip('phpInternals', $skip);
	}



	/**
	 * Default filtering callback.
	 *
	 * @param  stdClass trace file record
	 * @return int bitmask of self::SKIP, self::STOP
	 */
	protected function defaultFilterCb(\stdClass $record)
	{
		if ($this->skipOver['phpInternals'] && $record->isInternal) {
			return self::SKIP;
		}

		if ($this->skipOver['XDebugTrace']) {
			if ($record->filename === __FILE__) {
				return self::SKIP;
			}

			if (strncmp($record->function, 'Panel\\XDebugTrace::', 19) === 0) {
				return self::SKIP;
			}

			if (strncmp($record->function, 'Panel\\XDebugTrace->', 19) === 0) {
				return self::SKIP;
			}
		}

		if ($this->skipOver['Nette']) {
			if (strncmp($record->function, 'Nette\\', 6) === 0) {
				return self::SKIP;
			}
		}

		if ($this->skipOver['callbacks']) {
			if ($record->function === 'callback' || $record->function === '{closure}') {
				return self::SKIP;
			}
		}

		if ($this->skipOver['includes']) {
			if ($record->includeFile !== NULL) {
				return self::SKIP;
			}
		}
	}



	/**
	 * Register own filter callback.
	 *
	 * @param  callback(stdClass $record, bool $onEntry, \Panel\XDebugTrace $this)
	 * @param  int bitmask of self::FILTER_*
	 */
	public function addFilterCallback($callback, $flags = NULL)
	{
		$flags = (int) $flags;

		if ($flags & self::FILTER_REPLACE_ENTRY) {
			$this->filterEntryCallbacks = array();
		}

		if ($flags & self::FILTER_REPLACE_EXIT) {
			$this->filterExitCallbacks = array();
		}

		// Called when entry records came
		if (($flags & self::FILTER_ENTRY) || !($flags & self::FILTER_EXIT)) {
			if ($flags & self::FILTER_APPEND_ENTRY) {
				$this->filterEntryCallbacks[] = $callback;

			} else {
				array_unshift($this->filterEntryCallbacks, $callback);
			}
		}

		// Called when exit records came
		if ($flags & self::FILTER_EXIT) {
			if ($flags & self::FILTER_APPEND_EXIT) {
				$this->filterExitCallbacks[] = $callback;

			} else {
				array_unshift($this->filterExitCallbacks, $callback);
			}
		}
	}



	/**
	 * Replace all filter callbacks by this one.
	 *
	 * @param  callback(stdClass $record, bool $onEntry, \Panel\XDebugTrace $this)
	 * @param  int bitmask of self::FILTER_*
	 */
	public function setFilterCallback($callback, $flags = NULL)
	{
		$flags = ((int) $flags) | self::FILTER_REPLACE;
		return $this->addFilterCallback($callback, $flags);
	}



	/* ~~~ Filtering callback shortcuts ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
	/**
	 * Trace all.
	 */
	public function traceAll()
	{
		$cb = function () {
			return NULL;
		};

		$this->setFilterCallback($cb, self::FILTER_BOTH);
	}



	/**
	 * Trace function by name.
	 *
	 * @param  string|array name of function or pair array(class, method)
	 * @param  bool show inside function trace too
	 * @param  bool show internals in inside function trace
	 */
	public function traceFunction($name, $inDetail = FALSE, $showInternals = FALSE)
	{
		if (is_array($name)) {
			$name1 = implode('::', $name);
			$name2 = implode('->', $name);
		} else {
			$name1 = $name2 = (string) $name;
		}

		$cb = function(\stdClass $record, $onEntry) use ($name1, $name2, $inDetail, $showInternals) {
			static $cnt = 0;

			if ($record->function === $name1 || $record->function === $name2) {
				$cnt += $onEntry ? 1 : -1;
				return NULL;
			}

			return ($inDetail && $cnt && ($showInternals || !$record->isInternal)) ? NULL : XDebugTrace::SKIP;
		};

		$this->setFilterCallback($cb, self::FILTER_BOTH);
	}



	/**
	 * Trace function which name is expressed by PCRE reqular expression.
	 *
	 * @param  string regular expression
	 * @param  bool show inside function trace too
	 * @param  bool show internals in inside function trace
	 */
	public function traceFunctionRe($re, $inDetail = FALSE, $showInternals = FALSE)
	{
		$cb = function(\stdClass $record, $onEntry) use ($re, $inDetail, $showInternals) {
			static $cnt = 0;

			if (preg_match($re, $record->function)) {
				$cnt += $onEntry ? 1 : -1;
				return NULL;
			}

			return ($inDetail && $cnt && ($showInternals || !$record->isInternal)) ? NULL : XDebugTrace::SKIP;
		};

		$this->setFilterCallback($cb, self::FILTER_BOTH);
	}



	/**
	 * Trace functions running over/under the time.
	 *
	 * @param  float delta time
	 * @param  bool  TRUE = over the delta time, FALSE = under the delta time
	 */
	public function traceDeltaTime($delta, $over = TRUE)
	{
		if (is_string($delta)) {
			static $multipliers = array(
				's' => 1,
				'ms' => 0.001,
				'us' => 0.000001,
				'ns' => 0.000000001,
			);

			foreach ($multipliers as $suffix => $multipler) {
				if (substr_compare($delta, $suffix, -2, 2, TRUE) === 0) {
					$delta = substr($delta, 0, -2) * $multipler;
					break;
				}
			}
		}
		$delta = (float) $delta;

		$cb = function(\stdClass $record) use ($delta, $over) {
			if ($over) {
				if ($record->deltaTime < $delta) {
        			return XDebugTrace::SKIP;
        		}
        	} else {
				if ($record->deltaTime > $delta) {
        			return XDebugTrace::SKIP;
        		}
        	}
		};

		$this->setFilterCallback($cb, self::FILTER_EXIT);
	}



	/**
	 * Trace functions which consumes over/under the memory.
	 *
	 * @param  float delta memory
	 * @param  bool  TRUE = over the delta memory, FALSE = under the delta memory
	 */
	public function traceDeltaMemory($delta, $over = TRUE)
	{
		if (is_string($delta)) {
			static $multipliers = array(
				'b' => 1,
				'kb' => 1000,
				'mb' => 1000000,
				'gb' => 1000000000,
			);

			foreach ($multipliers as $suffix => $multipler) {
				if (substr_compare($delta, $suffix, -2, 2, TRUE) === 0) {
					$delta = substr($delta, 0, -2) * $multipler;
					break;
				}
			}
		}
		$delta = (float) $delta;

		$cb = function(\stdClass $record) use ($delta, $over) {
			if ($over) {
				if ($record->deltaMemory < $delta) {
        			return XDebugTrace::SKIP;
        		}
        	} else {
				if ($record->deltaMemory > $delta) {
        			return XDebugTrace::SKIP;
        		}
        	}
		};

		$this->setFilterCallback($cb, self::FILTER_EXIT);
	}

}