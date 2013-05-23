<?php
require_once dirname(__FILE__) . '/XmlSchemaType.php';

class XmlSchemaComplexType extends XmlSchemaType {
	public function __construct($name, $node) {
		parent::__construct($name, $node);
	}

	public function coerce($value, $doc, $schema_node) {
		throw new Exception('Cannot coerce for complex type');
	}
}
?>