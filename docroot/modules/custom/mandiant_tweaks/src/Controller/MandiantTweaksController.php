<?php
/**
 * @file
 * Contains \Drupal\mandiant_tweaks\Controller\ExampleModuleController.
 */

namespace Drupal\mandiant_tweaks\Controller;
use Drupal\node\Entity\NodeType;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

/**
 * The MandiantTweaksController controller.
 */
class MandiantTweaksController extends ControllerBase {

  public function _outputDd($url, $title) {
    return '
      <dl class="admin-list--panel admin-list">
        <div class="admin-item">
          <a href="'.$url->toString().'" title="'.t($title).'" class="admin-item__link">'.t($title).'</a>
          <dt class="admin-item__title">'.t($title).'</dt>
        </div>
      </dl>
    ';
  }

  /**
   * {@inheritdoc}
   */
  public function nodeAdd() {
    $types = NodeType::loadMultiple();

    // Link groups wrapper.
    $links = [
      'page' => ["title" => "", "items" => ""],
      'article' => ["title" => "", "items" => ""],
      'partner' => ["title" => "", "items" => ""],
      'other' => ["title" => "", "items" => ""],
    ];

    // Get each node type forms to build a dynamic list of layout types.
    foreach (array_keys($types) as $allowed_type) {
      $node_type = $types[$allowed_type]->label();

      $node = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->create(['type' => $allowed_type]);
      $form = \Drupal::entityTypeManager()
        ->getFormObject('node', 'default')
        ->setEntity($node);
      $form_obj = \Drupal::formBuilder()->getForm($form);

      if (!empty($form_obj['layout_selection'])) {
        $links[$allowed_type]['title'] = $this->t($node_type);
        foreach ($form_obj['layout_selection']['widget']['#options'] as $k => $v) {
          if ($form_obj['layout_selection']['widget']['#required'] === FALSE || $k !== '_none') {
            $label = $k === '_none' ? "Basic" : $v;
            $url = Url::fromRoute('node.add', ['node_type' => $allowed_type,'edit' => ['layout_selection' => ['widget' => $k]]]);
            $links[$allowed_type]['items'] .= $this->_outputDd($url, $label);
          }
        }
      }
      else {
        $links['other']['title'] = $this->t('Other');
        $url = Url::fromRoute('node.add', ['node_type' => $allowed_type]);
        $links['other']['items'] .= $this->_outputDd($url, $node_type);
      }
    }

    $build = array(
      '#title' => $this->t('Add Content'),
      '#type' => 'markup',
      '#markup' => '
        <div class="layout-row clearfix">
          <div class="layout-column layout-column--half">
            <div class="panel">
              <h3 class="panel__title">'. $links['page']['title'] .'</h3>
              <div class="panel__content">
                '. $links['page']['items'] .'
              </div>
            </div>
            <div class="panel">
              <h3 class="panel__title">'. $links['other']['title'] .'</h3>
              <div class="panel__content">
                '. $links['other']['items'] .'
              </div>
            </div>
          </div>
          <div class="layout-column layout-column--half">
            <div class="panel">
              <h3 class="panel__title">'. $links['article']['title'] .'</h3>
              <div class="panel__content">
                '. $links['article']['items'] .'
              </div>
            </div>
            <div class="panel">
              <h3 class="panel__title">'. $links['partner']['title'] .'</h3>
              <div class="panel__content">
                '. $links['partner']['items'] .'
              </div>
            </div>
          </div>
        </div>
      ',
    );

    return $build;
  }
}
