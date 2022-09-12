<?php

namespace Drupal\simplenews\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\StringTranslation\TranslationWrapper;
use Drupal\node\Entity\NodeType;

/**
 * Provides dynamic link actions for simplenews content types.
 */
class AddSimplenewsIssueActionLinks extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $node_types = simplenews_get_content_types();
    $node_type = reset($node_types);
    if (count($node_types) == 1) {
      $label = NodeType::load($node_type)->label();
      $this->derivatives[$node_type] = $base_plugin_definition;
      $this->derivatives[$node_type]['title'] = new TranslationWrapper('Add @label', array('@label' => $label));
      $this->derivatives[$node_type]['route_parameters'] = array(
        'node_type' => $node_type,
      );
    }
    elseif (count($node_types) > 1) {
      $base_plugin_definition['route_name'] = 'node.add_page';
      $base_plugin_definition['title'] = new TranslationWrapper('Add content');
      $this->derivatives[] = $base_plugin_definition;
    }
    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
