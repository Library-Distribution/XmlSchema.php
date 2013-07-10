<?php
require_once dirname(__FILE__) . '/XmlSchemaType.php';

require_once dirname(__FILE__) . '/../compositors/XmlSchemaAll.php';
require_once dirname(__FILE__) . '/../compositors/XmlSchemaChoice.php';
require_once dirname(__FILE__) . '/../compositors/XmlSchemaSequence.php';

class XmlSchemaComplexType extends XmlSchemaType {

	const CT_NONE = 0;
	const CT_GROUP = 1;

	const CT_ALL = 2;
	const CT_CHOICE = 3;
	const CT_SEQUENCE = 4;

	const CT_SIMPLECONTENT = 5;
	const CT_COMPLEXCONTENT = 6;

	protected $content = NULL;
	protected $attributes = NULL;

	public function __construct($name, $schema, $node) {
		parent::__construct($name, $schema, $node);

		$content_node = $this->getContentNode();
		$content_type = $this->getContentType();

		if ($content_type != self::CT_SIMPLECONTENT && $content_type != self::CT_COMPLEXCONTENT) {
			$this->parseAttributes();
		}

		switch ($content_type) {
			case self::CT_ALL:      $this->content = new XmlSchemaAll($schema, $content_node);
			case self::CT_CHOICE:   $this->content = new XmlSchemaChoice($schema, $content_node);
			case self::CT_SEQUENCE: $this->content = new XmlSchemaSequence($schema, $content_node);
		}
	}

	public function coerce($value) {
		$nodes = array();
		$content_type = $this->getContentType();

		if ($content_type != self::CT_SIMPLECONTENT && $content_type != self::CT_COMPLEXCONTENT) {
			foreach ($this->attributes AS $attribute) {
				$attr_name = $attribute->getName();

				if (is_array($value) && isset($value[$attr_name])) {
					$nodes[] = $attribute->coerce($value[$attr_name]);
				} else if ($attribute->isRequired()) {
					throw new Exception('Required attribute is missing');
				}
			}
		}

		if ($content_type !== self::CT_NONE) {
			$nodes = array_merge($nodes, $this->content->coerce($value));
		}

		return $nodes;
	}

	private function parseAttributes() {
		$this->attributes = array();
		foreach ($this->schema->query('./xsd:attribute', $this->node) AS $attr_node) {
			$this->attributes[] = new XmlSchemaAttribute($this->schema, $attr_node);
		}
	}

	private $content_node = NULL;

	private function getContentNode() {
		if ($this->content_node === NULL) {
			$list = $this->schema->query('xsd:group|xsd:sequence|xsd:all|xsd:choice|xsd:simpleContent|xsd:complexContent', $this->node);
			if ($list->length == 0) {
				return NULL;
			}
			$this->content_node = $list->item(0);
		}
		return $this->content_node;
	}

	private function getContentType() {
		$node = $this->getContentNode();
		if ($node === NULL) {
			return self::CT_NONE;
		}

		switch ($node->tagName) {
			case 'xsd:group':          return self::CT_GROUP;
			case 'xsd:all':            return self::CT_ALL;
			case 'xsd:choice':         return self::CT_CHOICE;
			case 'xsd:sequence':       return self::CT_SEQUENCE;
			case 'xsd:simpleContent':  return self::CT_SIMPLECONTENT;
			case 'xsd:complexContent': return self::CT_COMPLEXCONTENT;
		}
		throw new Exception('Unknown content type');
	}
}
?>