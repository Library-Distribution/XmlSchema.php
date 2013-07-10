<?php
require_once dirname(__FILE__) . '/XmlSchemaNode.php';

class XmlSchemaElement extends XmlSchemaNode {
	public function coerce($value) {
		if ($this->schema->elementsRequirePrefix()) {
			$namespace = $this->schema->getTargetNamespace();
			$node = self::$dummy_doc->createElementNS($namespace, $this->schema->getPrefix($namespace) . ':' . $this->name);
		} else {
			$node = self::$dummy_doc->createElement($this->name);
		}

		$children = $this->getType()->coerce($value);
		foreach ($children AS $child) {
			$node->appendChild($node->ownerDocument->importNode($child, true));
		}

		return array($node);
	}
}
?>