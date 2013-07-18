<?php
require_once dirname(__FILE__) . '/../XmlSchemaParticle.php';
require_once dirname(__FILE__) . '/../types/XmlSchemaCustomSimpleType.php';
require_once dirname(__FILE__) . '/../types/XmlSchemaComplexType.php';

abstract class XmlSchemaNode extends XmlSchemaParticle {
	protected $name, $source_name;
	protected $type = NULL;

	const NODE_TYPE_ATTRIBUTE = 'attribute';
	const NODE_TYPE_ELEMENT = 'element';

	public function __construct($schema, $node) {
		parent::__construct($schema, $node);

		$this->name = $node->getAttribute('name');
		$this->extractMeta();
	}

	protected function extractMeta() {
		# defaults:
		$this->source_name = $this->name;

		foreach ($this->get_meta_nodes() AS $node) {
			switch ($node->localName) {
				case 'source-name':
					$this->source_name = $node->nodeValue;
					break;
			}
		}
	}

	protected function get_meta_nodes() {
		return $this->schema->query('xsd:annotation/xsd:appinfo/transform:*', $this->node);
	}

	public function getName() {
		return $this->name;
	}

	public function getSourceName() {
		return $this->source_name;
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