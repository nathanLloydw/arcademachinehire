<?php
class Redux_Validation_preg_replace extends Redux_Options{	
	
	/**
	 * Field Constructor.
	 *
	 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
	 *
	 * @since Redux_Options 1.0.0
	*/
	function __construct($field, $value, $current) {
		parent::__construct();
		$this->field = $field;
		$this->value = $value;
		$this->current = $current;
		$this->validate();
	}

	/**
	 * Field Render Function.
	 *
	 * Takes the vars and validates them
	 *
	 * @since Redux_Options 1.0.0
	*/
	function validate() {
		$this->value = preg_replace_callback($this->field['preg']['pattern'], function($matches){return $this->field['preg']['replacement'];}, $this->value);
	}
}
