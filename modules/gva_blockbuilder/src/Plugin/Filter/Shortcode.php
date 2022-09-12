<?php
/**
 * @file
 * Contains \Drupal\insert_view\Plugin\Filter\Shortcode.
 */

namespace Drupal\gavias_blockbuilder\Plugin\Filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Url;
use Drupal\gavias_blockbuilder\Plugin\ShortcodeInterface;

/**
 * Provides a filter for insert view.
 *
 * @Filter(
 *   id = "shortcode",
 *   module = "gavias_blockbuilder",
 *   title = @Translation("Shortcodes"),
 *   description = @Translation("Provides gavias shortcodes to text formats."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class Shortcode extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    if (!empty($text)) {
      $text = do_shortcode( $text );
    }
    return new FilterProcessResult($text);
  }
  
  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $output = '<div>You can use shortcode for block builder module. You can visit admin/structure/gavias_blockbuilder and get shortcode, sample [gbb name="page_home_1"].<div>';
    return $output;
  }

}

