<?php
use Drupal\search_api\Query\QueryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Views\ViewExecutable;
use Drupal\Views\Plugin\views\query\QueryPluginBase;
use Drupal\views\Views;
use Drupal\taxonomy;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\block\Entity\Block;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\domain_access\DomainAccessManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Database\Query\ConditionInterface;
Use \Drupal\user\Entity\User;
use Drupal\Core\Render\BubbleableMetadata;

/*
* implement hook_token_info()
*/
function ggkp_custom_token_info(){
  $types['ggkp_custom_token_type'] = array(
    'name' => t('GGKP Custom token Type'),
    'description' => t('GGKP Custom token type')
  );	
  $tokens['custom_token_name'] = array(
    'name' => t('GGKP Custom token name'),
    'description' => t('GGKP Custom token name')
  );
	
  return array(
    'types' => $types,
    'tokens' => array(
      'ggkp_custom_token_type' => $tokens
    )
  );	
}



/**
 * Implements hook_tokens().
 */
function ggkp_custom_tokens($type, $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
  $replacements = [];

  if ($type === 'node' && !empty($data['node'])) {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $data['node'];

    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'canonical-source-domain-url':
          $replacements[$original] = $node->toUrl('canonical')->setAbsolute()->toString();
          if ($node->hasField('field_domain_source') && !$node->field_domain_source->isEmpty()) {
            /** @var \Drupal\domain\Domain $sourceDomain */
            $sourceDomain = $node->field_domain_source->entity;
            $replacements[$original] = $sourceDomain->buildUrl($node->toUrl()->toString());
          }
          break;
      }
    }
  }
  return $replacements;
}
?>