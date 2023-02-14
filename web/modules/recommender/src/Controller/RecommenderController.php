<?php
namespace Drupal\recommender\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Provides route responses for the Example module.
 */
class RecommenderController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function testPage() {
    return [
      '#markup' => 'Hello, world',
    ];
  }

}