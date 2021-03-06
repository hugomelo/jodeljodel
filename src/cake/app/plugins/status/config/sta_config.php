<?php

/**
 *
 * Copyright 2010-2013, Preface Design LTDA (http://www.preface.com.br)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2010-2013, Preface Design LTDA (http://www.preface.com.br)
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @link          https://github.com/prefacedesign/jodeljodel Jodel Jodel public repository 
 */


// @todo Translate these default values to English.

// to test with default configuration of the behavior
Configure::write('StatusBehavior.options.disponibilidade', array('field' => 'status', 'options' => array('ativo','inativo')));
Configure::write('StatusBehavior.options.etapa', array('options' => array('verde','maduro','podre')));

// to work easily. in the model just use: var $actsAs = array('Status.Status');
Configure::write('StatusBehavior.options.default', array('field' => 'status', 'options' => array('rascunho','publicado'), 'active' => array('publicado')));

// Default
Configure::write('StatusBehavior.options.publishing_status', array('field' => 'publishing_status', 'options' => array('published','draft'), 'active' => array('published', 'draft'), 'default' => 'published'));

?>