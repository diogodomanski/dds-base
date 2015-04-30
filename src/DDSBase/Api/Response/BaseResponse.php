<?php

namespace DDSBase\Api\Response;

/**
 * Description of BaseResponse
 *
 * @author domanski
 */
class BaseResponse extends AbstractResponse {

	/**
	 *
	 * @var int
	 */
	private $code;

	/**
	 *
	 * @var string
	 */
	private $message;

	/**
	 *
	 * @var array
	 */
	private $data;

	/**
	 *
	 * @var BaseResponsePagination
	 */
	private $pagination;

	public function __construct(array $options = array()) {
		$this->pagination = new BaseResponsePagination();
		parent::__construct($options);
	}

	/**
	 * 
	 * @return int
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * 
	 * @param int $code
	 * @return \Api\Response\BaseResponse
	 */
	public function setCode($code) {
		$this->code = $code;
		return $this;
	}

	/**
	 * 
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * 
	 * @param string $message
	 * @return \Api\Response\BaseResponse
	 */
	public function setMessage($message) {
		$this->message = $message;
		return $this;
	}

	/**
	 * 
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * 
	 * @param array $data
	 * @return \Api\Response\BaseResponse
	 */
	public function setData($data) {
		$this->data = $data;
		return $this;
	}

	/**
	 * 
	 * @return BaseResponsePagination
	 */
	public function getPagination() {
		return $this->pagination;
	}

	/**
	 * 
	 * @param \Api\Response\BaseResponsePagination $pagination
	 * @return \Api\Response\BaseResponse
	 */
	public function setPagination(BaseResponsePagination $pagination) {
		$this->pagination = $pagination;
		return $this;
	}

}
