<?php

namespace DDSBase\Stdlib;

class Url {

	/**
	 * 
	 * @return string
	 */
	public static function getBaseUrl() {
		$self = filter_input(INPUT_SERVER, "PHP_SELF");
		$self = substr($self, 0, strpos($self, "index.php"));
		$length = strlen($self);

		if ($length > 0 && $self[$length - 1] != "/")
			$self .= "/";

		return 'http://' . filter_input(INPUT_SERVER, "HTTP_HOST") . $self;
	}

}

