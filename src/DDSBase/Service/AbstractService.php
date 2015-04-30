<?php

namespace DDSBase\Service;

use Zend\Stdlib\Hydrator;

/**
 * Description of AbstractService
 *
 * @author domanski
 */
abstract class AbstractService {

	const START_SERVER_RECORD_ID = 10000;

	/**
	 *
	 * @var \Doctrine\ORM\EntityManager
	 */
	protected $em;

	/**
	 *
	 * @var string
	 */
	protected $entityName;

	public function __construct(\Doctrine\ORM\EntityManager $em) {
		$this->em = $em;
	}

	/**
	 * 
	 * @return \Doctrine\ORM\EntityRepository The repository class
	 */
	public function getRepository() {
		return $this->em->getRepository($this->entityName);
	}

	public function insert(array $data) {
		// If entity class has hydrate method
		if (method_exists($this->entityName, 'hydrate')) {
			$entity = new $this->entityName();
			$entity->hydrate($data, $this->em);
		} else
			$entity = new $this->entityName($data);

		// If entity class has insertDate attribute
		if (method_exists($entity, 'setInsertDate') && $entity->getInsertDate() == null)
			$entity->setInsertDate();

		// If entity class has updateDate attribute
		if (method_exists($entity, 'setUpdateDate'))
			$entity->setUpdateDate();

		// If entity's service class has validate method
		if (method_exists($this, 'validate'))
			$this->validate($entity);

		$this->em->persist($entity);
		$this->em->flush($entity);

		return $entity;
	}

	public function update(array $data) {
		$entity = $this->em->getReference($this->entityName, $data);

		// If entity class has hydrate method
		if (method_exists($this->entityName, 'hydrate'))
			$entity->hydrate($data, $this->em);
		else {
			$hydrator = new Hydrator\ClassMethods();
			$hydrator->hydrate($data, $entity);
		}

		// If entity class has updateDate attribute
		if (method_exists($entity, 'setUpdateDate'))
			$entity->setUpdateDate();

		// If entity's service class has validate method
		if (method_exists($this, 'validate'))
			$this->validate($entity);

		$this->em->persist($entity);
		$this->em->flush($entity);

		return $entity;
	}

	public function delete($id) {
		$entity = $this->em->getReference($this->entityName, $id);

		if ($entity) {
			if (!method_exists($entity, "getIsSystemRecord") || (method_exists($entity, "getIsSystemRecord") && !$entity->getIsSystemRecord())) {
				if (method_exists($entity, 'setDeleteDate')) {
					$entity->setDeleteDate('now');
					$this->em->persist($entity);
				} else {
					$this->em->remove($entity);
				}

				$this->em->flush($entity);
			}

			return $id;
		}
	}

//	public function save(array $data) {
//		$repo = $this->em->getRepository($this->entityName);
//		$save = true;
//		$entity = null;
//
//		// Isn't this a new mobile record?
//		if (!$this->isNewMobileRecord($data)) {
//			$entity = $repo->findOneBy(array(
//				"id" => $data['server_record_id'],
//				"deleteDate" => null
//			));
//
//			// If no record was found
//			if (empty($entity))
//				throw new \MOBBase\Exception\MOBBaseException(get_class($this) . ' Record not found', 2, $data['id']);
//
//			unset($data['insert_date']);
//			unset($data['client_record_id']);
//			unset($data['server_record_id']);
//			unset($data['client_device_id']);
//		} else {
//			// Check if this record was already synchronized
//			if (isset($data['server_record_id']) && $data['server_record_id'] >= self::START_SERVER_RECORD_ID)
//				$entity = $repo->findOneById($data['server_record_id']);
//			//else {
//			//	$entity = $repo->findOneBy(array(
//			//		'clientRecordId' => $this->getMobileId($data),
//			//		'clientDeviceId' => $data['client_device_id'],
//			//		'deleteDate' => null
//			//	));
//			//}
//			// If this is a new record
//			if (empty($entity)) {
//				$data['client_record_id'] = $data['id'];
//				unset($data['id']);
//				//unset($data['update_date']);
//
//				$entity = new $this->entityName();
//				//$entity->hydrate($data, $this->em);
//			} else {
//				$mobileUpdateDate = \MOBBase\Stdlib\DateTime::getDate($data['update_date']);
//
//				// If server record is more updated than mobile one
//				if ($mobileUpdateDate < $entity->getUpdateDate())
//					$save = false;
//				else {
//					unset($data['client_record_id']);
//				}
//			}
//		}
//
//		if ($save) {
//			// Prepare and persist entity
//			unset($data['id']);
//			$entity->hydrate($data, $this->em);
//
//			if (method_exists($this, "validate"))
//				$this->validate($entity);
//
//			$this->em->persist($entity);
//			$this->em->flush($entity);
//		}
//
//		return $entity;
//	}
//
//	/**
//	 * 
//	 * @param array $data
//	 * @return boolean
//	 */
//	protected function isNewMobileRecord(array &$data) {
//		//return (empty($data['client_record_id']) && !empty($data['client_device_id']) && $data['id'] < self::START_SERVER_RECORD_ID);
//		return (empty($data['server_record_id']) && !empty($data['client_device_id']) && $data['id'] < self::START_SERVER_RECORD_ID);
//	}
//
//	/**
//	 * 
//	 * @param array $data
//	 * @return int|null
//	 */
//	protected function getMobileId(array &$data) {
//		if (array_key_exists('id', $data) && $this->isNewMobileRecord($data))
//			return $data['id'];
//
//		if (array_key_exists('client_record_id', $data))
//			return $data['client_record_id'];
//
//		return null;
//	}

}
