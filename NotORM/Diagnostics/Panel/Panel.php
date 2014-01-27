<?php

namespace Schmutzka\NotORM\Diagnostics;

use Nette\Diagnostics\IBarPanel;
use Nette\Utils\Strings;


/**
 * @author Štěpán Svoboda
 * @author Michael Moravec
 * @author Patrik Votoček
 * @author Igor Hlina
 * @author Marek Lichtner
 * @author Tomáš Votruba
 */
class Panel implements IBarPanel
{
	/** @var self */
	private static $instance = NULL;

	/** @var array */
	private $queries = array();


	/**
	 * Create singleton instance.
	 * @return Panel
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * @return string
	 */
	public function getId()
	{
		return 'NotORM';
	}


	/**
	 * @return string HTML code for Debugbar
	 */
	public function getTab()
	{
		if (count($this->queries)) {
			return '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAHpJREFUOMvVU8ENgDAIBON8dgY7yU3SHTohfoQUi7FGH3pJEwI9oBwl+j1YDRGR8AIzA+hiAIxLsoOW1R3zB9Cks1VKmaQWXz3wHWEJpBbilF3wivxKB9OdiUfDnJ6Q3RNGyWp3MraytbKqjADkrIvhPYgSDG3itz/TBsqre3ItA1W8AAAAAElFTkSuQmCC">' . count($this->queries) . ' queries';

		} else {
			return NULL;
		}
	}


	/**
	 * @return string HTML code for Debugbar detail
	 */
	public function getPanel()
	{
		if (count($this->queries) == 0) {
			return NULL;
		}

		$i = 0;
		$queries = $this->queries;

		ob_start();
		require_once __DIR__ . '/panel.latte';
		return ob_get_clean();
	}


	/**
	 * @param  string
	 * @param  array
	 */
	public function logQuery($sql, array $params = NULL)
	{
		$this->queries[] = array('sql' => $sql, 'params' => $params);
	}


	/**
	 * @param string
	 * @return string
	 */
	public static function dump($sql)
	{
		$keywords1 = 'CREATE\s+TABLE|CREATE(?:\s+UNIQUE)?\s+INDEX|SELECT|UPDATE|INSERT(?:\s+INTO)?|REPLACE(?:\s+INTO)?|DELETE|FROM|WHERE|HAVING|GROUP\s+BY|ORDER\s+BY|LIMIT|SET|VALUES|LEFT\s+JOIN|INNER\s+JOIN|TRUNCATE';
		$keywords2 = 'ALL|DISTINCT|DISTINCTROW|AS|USING|ON|AND|OR|IN|IS|NOT|NULL|LIKE|TRUE|FALSE|INTEGER|CLOB|VARCHAR|DATETIME|TIME|DATE|INT|SMALLINT|BIGINT|BOOL|BOOLEAN|DECIMAL|FLOAT|TEXT|VARCHAR|DEFAULT|AUTOINCREMENT|PRIMARY\s+KEY';
		$keywords3 = 'LEFT JOIN|WHERE|ORDER BY|LIMIT';

		// insert new lines
		$sql = " $sql ";
		$sql = Strings::replace($sql, "#(?<=[\\s,(])($keywords1)(?=[\\s,)])#", "\n\$1");
		if (strpos($sql, "CREATE TABLE") !== FALSE) {
			$sql = Strings::replace($sql, "#,\s+#i", ", \n");
		}

		// reduce spaces
		$sql = Strings::replace($sql, '#[ \t]{2,}#', " ");

		$sql = wordwrap($sql, 100);
		$sql = htmlSpecialChars($sql);
		$sql = Strings::replace($sql, "#([ \t]*\r?\n){2,}#", "\n");
		$sql = Strings::replace($sql, "#VARCHAR\\(#", "VARCHAR (");

		// syntax highlight
		$sql = Strings::replace($sql, "#(/\\*.+?\\*/)|(\\*\\*.+?\\*\\*)|(?<=[\\s,(])($keywords1)(?=[\\s,)])|(?<=[\\s,(=])($keywords2)(?=[\\s,)=])#s", function ($matches) {
				if (!empty($matches[1])) { // comment
					return '<em style="color:gray">' . $matches[1] . '</em>';
				}

				if (!empty($matches[2])) { // error
					return '<strong style="color:red">' . $matches[2] . '</strong>';
				}

				if (!empty($matches[3])) { // most important keywords
					return '<strong style="color:blue">' . $matches[3] . '</strong>';
				}

				if (!empty($matches[4])) { // other keywords
					return '<strong style="color:green">' . $matches[4] . '</strong>';
				}
			}
		);

		// styling
		$sql = Strings::replace($sql, "~<strong style=\"color:blue\">($keywords3)<\/strong>~i", function ($m) {
			return '<br>' . $m[0];
		});

		$sql = trim($sql);

		return '<code class="dump">' . $sql . "</code>\n";
	}

}
