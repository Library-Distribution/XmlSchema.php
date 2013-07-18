<?php
require_once dirname(__FILE__) . '/XmlSchemaNode.php';

class XmlSchemaElement extends XmlSchemaNode {
	protected $default_root = false;

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

	protected function extractMeta() {
		parent::extractMeta();

		foreach ($this->get_meta_nodes() AS $node) {
			switch ($node->localName) {
				case 'default-root':
					$this->default_root = true;
					break;
			}
		}
	}

	public function isDefaultRoot() {
		return $this->default_root;
	}
}
?>