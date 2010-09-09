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
	protected $document = null;

/**
 * Constructor
 * 
 * @param string $html
 * @return void
 */
	public function __construct($html) {
		libxml_use_internal_errors(true);
		$this->document = new DomDocument();
		$this->document->preserveWhiteSpace = false;
		$this->document->loadHTML($html);
		libxml_use_internal_errors(false);
	}

/**
 * Returns an array of tanslate-friendly tokens extracted from a HTML document
 * 
 * @param int $length maximum size for each token extracted from the HTML document
 * @return array list of tokens
 */
	public function tokens($length = 5000) {
		$body = $this->document->getElementsByTagName('body')->item(0);
		$contents = $this->getTokenContents($body);

		if (strlen($contents) <= $length) {
			return array($contents);
		}

		$tokens = array();
		$biggestToken = '';

		foreach ($body->childNodes as $child) {
			
			$childContent = $this->getTokenContents($child);
			$childContentLenght = strlen($childContent);

			if (!trim($childContent)) {
				continue;
			}

			if ($child->tagName == 'code') {
				$tokens[] = str_replace('??>', '?>', $childContent);
				continue;
			}

			if (strlen($biggestToken) + $childContentLenght <= $length) {
				$biggestToken .= $childContent;
				continue;
			} else if ($childContentLenght > $length && $child->hasChildNodes()) {
				$tokens[] = $biggestToken;
				$biggestToken = '';
				$tokens = $this->tokenizeChild($child, $tokens, $length);
			} else {
				$tokens[] = $biggestToken;
				$biggestToken = $childContent;
			}
		}
		return $this->postProcess($tokens); 
	}

/**
 * Auxiliary function to extract tokens from an child node
 * 
 * @param DomNode $child the child node to extract tokens from
 * @param array tokes array of tokes previously extracted
 * @param int $length maximum size for each token extracted from the HTML document
 * @return array list of tokens
 */
	protected function tokenizeChild($child, $tokens, $length) {
		$attributes = $this->getNodeAttributes($child);
		if ($attributes) {
			$attributes = ' ' . $attributes;
		}
		$tokens[] = "<{$child->tagName}{$attributes}>";
		foreach ($child->childNodes as $c) {
			$cContent = $this->getTokenContents($c);
			if (!$c->hasChildNodes()) {
				if (!trim($cContent)) {
					continue;
				}
				$tokens[] = $cContent;
				continue;
			}
			$tokenizer = new HtmlTokenizer($cContent);
			$newTokens = $tokenizer->tokens($this->getTokenContents($c));
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
	protected function getTokenContents($node) {
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
	protected function getNodeAttributes($node) {
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
	protected function postProcess($tokens) {
		foreach ($tokens as &$token) {
			$token = str_replace('<br></br>', '<br />', $token);
			$token = str_replace('</img>', '', $token);
		}
		return $tokens;
	}
}