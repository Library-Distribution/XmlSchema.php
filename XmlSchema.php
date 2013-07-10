<?php
require_once dirname(__FILE__) . '/nodes/XmlSchemaNode.php';
require_once dirname(__FILE__) . '/nodes/XmlSchemaElement.php';
require_once dirname(__FILE__) . '/nodes/XmlSchemaAttribute.php';

require_once dirname(__FILE__) . '/types/XmlSchemaBuiltinSimpleType.php';
require_once dirname(__FILE__) . '/types/XmlSchemaCustomSimpleType.php';
require_once dirname(__FILE__) . '/types/XmlSchemaComplexType.php';

function starts_with($str, $needle) {
	return substr($str, 0, strlen($needle)) == $needle;
}

class XmlSchema extends XmlSchemaNode {
	private $doc;
	private $xpath;
	private $prefix;
	private $root;

	private $types = array();

	const schemaNamespace = 'http://www.w3.org/2001/XMLSchema';

	public function __construct($source) {
		$this->doc = new DOMDocument();
		$this->doc->loadXML($source);

		$this->xpath = new DOMXPath($this->doc);
		$this->xpath->registerNamespace('xsd', self::schemaNamespace);
		$this->prefix = $this->doc->lookupPrefix(self::schemaNamespace);

		parent::__construct($this, $this->rootElement()->node);
	}

	public function rootElement() {
		if (!$this->root) {
			$elements = $this->doc->getElementsByTagNameNS(self::schemaNamespace, 'element'); # all <xsd:element>
			$trim = strlen($this->prefix . ':element');

			foreach ($elements AS $element) {
				$path = $element->getNodePath();
				if (strpos(substr($path, 0, strlen($path) - $trim), $this->prefix . ':element')) { # the XPath contains an other xsd:element
					continue;
				}

				# ... further filtering may be necessary ...

				$this->root = new XmlSchemaElement($this, $element);
				break;
			}
		}
		return $this->root;
	}

	public function getPrefix($ns = NULL) {
		return $ns === NULL ? $this->prefix : $this->doc->lookupPrefix($ns);
	}

	public function getTargetNamespace() {
		return $this->doc->documentElement->getAttribute('targetNamespace');
	}

	public static function getNamespacePrefix($str) {
		$pos = strpos($str, ':');
		if ($pos !== FALSE) {
			return substr($str, 0, $pos);
		}
		return NULL;
	}

	public function query($path, $context) {
		return $this->xpath->query($path, $context);
	}

	public function coerce($value) {
		return $this->rootElement()->coerce($value);
	}

	##################################################################################################

	public function getElement($name, $context) {
		$node = $this->getNode($name, $context, XmlSchemaNode::NODE_TYPE_ELEMENT);
		if ($node !== NULL) {
			return new XmlSchemaElement($this, $node);
		}
		throw new Exception();
	}

	public function getAttribute($name, $context) {
		$node = $this->getNode($name, $context, XmlSchemaNode::NODE_TYPE_ATTRIBUTE);
		if ($node !== NULL) {
			return new XmlSchemaAttribute($this, $node);
		}
		throw new Exception();
	}

	private function getNode($name, $context, $type) {
		$xpath = './/' . $this->prefix . ':' . $type . '[@name="' . $name . '"]';
		$list = $this->xpath->query($xpath, $context->elem);
		return $list->length > 0 ? $list->item(0) : NULL;
	}

	##################################################################################################

	public function getType($name = NULL) {
		if ($name === NULL) {
			return NULL; # schemas have no own type
		}

		if (isset($this->types[$name])) { # this type has been asked before
			return $this->types[$name];
		}

		if (starts_with($name, $this->prefix . ':')) { # it's a builtin XSD type
			return $this->types[$name] = new XmlSchemaBuiltinSimpleType($name, $this);
		}

		# check for a simple type
		$node = $this->searchTypeDefinition($name, 'simpleType');
		if ($node !== NULL) {
			return $this->types[$name] = new XmlSchemaCustomSimpleType($name, $this, $node);
		}

		# check for a complex type
		$node = $this->searchTypeDefinition($name, 'complexType');
		if ($node !== NULL) {
			return $this->types[$name] = new XmlSchemaComplexType($name, $this, $node);
		}

		$prefix = self::getNamespacePrefix($name);
		if ($prefix !== NULL && $this->getPrefix($this->getTargetNamespace()) == $prefix) {
			return $this->getType(substr($name, strlen($prefix) + 1));
		}

		throw new Exception('Could not find custom type "' . $name . '"');
	}

	private function searchTypeDefinition($name, $type) {
		$xpath = '//' . $this->prefix . ':' . $type . '[@name="' . $name . '"]';
		$nodes = $this->xpath->query($xpath);
		if ($nodes->length > 0) {
			$node = $nodes->item(0);
			return $node;
		}
		return NULL;
	}
}
?>