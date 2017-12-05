<?php

namespace Drupal\example_batch;

use Drupal\Component\Uuid\Php;

class AnotherBatchOperation {

  /**
   * @param $numberO
   * @param $context
   */
  public static function generateSomeStrings($number, &$context) {
    $message = \Drupal::translation()->formatPlural(
      $number,
      'One random string is listed below:', '@count random strings are listed below:'
    );
    drupal_set_message($message);

    $context = AnotherBatchOperation::initializeSandbox($number, $context);
    $max = AnotherBatchOperation::batchLimit($context);

    // Start where we left off last time.
    $start = $context['sandbox']['progress'];
    for ($i = $start; $i < $max; $i++) {
      $str = AnotherBatchOperation::randomString();

      $context['results'][] = $str;

      // We want to display the counter 1-based, not 0-based.
      $counter = $i + 1;
      drupal_set_message($counter . '. ' . $str);

      // Update our progress!
      $context['sandbox']['progress']++;
    }

    $context = self::contextProgress($context);
  }

  /**
   * @param $number
   * @param $context
   */
  public static function reverseThoseStrings($number, &$context) {
    $message = \Drupal::translation()->formatPlural(
      $number,
      'The prior random string is listed below, reversed:', 'The prior @count random strings are listed below, reversed:'
    );
    drupal_set_message($message);

    $context = self::initializeSandbox($number, $context);
    $max = self::batchLimit($context);

    // Start where we left off last time.
    $start = $context['sandbox']['progress'];
    for ($i = $start; $i < $max; $i++) {
      $str = strrev($context['results'][$i]);

      // We want to display the counter 1-based, not 0-based.
      $counter = $i + 1;
      drupal_set_message($counter . '. ' . $str);

      // Update our progress!
      $context['sandbox']['progress']++;
    }

    $context = self::contextProgress($context);
  }

  /**
   * @param $context
   *
   * @return mixed
   */
  protected static function contextProgress(&$context) {
    if ($context['sandbox']['progress'] != $context['sandbox']['max']) {
      $context['finished'] = $context['sandbox']['progress'] / $context['sandbox']['max'];
    }
    return $context;
  }

  public static function finishUpMyBatch($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()
        ->formatPlural(count($results), 'One string generated and reversed.', '@count strings generated and reversed.');
    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);
  }

  /**
   * @param $number
   * @param $context
   *
   * @return mixed
   */
  protected static function initializeSandbox($number, &$context) {
    if (empty($context['sandbox'])) {
      $context['sandbox']['progress'] = 0;
      $context['sandbox']['max'] = $number;
      $context['sandbox']['working_set'] = [];
    }
    return $context;
  }

  /**
   * @param $context
   */
  protected static function batchLimit(&$context) {
    // Process the next 100 if there are at least 100 left. Otherwise,
    // we process the remaining number.
    $batchSize = 100;

    $max = $context['sandbox']['progress'] + $batchSize;
    if ($max > $context['sandbox']['max']) {
      $max = $context['sandbox']['max'];
    }
    return $max;
  }

  /**
   * @return string
   */
  protected static function randomString() {
    $uuid = new Php();
    $code = $uuid->generate();
    $code = strtoupper($code);
    return $code;
  }
}

