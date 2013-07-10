<?php
require_once dirname(__FILE__) . '/XmlSchemaSimpleType.php';

class XmlSchemaCustomSimpleType extends XmlSchemaSimpleType {
	public function __construct($name, $node) {
		parent::__construct($name, $node);
	}

	public function coerce($value) {
		// pass to base type
		// perform further restrictions/changes
		return array($value);
	}
}
?>