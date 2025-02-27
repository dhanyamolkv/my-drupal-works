<?php
/**
 * @file
 * Contains \Drupal\diagnostic_toolkit\Theme\ThemeNegotiator
 */
namespace Drupal\diagnostic_toolkit\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;
Use Drupal\user\Entity\User;

class ThemeNegotiator implements ThemeNegotiatorInterface {

    /**
     * @param RouteMatchInterface $route_match
     * @return bool
     */
    public function applies(RouteMatchInterface $route_match)
    {
        return $this->negotiateRoute($route_match) ? true : false;
    }

    /**
     * @param RouteMatchInterface $route_match
     * @return null|string
     */
    public function determineActiveTheme(RouteMatchInterface $route_match)
    {
        return $this->negotiateRoute($route_match) ?: null;
    }

    /**
     * Function that does all of the work in selecting a theme
     * @param RouteMatchInterface $route_match
     * @return bool|string
     */
    private function negotiateRoute(RouteMatchInterface $route_match)
    {   
        $userRolesArray = \Drupal::currentUser()->getRoles();
        $current_path = \Drupal::service('path.current')->getPath();
        $result = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
        $path = explode("/",$result); 
        $route_name = $route_match->getRouteName();
        
        if ($route_match->getRouteName() == 'user.register' || $route_name == 'view.diagnostic_toolkit_webform_data_submissions.page_1'
        || $route_name == 'view.diagnostic_toolkit_webform_confirmation_page.page_1' || $route_name == 'view.diagnostic_toolkit_user_dashboard.page_2'
        || $route_name == 'view.diagnostic_toolkit_user_dashboard.page_3' || $route_name == 'view.diagnostic_toolkit_webform_data_submissions.page_2')
        {
            return 'webform_survey';
        }
        if ($route_match->getRouteName() == 'entity.node.canonical' && $path[2] == 'diagnostic-toolkit-terms-use') {
		    return 'webform_survey';
		}
        if ($current_path == '/webform/diagnostic_toolkit' || $current_path == '/diagnostic-toolkit' 
        || $current_path == '/diagnostic-toolkit-user-dashboard' || $current_path == '/user/diagnostic-toolkit/login'
        || $current_path == '/user/register/diagnostic_toolkit' || $current_path == '/diagnostic-toolkit-and-sustainable-finance-roadmaps'
        || $current_path == '/sustainable-financial-architecture-for-sustainable-economic-transitions')
        {
            return 'webform_survey';
        }
        elseif($current_path == '/user/password') {
            $dest = \Drupal::request()->get('destination');
            if($dest == '/user/diagnostic-toolkit/login') {
                return 'webform_survey';
            }
        }
        elseif(strpos($current_path, '/user/reset/') !== false) {
            $result = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
            $args = explode("/",$result);
            
            if(isset($args[3])) {
                $uid = $args[3];
                $user = User::load($uid); 
                $roles = $user->getRoles();
                if (in_array('diagnostic_toolkit', $roles))  {
                    return 'webform_survey';
                }
            }
        }
        
        if($route_match->getRouteName() == "entity.user.edit_form") {
            $result = \Drupal::service('path_alias.manager')->getAliasByPath($current_path);
            $args = explode("/",$result);
            if(isset($args[2])) {
                $uid = $args[2];
                $user = User::load($uid); 
                $roles = $user->getRoles();
                if (in_array('diagnostic_toolkit', $roles))  {
                    return 'webform_survey';
                }
            }
        }        
        return false;
    }
}
