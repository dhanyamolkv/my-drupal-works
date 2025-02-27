<?php

namespace Drupal\ggkp_custom;

use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Drupal\user\RoleStorageInterface;
use Drupal\Core\Render\Markup;



/**
 * Class DefaultService.
 *
 * @package Drupal\ggkp_custom
 */
class TwigExtension extends \Twig_Extension {
  /**
   * In this function we can declare the extension function.
   */
    public function getFunctions() {
        return [
            //new \Twig_SimpleFunction('featured_heading_sme_detail', [$this, 'featured_heading_sme_detail'], ['is_safe' => ['html']]),
            //new \Twig_SimpleFunction('sme_intro_text', [$this, 'sme_intro_text'], ['is_safe' => ['html']]),
            //new \Twig_SimpleFunction('editOption', [$this, 'editOption'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('load_domain', [$this, 'load_domain'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('published_at_date', [$this, 'published_at_date'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('get_regional_programme', [$this, 'get_regional_programme'], ['is_safe' => ['html']]),
			new \Twig_SimpleFunction('checkCurrentUserRole', [$this, 'checkCurrentUserRole'], ['is_safe' => ['html']]),
			new \Twig_SimpleFunction('get_user_roles', [$this, 'getUserRoles'], ['is_safe' => ['html']]),
			new \Twig_SimpleFunction('getExternalUserDataLink', [$this, 'getExternalUserDataLink'], ['is_safe' => ['html']]),
			new \Twig_SimpleFunction('getTermPattern', [$this, 'getTermPattern'], ['is_safe' => ['html']]),
        ];
    }

    /* for getting regional count for calender*/
   function get_regional_programme($nid)
  {
	  $nid=$nid->__tostring();
	  $node = \Drupal\node\Entity\Node::load($nid);
	  $programmes= $node->field_regional_programme->getValue();
	  return count($programmes);

  }


    /* Different Heading for Featured section in SME Detail page */
    function featured_heading_sme_detail($type){
		$current_path = \Drupal::service('path.current')->getPath();
		$url = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
		$args = explode('/', $current_path);

		if (in_array("type", $args)) {
			$counts = array_count_values($args);
			if ($counts['type'] > 1) {
				return '<div class="sme-featured-view featured-content-block featured-research-block"><h2>Featured Knowledge</h2></div>';
			}
			if ($counts['type'] == '1') {
				if (in_array("case_studies", $args)) {
				    return '<div class="sme-featured-view featured-content-block featured-research-block"><h2>Featured Case Studies</h2></div>';
				}
				if (in_array("tools_and_platforms", $args)) {
					return '<div class="sme-featured-view featured-content-block featured-research-block"><h2>Featured Tools</h2></div>';
				}
				if (in_array("platforms", $args)) {
					return '<div class="sme-featured-view featured-content-block featured-research-block"><h2>Featured Platforms</h2></div>';
				}
				if (in_array("guidance", $args)) {
					return '<div class="sme-featured-view featured-content-block featured-research-block"><h2>Featured Guidance</h2></div>';
				}
				if (in_array("financial_solutions", $args)) {
					return '<div class="sme-featured-view featured-content-block featured-research-block"><h2>Featured Financial Solutions</h2></div>';
				}
				if (in_array("courses", $args)) {
					return '<div class="sme-featured-view featured-content-block featured-research-block"><h2>Featured Training</h2></div>';
				}
			}
		}
		if (!in_array("type", $args)) {
            return '<div class="sme-featured-view featured-content-block featured-research-block"><h2>Featured Knowledge</h2></div>';
        }
    }
    /* Introduction text of First level parent in SME detail page -> disabling as per client suggested */
    //~ function sme_intro_text() {
	    //~ $uri = $_SERVER['REQUEST_URI'];
        //~ $url = explode("/", $uri);
		//~ foreach ($url as $k => $value){
			//~ $pattern = "/field_sme_operations_support_cen/";
			//~ $pregvalue = preg_match($pattern, $value);
			//~ if ($pregvalue == 1) {
			   //~ $i = $k; // Get the key
			   //~ $x = $i + 1;
			   //~ $termvalue = $url[$x];
			   //~ $termValue = explode("-", $termvalue);
			   //~ $termid = end($termValue);
		   //~ }
		//~ }
		//~ // load taxonomy parents from termid
        //~ $parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($termid);
        //~ $parent = reset($parent);
        //~ $sec_parent_tid = $parent->id();

