<?php

namespace DDSBase\Api\Response;

use Zend\Stdlib\Hydrator\Filter\FilterComposite,
	Zend\Stdlib\Hydrator;
use Zend\Stdlib\Hydrator\Filter\GetFilter;
use \ReflectionMethod;

/**
 * Description of Response
 *
 * @author domanski
 */
abstract class AbstractResponse implements \Iterator {

	private $_current;
	private $_attributes;

	public function __construct(array $options = array()) {
		$hydrator = new Hydrator\ClassMethods();
		$hydrator->hydrate($options, $this);

		$filterComposite = new FilterComposite();
		$filterComposite->addFilter("get", new GetFilter());

		$filter = null;
		if ($this instanceof FilterProviderInterface) {
			$filter = new FilterComposite(
					array($this->getFilter()), array(new MethodMatchFilter("getFilter"))
			);
		} else {
			$filter = $filterComposite;
		}


		$this->_current = 0;
		$this->_attributes = array();
		$methods = get_class_methods($this);

		foreach ($methods as $method) {
			if (
				!$filter->filter(
						get_class($this) . '::' . $method
				)
			) {
				continue;
			}

			$reflectionMethod = new ReflectionMethod(get_class($this) . '::' . $method);
			if ($reflectionMethod->getNumberOfParameters() > 0) {
				continue;
			}

			$attribute = $method;
			if (preg_match('/^get/', $method)) {
				$attribute = lcfirst(substr($method, 3));
			}

			$this->_attributes[] = $attribute;
		}
	}

	public function current() {
		$methodName = "get" . ucfirst($this->_attributes[$this->_current]);
		return $this->$methodName();
	}

	public function key() {
		return $this->_attributes[$this->_current];
	}

	public function next() {
		$this->_current++;
	}

	public function rewind() {
		$this->_current = 0;
	}

	public function valid() {
		return ($this->_current < count($this->_attributes));
	}

}
