<?php

interface EntityInterface {
	/**
	 * 
	 * @param array $data
	 * @param \Doctrine\ORM\EntityManager $em
	 */
	public function hydrate(array $data = null, \Doctrine\ORM\EntityManager $em = null);
	
	/**
	 * 
	 * @param boolean $details
	 * @return array
	 */
	public function toArray($details = false);
	
	/**
	 * @return array
	 */
	public function toArrayReduced();
	
	/**
	 * 
	 * @param boolean $details
	 * @return array
	 */
	public function toJson($details = false);
}

