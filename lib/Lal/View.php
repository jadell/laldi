<?php
namespace Lal;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Helper class to handle rendering output
 */
class View
{
	protected $templatePath;

	public function __construct()
	{
		$this->templatePath = __DIR__.'/../../templates/';
	}

	public function render($name, $context=array())
	{
		$templateFile = $this->templatePath.$name.'.phtml';
		extract($context);
		ob_start();
		include $templateFile;
		$rendered = ob_get_clean();
		return $rendered;
	}

	public function redirect($url, $status=302)
	{
        return new RedirectResponse($url, $status);
	}

	public function escape($var)
	{
		return htmlspecialchars($var, ENT_COMPAT);
	}
}