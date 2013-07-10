<?php
require_once dirname(__FILE__) . '/XmlSchemaNode.php';

class XmlSchemaElement extends XmlSchemaNode {
	public function coerce($value) {
		$node = self::$dummy_doc->createElement($this->name);

		$children = $this->getType()->coerce($value);
		foreach ($children AS $child) {
			$node->appendChild($node->ownerDocument->importNode($child, true));
		}

		return array($node);
	}
}
?>