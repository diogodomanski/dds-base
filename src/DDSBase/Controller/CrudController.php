<?php

namespace DDSBase\Controller;

use Zend\Mvc\Controller\AbstractActionController,
	Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator,
	Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator,
	DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;

/**
 * Description of CrudController
 *
 * @author domanski
 */
abstract class CrudController extends AbstractActionController {

	const METHOD_JSON = 'toJson';
	const METHOD_ARRAY = 'toArray';
	const METHOD_ARRAY_REDUCED = 'toArrayReduced';
	const CLIENT_RECORD_ID_WEB = 9999;
	const CLIENT_DEVICE_ID_WEB = "WEB";

	/**
	 *
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 *
	 * @var string
	 */
	protected $serviceName;

	/**
	 *
	 * @var string
	 */
	protected $entityName;

	/**
	 *
	 * @var string
	 */
	protected $routeName;

	/**
	 *
	 * @var string
	 */
	protected $controllerName;

	/**
	 *
	 * @var type 
	 */
	protected $userIdentity = null;

	/**
	 *
	 * @var int
	 */
	protected $ITEM_COUNT_PER_PAGE = null;

	/**
	 *
	 * @var int
	 */
	protected $SELECT_ITEM_COUNT_PER_PAGE = null;

	/**
	 *
	 * @var array
	 */
	protected $ACCEPT_MAPPING = array(
		'Zend\View\Model\ViewModel' => array(
			'text/html'
		),
		'Zend\View\Model\JsonModel' => array(
			'application/json'
		)
	);

	/**
	 * 
	 * @return int
	 */
	public function getItemCountPerPage() {
		if ($this->ITEM_COUNT_PER_PAGE === null) {
			$config = $this->getServiceLocator()->get('Config');
			$this->ITEM_COUNT_PER_PAGE = $config['paginator']['item_count_per_page'];
			$this->SELECT_ITEM_COUNT_PER_PAGE = $config['paginator']['select_item_count_per_page'];
		}

		return $this->ITEM_COUNT_PER_PAGE;
	}

	/**
	 * 
	 * @return int
	 */
	public function getSelectItemCountPerPage() {
		if ($this->SELECT_ITEM_COUNT_PER_PAGE === null) {
			$config = $this->getServiceLocator()->get('Config');
			$this->ITEM_COUNT_PER_PAGE = $config['paginator']['item_count_per_page'];
			$this->SELECT_ITEM_COUNT_PER_PAGE = $config['paginator']['select_item_count_per_page'];
		}

		return $this->SELECT_ITEM_COUNT_PER_PAGE;
	}

