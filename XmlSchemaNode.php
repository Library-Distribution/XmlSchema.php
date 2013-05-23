<?php
require_once dirname(__FILE__) . '/types/XmlSchemaType.php';

abstract class XmlSchemaNode {
	protected $schema;
	protected $elem;
	protected $name;
	protected $type = NULL;

	public function __construct($schema, $element) {
		$this->elem = $element;
		$this->schema = $schema;

		$this->name = $element->getAttribute('name');
	}

	public function getName() {
		return $this->name;
	}

	public function getType() {
		if ($this->type !== NULL) {
			return $this->type;
		}

		if ($this->elem->hasAttribute('type')) {
			$type_name = $this->elem->getAttribute('type');
			return $this->type = $this->schema->getType($type_name);
		}

		$nodes = $this->schema->xpath->query('simpleType', $this->elem);
		if ($nodes->length > 0) {
			$node = $nodes->item(0);
			return $this->type = new XmlSchemaSimpleType('<# anonymous type #>', $node);
		}

		$nodes = $this->schema->xpath->query('complexType', $this->elem);
		if ($nodes->length > 0) {
			$node = $nodes->item(0);
			return $this->type = new XmlSchemaComplexType('<# anonymous type #>', $node);
		}

		throw new Exception('No type found for this node');
	}
}
?>