        //~ $first_parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($sec_parent_tid);
        //~ $first_parent = reset($first_parent);
        //~ $first_parent_id = $first_parent->id();

        //~ $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($first_parent_id);
        //~ //$first_parentDesc = $term->field_description->value;
        //~ $first_parentDesc = $term->description->value;
        //~ if (isset($first_parentDesc) && $first_parentDesc != '') {
		   //~ return '<div class="sme-parent-intro-desc">'.$first_parentDesc.'</div>';
	    //~ }
	//~ }
    /* Edit option for sme taxonomy term in SME detail page -> disabling as per client suggested */
    //~ function editOption() {
		//~ $uri = $_SERVER['REQUEST_URI'];
        //~ $url = explode("/", $uri);
		//~ foreach ($url as $k => $value){
			//~ $pattern = "/field_sme_operations_support_cen/";
			//~ $pregvalue = preg_match($pattern, $value);
			//~ if ($pregvalue == 1) {
			   //~ $i = $k; // Get the key
			   //~ $x = $i + 1;
			   //~ $termvalue = $url[$x];
			   //~ $termValue = explode("-", $termvalue);
			   //~ $termid = end($termValue);
		   //~ }
		//~ }
		//~ // load taxonomy parents from termid
        //~ $parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($termid);
        //~ $parent = reset($parent);
        //~ $sec_parent_tid = $parent->id();

        //~ $first_parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($sec_parent_tid);
        //~ $first_parent = reset($first_parent);
        //~ $first_parent_id = $first_parent->id();
        //~ $term = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($first_parent_id);
        //~ //$first_parentDesc = $term->field_description->value;
        //~ $first_parentDesc = $term->description->value;
        //~ if (isset($first_parentDesc) && $first_parentDesc != '') {
			//~ return '<a href ="/taxonomy/term/'.$first_parent_id.'/edit" target="_blank">Edit</a>';
		//~ }
	//~ }

    function load_domain() {
	    $loader = \Drupal::service('domain.negotiator');
        $current_domain = $loader->getActiveDomain();
        $current_domain_id = $current_domain->id();
        return $current_domain_id;
	}
	/* Published at date in Manage all contents in dashboard */
	function published_at_date($published_at) {
		$current_date = strtotime("now");
		$published_at_date = strtotime($published_at);
		if ($current_date < $published_at_date) {
			return '';
		} else {
		    return $published_at;
	    }
	}
	function showSMEFilterInDetailPage() {
		// Getting the referer.
		$request = \Drupal::request();
		$referer = $request->headers->get('referer');
	}
	function checkCurrentUserRole() {
		$current_user = \Drupal::currentUser(); //returns an AccountProxyInterface object and not a UserInterface object
		$current_user_uid = $current_user->id(); // numberic uid value
		$current_user_roles = $current_user->getRoles(); // array List of role IDs

		// Check user has specific role; example 'administrator' role
		if (in_array('ggkp_admin', $current_user_roles)) {
			// do something if user has this role
			return 'ggkp_admin';
		}
	}

  /**
   * Get roles for a given user ID.
   *
   * @param int $userId
   *   The user ID.
   *
   * @return array
   *   An array of user roles.
   */
  public function getUserRoles($userId) {
      //dsm($userId);
		if ($userId instanceof Markup) {
			//Used to change markup id to integer.
      $userId = (int) $userId->__toString();
    }
    $user = User::load($userId);
    if ($user) {
        $roles = $user->getRoles();
        // Check if the user has any roles.
        if (count($roles) >= 2) {
            $role_storage = \Drupal::service('entity_type.manager')->getStorage('user_role');
            $second_role_id = $roles[1]; // Get the second role ID.
            $second_role = $role_storage->load($second_role_id);

            if ($second_role) {
                return $second_role->label();
            }
        }
    }
			//return $role_labels;
		
    //return [];
  }
  Public function getExternalUserDataLink($nid) {
	$nid=$nid->__tostring();
	$node = \Drupal\node\Entity\Node::load($nid);
	$rid = $node->field_knowledge_resource_id->getValue()[0]['value'];
	$link = '';
	//global $base_url;
	if(isset($rid)) {
		//$alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/'.$rid);
		$options = ['absolute' => TRUE];
		$alias = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $rid], $options)->toString();
		$link = '<a href="' . $alias . '">'. $alias.'</a>';
	}
	return $link;
  }

  public function getTermPattern($term_name, $id) {
	$term_name = \Drupal::service('pathauto.alias_cleaner')
        ->cleanString($term_name);
    return $term_name . '-' . $id; 
  }
}

