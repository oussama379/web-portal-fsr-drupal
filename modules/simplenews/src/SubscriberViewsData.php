<?php

namespace Drupal\simplenews;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for the subscriber entity type.
 */
class SubscriberViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['simplenews_subscriber']['edit_link'] = array(
      'field' => array(
        'title' => $this->t('Link to edit'),
        'help' => $this->t('Provide a simple link to edit the subscriber.'),
        'id' => 'subscriber_link_edit',
      ),
    );

    $data['simplenews_subscriber']['delete_link'] = array(
      'field' => array(
        'title' => $this->t('Link to delete'),
        'help' => $this->t('Provide a simple link to delete the subscriber.'),
        'id' => 'subscriber_link_delete',
      ),
    );
    // @todo Username obtained through custom plugin due to core issue.
    $data['simplenews_subscriber']['user_name'] = array(
      'real field' => 'uid',
      'field' => array(
        'title' => $this->t('Username'),
        'help' => $this->t('Provide a simple link to the subscriber\'s user account .'),
        'id' => 'simplenews_user_name',
      ),
    );
    return $data;
  }
}
