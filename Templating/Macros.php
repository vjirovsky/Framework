<?php

namespace Schmutzka\Templating;

use Nette;
use Nette\Latte\MacroNode;
use Nette\Latte\PhpWriter;
use Nette\Latte\Compiler;
use Nette\Latte\Macros\MacroSet;


class Macros extends MacroSet
{

	/**
	 * @param  Compiler
	 */
	public static function install(Compiler $compiler)
	{
		$set = new static($compiler);
		$set->addMacro('phref', NULL, NULL, array($set, 'macroPhref'));
		$set->addMacro('current', NULL, NULL, array($set, 'macroCurrent'));
		$set->addMacro('id', NULL, NULL, array($set, 'macroId'));
		$set->addMacro('description', '$__desc = $form[%node.word]->getOption("description"); echo %escape($__desc);');
	}


	/**
	 * n:phref="..."
	 */
	public function macroPhref(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('echo \' href="\' . %escape($_presenter->link(%node.word, %node.array?)) . \'"\'');

	}


	/**
	 * n:current="..."
	 */
	public function macroCurrent(MacroNode $node, PhpWriter $writer)
	{
		$node = $node->args;
		return $writer->write('if ($_l->tmp = array_filter(array($presenter->isLinkCurrent() ? "'. $node .'" :null))) echo \' class="\' . %escape(implode(" ", array_unique($_l->tmp))) . \'"\'');
	}


	/**
	 * n:id="..."
	 */
	public function macroId(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write('if ($_l->tmp = array_filter(%node.array)) echo \' id="\' . %escape(implode(" ", array_unique($_l->tmp))) . \'"\'');
	}

}
