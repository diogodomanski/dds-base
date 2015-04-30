<?php

namespace DDSBase\Api\Response;

/**
 * Description of BaseResponse
 *
 * @author domanski
 */
class BaseResponsePagination extends AbstractResponse {

	/**
	 *
	 * @var int
	 */
	private $totalItems;

	/**
	 *
	 * @var int
	 */
	private $pageSize;

	/**
	 *
	 * @var int
	 */
	private $currentPage;

	public function __construct(array $options = array()) {
		parent::__construct($options);
	}

	/**
	 * 
	 * @return int
	 */
	public function getTotalItems() {
		return $this->totalItems;
	}

	/**
	 * 
	 * @return int
	 */
	public function getPageSize() {
		return $this->pageSize;
	}

	/**
	 * 
	 * @return int
	 */
	public function getCurrentPage() {
		return $this->currentPage;
	}

	/**
	 * 
	 * @param int $totalItems
	 * @return \Api\Response\BaseResponsePagination
	 */
	public function setTotalItems($totalItems) {
		$this->totalItems = $totalItems;
		return $this;
	}

	/**
	 * 
	 * @param int $pageSize
	 * @return \Api\Response\BaseResponsePagination
	 */
	public function setPageSize($pageSize) {
		$this->pageSize = $pageSize;
		return $this;
	}

	/**
	 * 
	 * @param int $currentPage
	 * @return \Api\Response\BaseResponsePagination
	 */
	public function setCurrentPage($currentPage) {
		$this->currentPage = $currentPage;
		return $this;
	}

}