	/**
	 * 
	 * @return \Doctrine\ORM\EntityManager
	 */
	protected function getEm() {
		if (empty($this->em))
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

		return $this->em;
	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	protected function prepareJsonResult(Paginator $paginator, $method = self::METHOD_JSON) {
		try {
			if ($method !== self::METHOD_ARRAY && $method !== self::METHOD_ARRAY_REDUCED && $method !== self::METHOD_JSON)
				throw new \Exception("Invalid method type");

			$result = array();
			foreach ($paginator as $item) {
				$result[] = $item->$method();
			}

			return $result;
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function indexAction() {
		try {
			return new ViewModel(array());
		} catch (\Exception $e) {
			throw $e;
		}
	}

	/**
	 * 
	 * @return \Zend\View\Model\ViewModel
	 */
	public function listAction() {
		try {

			/**
			 * @var \Zend\View\Model\JsonModel
			 */
			$viewModel = $this->acceptableViewModelSelector($this->ACCEPT_MAPPING);

			if (!($viewModel instanceof \Zend\View\Model\JsonModel)) {
				throw new \Exception("Invalid request type", 1000);
			}

			$search = $this->params()->fromQuery('q', null);

			$query = $this->getEm()
					->getRepository($this->entityName)
					->getFindAllQuery($search);

			$page = $this->params()->fromRoute('page');

			$doctrinePaginator = new DoctrinePaginator($query);
			$paginatorAdapter = new PaginatorAdapter($doctrinePaginator);
			$paginator = new Paginator($paginatorAdapter);
			$paginator->setCurrentPageNumber($page)
					->setDefaultItemCountPerPage($this->getItemCountPerPage());


			$response = new \Api\Response\BaseResponse();
			$response->setCode(0)
					->setData($this->prepareJsonResult($paginator))
					->setMessage("")
					->getPagination()
					->setCurrentPage($paginator->getCurrentPageNumber())
					->setPageSize($paginator->getItemCountPerPage())
					->setTotalItems($paginator->getTotalItemCount());

			return $viewModel->setVariables($response);
		} catch (\Exception $e) {
			$response = new \Api\Response\BaseResponse();
			$response->setCode($e->getCode())
					->setData(null)
					->setMessage($e->getMessage());

			$this->getResponse()->setStatusCode(403);

			return new \Zend\View\Model\JsonModel($response);
		}
	}

	/**
	 * 
	 * @return \Zend\View\Model\JsonModel
	 * @throws \Exception
	 */
	public function saveAction() {
		try {
			/**
			 * @var \Zend\View\Model\JsonModel
			 */
			$viewModel = $this->acceptableViewModelSelector($this->ACCEPT_MAPPING);

			if (!($viewModel instanceof \Zend\View\Model\JsonModel)) {
				throw new \Exception("Invalid request type", 1000);
			}

			$params = json_decode($this->getRequest()->getContent(), true);

			/**
			 * @var \DDSBase\Service\AbstractService
			 */
			$service = $this->getServiceLocator()->get($this->serviceName);

			$entity = null;

			if (empty($params['id']))
				$entity = $service->insert($params);
			else
				$entity = $service->update($params);

			if (empty($entity))
				throw new \Exception("Operation failed", 1002);


			$this->getEm()->clear();
			$entity = $this->em->find($this->entityName, $entity->getId());
			$response = new \DDSBase\Api\Response\BaseResponse();
			$response->setCode(0)
					->setData($entity->toVO())
					->setMessage("");

			return $viewModel->setVariables($response);
		} catch (\Exception $e) {
			$response = new \DDSBase\Api\Response\BaseResponse();
			$response->setCode($e->getCode())
					->setData(null)
					->setMessage($e->getMessage());

			$this->getResponse()->setStatusCode(403);

			return new \Zend\View\Model\JsonModel($response);
		}
	}

	public function deleteAction() {
		try {
			/**
			 * @var \Zend\View\Model\JsonModel
			 */
			$viewModel = $this->acceptableViewModelSelector($this->ACCEPT_MAPPING);

			if (!($viewModel instanceof \Zend\View\Model\JsonModel)) {
				throw new \Exception("Invalid request type", 1000);
			}

			$params = json_decode($this->getRequest()->getContent(), true);

			$service = $this->getServiceLocator()->get($this->serviceName);

			$id = $service->delete($params['id']);


			if (!$id)
				throw new \Exception("Operation failed", 1001);


			$response = new \DDSBase\Api\Response\BaseResponse();
			$response->setCode(0)
					->setData($id)
					->setMessage("");

			return $viewModel->setVariables($response);
		} catch (\Exception $e) {
			$response = new \DDSBase\Api\Response\BaseResponse();
			$response->setCode($e->getCode())
					->setData(null)
					->setMessage($e->getMessage());

			$this->getResponse()->setStatusCode(403);

			return new \Zend\View\Model\JsonModel($response);
		}
	}

	/**
	 * 
	 * @return \User\Entity\User
	 */
	public function getUserIdentity() {
		if ($this->userIdentity == null) {
			$authService = $this->getServiceLocator()->get('zfcuser_auth_service');
			$this->userIdentity = $authService->getIdentity();
		}

		return $this->userIdentity;
	}

//	public function getUserIdentitySubscriber() {
//		if ($this->getUserIdentity() === null)
//			throw new \Exception("Invalid user identity");
//
//
//		return $this->em->getRepository('Subscriber\Entity\Subscriber')
//						->findByUserId($this->getUserIdentity()->getId());
//	}
//
//	public function userIdentityHasRole($role) {
//		/**
//		 * @var \User\Auth\AuthService
//		 */
//		$authService = $this->serviceLocator->get("User\Session\Auth");
//		return $authService->hasRole($role);
//	}

}
