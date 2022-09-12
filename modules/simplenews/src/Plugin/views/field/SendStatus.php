<?php

namespace Drupal\simplenews\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to provide send status of a newsletter issue.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("simplenews_send_status")
 */
class SendStatus extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $values->_entity;
    if ($node->hasField('simplenews_issue')) {
      // Get elements to render.
      $message = $this->getMessage($node);
      if (!empty($message['uri'])) {
        $output['image'] = array(
          '#theme' => 'image',
          '#uri' => $message['uri'],
          '#alt' => $message['description'],
          '#title' => $message['description'],
          '#getsize' => TRUE,
        );
      }
      $output['text'] = array(
        '#type' => 'inline_template',
        '#template' => '<span title="{{ description }}">{{ count }}</span>',
        '#context' => $message,
      );
      return $output;
    }
  }

  /**
   * Return a compiled message to display.
   *
   * @param $node
   *   The node object.
   *
   * @return array
   *   An array containing the elements of the message to be rendered.
   */
  protected function getMessage($node) {
    $status = $node->simplenews_issue->status;
    $sent_count = (int) $node->simplenews_issue->sent_count;
    $published = $node->isPublished();
    $subscriber_count = $node->simplenews_issue->status == SIMPLENEWS_STATUS_SEND_READY ? $node->simplenews_issue->subscribers : simplenews_count_subscriptions($node->simplenews_issue->target_id);
    $message = array();
    $message['count'] = $subscriber_count;
    $message['uri'] = NULL;
    $images = array(
      SIMPLENEWS_STATUS_SEND_PENDING => 'images/sn-cron.png',
      SIMPLENEWS_STATUS_SEND_READY => 'images/sn-sent.png',
    );
    if ($status == SIMPLENEWS_STATUS_SEND_READY) {
      $message['description'] = t('Newsletter issue sent to @sent_count subscribers.', ['@sent_count' => $sent_count]);
      $message['uri'] = drupal_get_path('module', 'simplenews') . '/' . $images[$status];
    }
    elseif ($status == SIMPLENEWS_STATUS_SEND_PENDING) {
      $message['description'] = t('Newsletter issue is pending, @sent_count mails sent out of @count.', array(
        '@sent_count' => $sent_count,
        '@count' => $subscriber_count
      ));
      $message['uri'] = drupal_get_path('module', 'simplenews') . '/' . $images[$status];
    }
    elseif (($status == SIMPLENEWS_STATUS_SEND_NOT)) {
      $message['description'] = t('Newsletter issue will be sent to @count subscribers.', array('@count' => $subscriber_count));
    }
    if (!$published) {
      $message['description'] = t('Newsletter issue will be sent to @count subscribers on publish.', array('@count' => $subscriber_count));
    }
    return $message;
  }
}
