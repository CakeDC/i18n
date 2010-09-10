<?php
/**
 * Copyright 2010, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * HtmlTokenizer Library
 *
 *
 * @package i18n
 * @subpackage i18n.libs
 */
class HtmlTokenizer {

/**
 * Reference to the DomDocument object to tokenize 
 * 
 * @var DomDocument
 */
	protected $_document = null;

/**
 * Constructor
 * 
 * @param string $html
 * @return void
 */
	public function __construct($html) {
		libxml_use_internal_errors(true);
		$this->_document = new DomDocument();
		$this->_document->preserveWhiteSpace = false;
		$this->_document->loadHTML($html);
		libxml_use_internal_errors(false);
	}

/**
 * Returns an array of translate-friendly tokens extracted from a HTML document
 * The code will be splitten in tokens no longer than the passed lenght, and are valid HTML
 * No tag will be splitted across two tokens.
 * 
 * @param int $length maximum size for each token extracted from the HTML document
 * @param DOMElement $body DomElement to tokenize, by default the current $this->_document in used
 * @return array list of tokens
 */
	public function tokens($length = 5000, $body = null) {
		if (!($body instanceof DOMElement)) {
			$body = $this->_document->getElementsByTagName('body')->item(0);
		}
		$contents = $this->_getTokenContents($body);

		if (strlen($contents) <= $length) {
			return array($contents);
		}

		$tokens = array();
		$biggestToken = '';

		foreach ($body->childNodes as $child) {
			
			$childContent = $this->_getTokenContents($child);
			$childContentLenght = strlen($childContent);

			if (trim($childContent) == '') {
				continue;
			}

			if (isset($child->tagName) && $child->tagName == 'code') {
				$childContent = str_replace('??>', '?>', $childContent);
			}

			if (strlen($biggestToken) + $childContentLenght <= $length) {
				$biggestToken .= $childContent;
			} else if ($childContentLenght > $length && $child->hasChildNodes()) {
				if (trim($biggestToken) != '') {
					$tokens[] = $biggestToken;
				}
				$biggestToken = '';
				$tokens = $this->_tokenizeChild($child, $tokens, $length);
			} else {
				if (trim($biggestToken) != '') {
					$tokens[] = $biggestToken;
				}
				$biggestToken = $childContent;
			}
		}
		
		if (trim($biggestToken) != '') { 
			$tokens[] = $biggestToken;
		}
		
		return $this->_postProcess($tokens); 
	}

/**
 * Auxiliary function to extract tokens from an child node
 * 
 * @param DomNode $child the child node to extract tokens from
 * @param array tokes array of tokes previously extracted
 * @param int $length maximum size for each token extracted from the HTML document
 * @return array list of tokens
 */
	protected function _tokenizeChild($child, $tokens, $length) {
		$attributes = $this->_getNodeAttributes($child);
		if ($attributes) {
			$attributes = ' ' . $attributes;
		}
		$tokens[] = "<{$child->tagName}{$attributes}>";
		foreach ($child->childNodes as $c) {
			$cContent = $this->_getTokenContents($c);
			if (!$c->hasChildNodes()) {
				if (!trim($cContent)) {
					continue;
				}
				if (isset($child->tagName) && $child->tagName == 'code') {
					$cContent = str_replace('??>', '?>', $cContent);
				}
				$tokens[] = $cContent;
				continue;
			}
			$tokenizer = new HtmlTokenizer($cContent);
			$newTokens = $tokenizer->tokens($length, $c);
			if (array_sum(array_map('strlen', $newTokens)) <= $length) {
				$tokens[] = join('', $newTokens);
			} else {
				$tokens = array_merge($tokens, $newTokens);
			}
		}
		$tokens[] = "</{$child->tagName}>";
		return $tokens;
	}

/**
 * Auxiliary function to extract DomNode raw text
 * 
 * @param DomNode $node
 * @return string raw text extracted from $node
 */
	protected function _getTokenContents($node) {
		if ($node instanceof DomText) {
			return $node->textContent;
		}
		return $node->C14N();
	}

/**
 * Returns a string with all attributes from a node
 * 
 * @param DomNode $node
 * @return string serialized list of $node attributes
 */
	protected function _getNodeAttributes($node) {
		$result = array();
		foreach ($node->attributes as $attribute) {
			$result[] = "{$attribute->nodeName}=\"{$attribute->nodeValue}\"";
		}
		return join(' ', $result);
	}

/**
 * Processes the list of extracted tokens to workaround some behaviors of DomDocument parser
 * 
 * @param array $tokens list of tokens to process and clean html
 * @return array of processed tokens
 */
	protected function _postProcess($tokens) {
		foreach ($tokens as &$token) {
			$token = str_replace('<br></br>', '<br />', $token);
			$token = str_replace('</img>', '', $token);
		}
		return $tokens;
	}
}