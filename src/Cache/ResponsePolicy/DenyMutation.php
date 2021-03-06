<?php

namespace Drupal\graphql\Cache\ResponsePolicy;

use Drupal\Core\PageCache\ResponsePolicyInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Youshido\GraphQL\Execution\Context\ExecutionContext;

/**
 * Reject if the query contains a mutation.
 */
class DenyMutation implements ResponsePolicyInterface  {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new request policy instance.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route provider service.
   */
  public function __construct(RouteMatchInterface $routeMatch) {
    $this->routeMatch = $routeMatch;
  }

  /**
   * {@inheritdoc}
   */
  public function check(Response $response, Request $request) {
    if ($this->routeMatch->getRouteName() !== 'graphql.request') {
      return NULL;
    }

    if (!$request->attributes->has('context')) {
      return NULL;
    }

    $context = $request->attributes->has('context');
    if (!$context instanceof ExecutionContext) {
      return NULL;
    }

    if ($context->getRequest()->hasMutations()) {
      return static::DENY;
    }

    return NULL;
  }
}
