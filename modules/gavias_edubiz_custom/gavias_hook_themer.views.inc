<?php
/**
 * Implements hook_views_data_alter().
 */
function gavias_hook_themer_views_data_alter(array &$data) {
    $data['node_field_data']['related_content_titles'] = array(
        'title' => t('Related Course titles'),
        'filter' => array(
            'title' => t('Related Course titles'),
            'help' => t('Specify a list of titles a Course node can have.'),
            'field' => 'nid',
            'id' => 'gavias_hook_themer_related_content_titles'
        ),
    );
}