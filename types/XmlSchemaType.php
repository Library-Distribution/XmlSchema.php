<?php
require_once dirname(__FILE__) . '/../XmlSchemaObject.php';

abstract class XmlSchemaType extends XmlSchemaObject {
	protected $name;

	public function __construct($name, $schema, $node = NULL) {
		parent::__construct($schema, $node);

		$this->name = $name;
	}

	public function getName() {
		return $this->name;
	}

	public abstract function coerce($value, $doc, $schema_node);
}
?>