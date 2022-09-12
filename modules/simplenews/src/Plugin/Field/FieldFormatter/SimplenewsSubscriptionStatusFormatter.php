<?php

namespace Drupal\simplenews\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;

/**
 * Formatter that displays a newsletter subscription with the status.
 *
 * @FieldFormatter(
 *   id = "simplenews_subscription_status",
 *   label = @Translation("Subscriptions Status"),
 *   field_types = {
 *     "simplenews_subscription"
 *   }
 * )
 */
class SimplenewsSubscriptionStatusFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $label = $entity->label();

      // Do not explicitly display the status for confirmed subscriptions.
      $output = $label;

      // Add status label for the unconfirmed subscriptions.
      if ($items[$delta]->status == SIMPLENEWS_SUBSCRIPTION_STATUS_UNCONFIRMED) {
        $output = $this->t('@label (Unconfirmed)', array('@label' => $label));
      }

      // Add status label for the unsubscribed subscriptions.
      if ($items[$delta]->status == SIMPLENEWS_SUBSCRIPTION_STATUS_UNSUBSCRIBED) {
        $output = $this->t('@label (Unsubscribed)', array('@label' => $label));
      }

      // Add the label.
      $elements[$delta]['#markup'] = $output;
    }

    return $elements;
  }

}
