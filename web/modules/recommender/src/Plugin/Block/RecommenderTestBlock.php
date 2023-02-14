<?php
namespace Drupal\recommender\Plugin\Block;

use Drupal\Component\Uuid\Com;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\node\NodeInterface;
use Drupal\user\Entity\User;
/**
 * Provides a 'Redommender' Block
 *
 * @Block(
 *   id = "recommender_item",
 *   admin_label = @Translation("Recommender item"),
 * )
 */
class RecommenderTestBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // $config = $this->getConfiguration();
    // $config_factory = \Drupal::configFactory();
    // $config2 = $config_factory->getEditable('recommender.settings');
    $config = $this->getConfiguration();
    if (!empty($config['limit'])) {
      $limit = $config['limit'];
    } else {
        $limit = 1;
    }
    // $config = \Drupal::config('recommender_setting.settings');
    // if(!empty($config)) {
    //     \Drupal::logger('recommender')->notice('testLog'.$config->get('limit'));
    //     // $limit = $config->get('limit');
    //     $limit = 2;
    // } else {
    //     $limit = 1;
    // }
    // \Drupal::service('page_cache_kill_switch')->trigger();
    // $build['#cache']['max-age'] = 0;
    // if (!empty($config['hello_block_settings'])) {
    //   $name = $config['hello_block_settings'];
    // }
    // else {
    //   $name = $this->t('to no one');
    // }
    // $result = \Drupal::database()->query('SELECT [nid], [timestamp] FROM {history} WHERE [uid] = :uid', [
    //     ':uid' => \Drupal::currentUser()->id()
    //     // ':nids[]' => array_keys($nodes_to_read),
    // ]);
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
        // You can get nid and anything else you need from the node object.
        $nid = $node->id();
    }
    
    $result2 = \Drupal::database()->query('SELECT [nid] FROM {history} WHERE [nid] <> :nid AND [uid] IN(SELECT [uid] FROM {history} WHERE [nid] = :nid) GROUP BY [nid] ORDER BY MAX([timestamp]) DESC LIMIT ' . $limit, [
        // ':uid' => \Drupal::currentUser()->id()
        // ':nids[]' => array_keys($nodes_to_read),
        ':nid' => $nid
    ]);
    // Console::log($nid);
    $form['mytable'] = array(
        '#type' => 'table',
        '#empty' => t('There are no recommend contents.'),
        '#header' => array(t('Link')),
    );
    foreach ($result2 as $row) {
        // $nodes_to_read[$row->nid] = (int) $row->timestamp;
        $recommend_link = Link::fromTextAndUrl(t('Recommend Content @nid',  ['@nid' =>  $row->nid]), Url::fromUri('internal:/node/' . $row->nid ))->toString();
        $form['mytable'][] = array(
            array('#type' => 'markup', '#markup' => $recommend_link)
        );
    }
    // $recommend_link = Link::fromTextAndUrl(t('Recommend Content'), Url::fromUri('internal:/node/2' ))->toString();
    // return array(
    // //   '#markup' => $this->t('Hello @name!', array(
    // //       '@name' => $name,
    // //     )
    // //   ),
    // // '#markup' => $recommend_links,
    // '#markup' => $config->get('message')
    // );
    // $user = User::create();
    // $user->set('status', 1);
    // $user->setUsername('test123456789');
    // $user->setPassword('test123456789');
    // $user->addRole('Administrator');
    // $user->save();
    return $form;
  }
    /**
     * {@inheritdoc}
     */
    public function getCacheMaxAge()
    {
        return 0;
    }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $default_config = \Drupal::config('recommender.settings');
    $config = $this->getConfiguration();

    $form['recommender_block_settings'] = array (
      '#type' => 'textfield',
      '#title' => $this->t('Count'),
      '#description' => $this->t('Max item count to show?'),
      '#default_value' => isset($config['limit']) ? $config['limit'] : $default_config->get('limit'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('limit', $form_state->getValue('recommender_block_settings'));
  }
}
