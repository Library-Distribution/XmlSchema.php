<?php
require_once dirname(__FILE__) . '/XmlSchemaNode.php';

class XmlSchemaAttribute extends XmlSchemaNode {
	public function isRequired() {
		return $this->node->getAttribute('use') == 'required';
	}

	public function coerce($value) {
		$node = self::$dummy_doc->createAttribute($this->name);
		$node->nodeValue = $this->getType()->coerce($value)[0];
		return array($node);
	}
}
?>