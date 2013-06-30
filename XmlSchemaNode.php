<?php
require_once dirname(__FILE__) . '/XmlSchemaObject.php';
require_once dirname(__FILE__) . '/types/XmlSchemaCustomSimpleType.php';
require_once dirname(__FILE__) . '/types/XmlSchemaComplexType.php';

abstract class XmlSchemaNode extends XmlSchemaObject {
	protected $name;
	protected $type = NULL;

	const NODE_TYPE_ATTRIBUTE = 'attribute';
	const NODE_TYPE_ELEMENT = 'element';

	public function __construct($schema, $node) {
		parent::__construct($schema, $node);

		$this->name = $node->getAttribute('name');
	}

	public function getName() {
		return $this->name;
	}

	public function getSchema() {
		return $this->schema;
	}

	public function getType() {
		if ($this->type !== NULL) {
			return $this->type;
		}

		if ($this->node->hasAttribute('type')) {
			$type_name = $this->node->getAttribute('type');
			return $this->type = $this->schema->getType($type_name);
		}

		$nodes = $this->schema->query('xsd:simpleType', $this->node);
		if ($nodes->length > 0) {
			return $this->type = new XmlSchemaSimpleType('<# anonymous type #>', $this->schema, $nodes->item(0));
		}

		$nodes = $this->schema->query('xsd:complexType', $this->node);
		if ($nodes->length > 0) {
			return $this->type = new XmlSchemaComplexType('<# anonymous type #>', $this->schema, $nodes->item(0));
		}

		throw new Exception('No type found for this node');
	}
}
?>