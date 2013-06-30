<?php
abstract class XmlSchemaObject {
	protected $schema;
	protected $node;

	public function __construct($schema, $node) {
		$this->schema = $schema;
		$this->node = $node;
	}
}
?>