<?php
abstract class XmlSchemaType {
	protected $name;
	protected $node;

	public function __construct($name, $node = NULL) {
		$this->name = $name;
		$this->node = $node;
	}

	public function getName() {
		return $this->name;
	}

	public abstract function coerce($value, $doc, $schema_node);
}
?>