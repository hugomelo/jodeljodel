<?php
class PapPaperController extends PaperAppController {
	var $name = 'PapPaper';
	var $uses = array('Paper.PapPaper');

	var $components = array('Typographer.TypeLayoutSchemePicker');
	var $helpers = array(
		'Typographer.TypeDecorator' => array(
			'name' => 'Decorator',
			'compact' => false,
			'receive_tools' => true
		),
		'Typographer.*TypeStyleFactory' => array(
			'name' => 'TypeStyleFactory',
			'receive_automatic_classes' => true,
			'receive_tools' => true,
			'generate_automatic_classes' => false //significa que eu que vou produzir as classes automaticas
		),
		'Typographer.*TypeBricklayer' => array(
			'name' => 'Bl',
			'receive_tools' => true,
		),
		'Burocrata.*BuroBurocrata' => array(
			'name' => 'Buro'
		),
		'Kulepona',
		'Corktile.Cork'
	);
	var $layout = 'dinafon';

//	function beforeFilter()
//	{
//		parent::beforeFilter();
//		StatusBehavior::setGlobalActiveStatuses(array(
//			'publishing_status' => array('active' => array('published'), 'overwrite' => true),
//		));
//	}

	function beforeRender()
	{
		parent::beforeRender();
		$this->TypeLayoutSchemePicker->pick('dinafon');
	}

	function index()
	{
		$data = $this->PapPaper->find(
				'all',
				array(
					'contain' => array(
						'AuthAuthor',
						'TagsTag',
						'JourJournal'
					),
					'order' => 'PapPaper.date'
				)
			);

		$this->set('data',$data);
	}

	function view()
	{
		$data = $this->PapPaper->find(
			'first',
			array(
				'contain' => array(
					'AuthAuthor',
					'TagsTag',
					'JourJournal'
				),
				'order' => 'PapPaper.date'
			)
		);

		$this->set('data',$data);
		//$this->set('data',$this->NewsNew->find('first', array('contain' => array('AuthAuthor'))));
	}
}



?>