<?php


namespace Base\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

abstract class AbstractController  extends AbstractActionController
{
	protected $em;
	protected $entity;
	protected $controller;
	protected $route;
	protected $service;
	protected $form;

	abstract function __construct();

	public function indexAction()
	{
		
		$list = $this->getEm()->getRepository($this->entity)->findAll();
		$page = $this->params()->fromRoute('page');

		$paginator = new Paginator(new ArrayAdapter($list));
		$paginator->setCurrentPageNumber($page)->setDefaultItemCountPerPage(10);

		return new ViewModel(array('data'=> $paginator, 'page' => $page));		

	}

	public function inserirAction()
	{
		if(is_string($this->form))
			$form = new $this->form;
		else
			$form = $this->form;

		$request = $this->getRequest();

		if($request->isPost()){
			$form->setData($request->getPost());
			if($form->isValid()){
				$service = $this->getServiceLocator()->get($this->service);

				if($service->save($request->getPost()->toArray())){
					$this->flashMessenger()->addSuccessMessage('Cadastrado!');
				}else{
					$this->flashMessenger()->addErrorMessage('Não foi possível cadastrar!');
				}			

				return $this->redirect->toRoute($this->route, array('controller' => $this->controller));
			}
		}
		if($this->flashMessenger()->hasSuccessMessages()){
			return new ViewModel(array(
				'form'=>$form , 
				'success' => $this->flashMessenger()->getSuccessMessages()
			));
		}
		if($this->flashMessenger()->hasErrorMessages()){
			return new ViewModel(array(
				'form'=>$form , 
				'success' => $this->flashMessenger()->getErrorMessages()
			));
		}

		return new ViewModel(array('form'=>$form));
	}


	public function getEm()
	{
		if($this->em == null){
			$this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		}

		return $this->em;
	}


}
