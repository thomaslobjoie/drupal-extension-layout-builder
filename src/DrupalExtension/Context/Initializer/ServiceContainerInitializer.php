<?php

namespace Kbrodej\Drupal\DrupalExtension\Context\Initializer;

use Behat\Behat\Context\Initializer\ContextInitializer;

class ServiceContainerInitializer implements ContextInitializer {
  /**
   * Service container instance.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerBuilder
   */
  private $container;

  /**
   * ServiceContainerInitializer constructor.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
   *   Service container instance.
   *
   * @see \Kbrodej\Drupal\DrupalExtension\ServiceContainer\DrupalExtension::loadContextInitializer()
   */
  public function __construct(ContainerBuilder $container) {
    $this->container = $container;
  }

  /**
   * Initializes provided context.
   *
   * @param \Behat\Behat\Context\Context $context
   *   Context instance.
   */
  public function initializeContext(Context $context) {
    if ($context instanceof ServiceContainerAwareInterface) {
      /** @var \Kbrodej\Drupal\DrupalExtension\Context\ServiceContainerAwareInterface $context */
      $context->setContainer($this->container);
    }
  }
}
