<?php

namespace DDSBase\Stdlib;

/**
 * Description of Entity
 *
 * @author domanski
 */
abstract class Entity {
	/**
	 * 
	 * @param \Doctrine\ORM\EntityManager $em A reference to the entity manager
	 * @param string $entityName The name of the entity to be created a new referente (for example, \Test\Entity\Test)
	 * @param array $data A reference to the data to be used
	 * @param array $checkFields List of field names to be used to check the entity ID
	 * @param string $targetField Target field where the new reference will be set
	 * @return mixed|null A reference to the especified entity, if any of the fields has a valid value
	 */
	public static function setReference(\Doctrine\ORM\EntityManager &$em, $entityName, array &$data, array $checkFields, $targetField) {
		foreach($checkFields as $field) {
			if(isset($data[$field]) && !empty($data[$field])) {
				return $data[$targetField] = $em->getReference($entityName, $data[$field]);
			}
		}
		
		return null;
	}
}
