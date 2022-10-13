<?php

namespace Drupal\mandiant_tweaks\EventSubscriber;

// This is the interface we are going to implement.
use Drupal\Core\Condition\ConditionManager;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// This class contains the event we want to subscribe to.
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Url;
use Drupal\disable_language\DisableLanguageManager;
use Drupal\Core\Language\LanguageManager;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\path_alias\AliasManagerInterface;

/**
 * Subscribe to KernelEvents::REQUEST events.
 */
class DisabledLanguages implements EventSubscriberInterface {

  /**
   * Contains disable_language.disable_language_manager service.
   *
   * @var \Drupal\disable_language\DisableLanguageManager
   */
  protected $disableLanguageManager;

  /**
   * Contains current_user service.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * This module's settings configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * The schemes of all available StreamWrapper.
   *
   * @var array
   */
  protected $schemes;

  /**
   * A plugin manager for conditions plugins.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $conditionManager;

  /**
   * DisabledLanguagesEventSubscriber constructor.
   *
   * @param \Drupal\disable_language\DisableLanguageManager $disableLanguageManager
   *   Class DisableLanguageManager.
   * @param \Drupal\Core\Session\AccountProxyInterface $currentUser
   *   A proxied implementation of AccountInterface.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory.
   * @param \Drupal\Core\Condition\ConditionManager $conditionManager
   *   A plugin manager for conditions plugins.
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManager $streamWrapperManager
   */
  public function __construct(DisableLanguageManager $disableLanguageManager, AccountProxyInterface $currentUser, ConfigFactoryInterface $configFactory, ConditionManager $conditionManager, StreamWrapperManager $streamWrapperManager, languageManager $languageManager, AliasManagerInterface $path_alias_manager) {
    $this->currentUser = $currentUser;
    $this->disableLanguageManager = $disableLanguageManager;
    $this->config = $configFactory->get('disable_language.settings');
    $this->conditionManager = $conditionManager;
    $this->schemes = array_keys($streamWrapperManager->getWrappers());
    $this->languageManager = $languageManager;
    $this->pathAliasManager = $path_alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // On a normal request.
    $events[KernelEvents::REQUEST][] = ['checkForDisabledLanguageAndRedirect', 100];
    // On an access denied request.
    $events[KernelEvents::EXCEPTION][] = [
      'checkForDisabledLanguageAndRedirect',
      100,
    ];
    return $events;
  }

  /**
   * {@inheritdoc}
   */
  public function checkForDisabledLanguageAndRedirect(GetResponseEvent $event) {
    // Do not redirect if this is a file.
    $params = $event->getRequest()->attributes->all();

    if (isset($params['scheme']) && in_array($params['scheme'], $this->schemes)) {
      return;
    }
    elseif ($this->isPathExcluded()) {
      return;
    }
    elseif ($this->currentUser->isAnonymous()) {
      if ($this->disableLanguageManager->isCurrentLanguageDisabled()) {
        $current_language = $this->languageManager->getCurrentLanguage();

        $lang_code = '';
        switch ($current_language->getId()) {
          case 'ja':
            $lang_code = 'jp';
            break;
          case 'ko':
            $lang_code = 'kr';
            break;
          default:
            $lang_code = $current_language->getId();
            break;
        }

        $path = '/' . $lang_code . '-landing';

        $resolved_path = $this->pathAliasManager->getPathByAlias($path, 'en');

        if ($resolved_path !== $path) {
          $url = Url::fromUserInput($path, ['language' => 'en']);

          // Set the response.
          $cache = new CacheableMetadata();
          $cache->addCacheContexts(['languages', 'url', 'user.permissions']);
          $cache->addCacheableDependency($this->config);
          $cache->addCacheableDependency($current_language);
          $response = new TrustedRedirectResponse('https://www.mandiant.com'. $path, '307');
          $response->addCacheableDependency($cache);
          $event->setResponse($response);
        }
      }
    }
  }

  /**
   * Check if the path belong to the list of excluded ones.
   *
   * @return bool
   *   Whether or not the requested path being accessed is excluded.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Core\Executable\ExecutableException
   */
  private function isPathExcluded() {
    if (($excluded_path_config = $this->config->get('exclude_request_path')) && !empty($excluded_path_config['pages'])) {
      /** @var \Drupal\system\Plugin\Condition\RequestPath $condition */
      $condition = $this->conditionManager->createInstance('request_path');
      $condition->setConfiguration($excluded_path_config);
      return $this->conditionManager->execute($condition);
    }

    return FALSE;
  }

}
