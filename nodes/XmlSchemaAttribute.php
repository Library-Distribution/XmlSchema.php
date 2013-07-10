<?php
require_once dirname(__FILE__) . '/XmlSchemaNode.php';

class XmlSchemaAttribute extends XmlSchemaNode {
	public function isRequired() {
		return $this->node->getAttribute('use') == 'required';
	}

	public function coerce($value) {
		$node = self::$dummy_doc->createAttribute($this->name);

		$values = $this->getType()->coerce($value);
		$node->nodeValue = $values[0];

		return array($node);
	}
}
?>