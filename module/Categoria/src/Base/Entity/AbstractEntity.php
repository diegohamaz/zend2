<?php 
namespace Base\Entity;


abstract class AbstractEntity
{
	
	public function _construct(Array $options = array())
	{
		$hydrator = new ClassMethods();
		$hydrator->hydrate($options , $this);
	}

	public function toArray(){
		$hydrator = new ClassMethods();
		return $hydrator->extract($this);
	}

}