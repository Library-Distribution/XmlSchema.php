<?php
require_once dirname(__FILE__) . '/types/XmlSchemaCustomSimpleType.php';
require_once dirname(__FILE__) . '/types/XmlSchemaComplexType.php';

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

		$nodes = $this->schema->query('xsd:simpleType', $this->elem);
		if ($nodes->length > 0) {
			return $this->type = new XmlSchemaSimpleType('<# anonymous type #>', $nodes->item(0));
		}

		$nodes = $this->schema->query('xsd:complexType', $this->elem);
		if ($nodes->length > 0) {
			return $this->type = new XmlSchemaComplexType('<# anonymous type #>', $nodes->item(0));
		}

		throw new Exception('No type found for this node');
	}
}
?>