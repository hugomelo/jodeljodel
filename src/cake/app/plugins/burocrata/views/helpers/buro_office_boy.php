<?php

/**
 * BuroOfficeBoy helper.
 *
 * Creates all javascript necessary for BuroBurocrataHelper work.
 *
 * @package       jodel
 * @subpackage    jodel.burocrata.view.helpers
 */
class BuroOfficeBoyHelper extends AppHelper
{
/**
 * Other helpers used by OfficeBoy
 * 
 * @var array
 * @access public
 */
	public $helpers = array('Html', 'Js' => 'prototype');


/**
 * Callbacks tamplate
 *
 * @var array
 * @access protected
 */
	protected $callbacks = array(
		'form' => array(
			'onStart' => 'function(form){%s}',
			'onSave' => 'function(form, response, json, saved){%s}',
			'onReject' => 'function(form, response, json, saved){%s}',
			'onSuccess' => 'function(form, response, json){%s}',
			'onComplete' => 'function(form, response){%s}',
			'onFailure' => 'function(form, response){%s}',
			'onError' => 'function(code, error){%s}'
		),
		'autocomplete' => array(
			'onStart' => 'function(input){%s}',
			'onSelect' => 'function(input, pair, element){%s}',
			'onSuccess' => 'function(input, response, json){%s}',
			'onComplete' => 'function(input, response){%s}',
			'onFailure' => 'function(input, response){%s}',
			'onError' => 'function(code, error){%s}'
		)
	);


/**
 * Creates the javascript counter-part of one autocomplete input.
 *
 * @access public
 * @param string $form_id The form dom ID
 * @param array $options An array that must contains some attributes that defines the current form
 * @return boolean True if the javascript was sucefully generated, false, otherwise
 */
	public function autoComplete($options = array())
	{
		$this->_includeScripts();
		
		$options = am(array('callbacks' => array()), $options);
		extract($options);
		
		unset($options['url']);
		unset($options['id_base']);
		unset($options['callbacks']);
		
		$options = $this->Js->object($options);
		$script = sprintf("new BuroAutocomplete('%s','%s', %s)", $this->url($url), $id_base, $options);
		
		if(!empty($callbacks) && is_array($callbacks))
			$script .= $this->formatCallbacks('autocomplete', $callbacks);
		
		$this->Js->buffer($script);
		$this->Js->writeBuffer(array('inline' => false));
	}


/**
 * Creates the javascript counter-part of one form.
 *
 * @access public
 * @param string $form_id The form dom ID
 * @param array $options An array that must contains some attributes that defines the current form
 * @return boolean True if the javascript was sucefully generated, false, otherwise
 */
	public function newForm($form_id, $options)
	{
		$this->_includeScripts();
		
		$options = am(array('callbacks' => array()), $options);
		extract($options);
		
		$script = sprintf("new BuroForm('%s','%s','%s')",$this->url($url), $form_id, $submit);
		
		if(!empty($callbacks) && is_array($callbacks))
			$script .= $this->formatCallbacks('form', $callbacks);
		
		$this->Js->buffer($script);
		$this->Js->writeBuffer(array('inline' => false));
	}


/**
 * Handles the array of callbacks and converts it to javascript
 *
 * @access protected
 * @param array $callbacks One associative array that contains all configurable callbacks for the form
 * @return string An javascript object that contains all registred callbacks
 */	
	protected function formatCallbacks($type, $callbacks)
	{
		if(!is_array($callbacks))
			return null;
		
		$out = array();
		foreach($callbacks as $callback => $script)
		{
			if(!isset($this->callbacks[$type][$callback]))
				continue;
			
			if(is_string($script))
				$script = array($script => null);
			
			$js = sprintf($this->callbacks[$type][$callback], $this->_parseScript($script));
			$out[] = $callback . ':' . $js;
		}
		
		return '.addCallbacks({' . implode(', ', $out) . '})';
	}


/**
 * Converts an array to javascript using the jodel convention
 *
 * @access protected
 * @param mixed $script
 * @return string The adequate function script for context
 */
	protected function _parseScript($script)
	{
		if(!is_array($script)) return null;
		
		$js = '';
		foreach($script as $type => $code)
		{
			if(is_numeric($type))
				$type = $code;
			$js .= $this->{'_'.$type}($code) . ' ';
		}
		
		return $js;
	}


/**
 * Generates the script that locks the content of form
 *
 * @access protected
 * @param mixed $script
 * @return string The formated script
 */
	protected function _lockForm($script)
	{
		return 'form.lock();';
	}


/**
 * Generates the script that locks the content of form
 *
 * @access protected
 * @param mixed $script
 * @return string The formated script
 */
	protected function _unlockForm($script)
	{
		return 'form.unlock();';
	}


/**
 * Generates the script that updates the content of form, based on json response
 *
 * @access protected
 * @param mixed $script
 * @return string The formated script
 */
	protected function _contentUpdate($script)
	{
		return 'form.update(json.content);';
	}


/**
 * Just escapes a string to be JSON friendly.
 *
 * @access protected
 * @param mixed $script 
 * @return string The formated script
 */
	protected function _js($script)
	{
		return $this->Js->escape($script);
	}


/**
 * Formats a redirect script
 *
 * @access protected
 * @param mixed $url
 * @return string The formated script
 */
	protected function _redirect($url)
	{
		return $this->Js->redirect($url);
	}


/**
 *
 * @access protected
 * @param mixed $msg
 * @return string The formated script
 */
	protected function _popup($msg)
	{
		return $this->Js->alert((string) $msg);
	}


/**
 * Includes the necessary script files to burocrata works
 *
 * @access protected
 */
	protected function _includeScripts()
	{
		$this->Html->script('prototype', array('inline' => false));
		$this->Html->script('effects', array('inline' => false));
		$this->Html->script('controls', array('inline' => false));
		$this->Html->script('/burocrata/js/burocrata.js', array('inline' => false));
	}
}