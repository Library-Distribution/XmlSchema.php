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

		$can_set = true;
		foreach ($this->getType()->coerce($value) AS $child) {
			if ($child instanceof DOMNode) {
				$node->appendChild($node->ownerDocument->importNode($child, true));
			} else {
				if ($can_set = !$can_set) {
					throw new Exception('Already set the value');
				}
				$node->nodeValue = $child;
			}
		}

		return array($node);
	}

	public function minOccurs() {
		return $this->getAttribute('minOccurs', 1);
	}

	public function maxOccurs() {
		return $this->getAttribute('maxOccurs', 1);
	}
}
?>