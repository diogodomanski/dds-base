<?php

namespace DDSBase;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ControllerPluginProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\ApplicationInterface;

/**
 * DDSBase module
 *
 * @author Diogo Domanski de Souza <diogo.domanski@gmail.com>
 */
class Module implements
AutoloaderProviderInterface, BootstrapListenerInterface, ConfigProviderInterface, ControllerPluginProviderInterface, ViewHelperProviderInterface {

	/**
	 * {@inheritDoc}
	 */
	public function onBootstrap(EventInterface $event) {
		
	}

	/**
	 * {@inheritDoc}
	 */
	public function getViewHelperConfig() {
		return array(
			'factories' => array(
			)
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getControllerPluginConfig() {
		return array(
			'factories' => array(
			)
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
				),
			),
		);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getConfig() {
		return include __DIR__ . '/../../config/module.config.php';
	}

}
