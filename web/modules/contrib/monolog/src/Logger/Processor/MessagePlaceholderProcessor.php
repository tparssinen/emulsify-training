<?php

namespace Drupal\monolog\Logger\Processor;

use Drupal\Core\Logger\LogMessageParserInterface;

/**
 * Parse and replace message placeholders.
 */
class MessagePlaceholderProcessor {

  /**
   * The message's placeholders parser.
   *
   * @var \Drupal\Core\Logger\LogMessageParserInterface
   */
  protected $parser;

  /**
   * Construct default object.
   * 
   * @param \Drupal\Core\Logger\LogMessageParserInterface $parser
   *   The parser to use when extracting message variables.
   */
  public function __construct(LogMessageParserInterface $parser) {
    $this->parser = $parser;
  }

  /**
   * {@inheritdoc}
   */
  public function __invoke(array $record) {
    // Populate the message placeholders and then replace them in the message.
    $message_placeholders = $this->parser->parseMessagePlaceholders($record['message'], $record['context']);
    $record['message'] = empty($message_placeholders) ? $record['message'] : strtr($record['message'], $message_placeholders);

    // Remove the replaced placeholders from the context to prevent logging the
    // same information twice.
    foreach (array_keys($message_placeholders) as $placeholder) {
      unset($record['context'][$placeholder]);
    }

    return $record;
  }

}
