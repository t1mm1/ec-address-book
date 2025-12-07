<?php

namespace Drupal\ec_address_book\Controller;

use Drupal\commerce_order\AddressBookInterface;
use Drupal\commerce_order\Controller\AddressBookController as CommerceAddressBookController;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\user\UserInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for address book page.
 */
class AddressBookController extends CommerceAddressBookController {

  /**
   * Theme for list
   *
   * @var string
   */
  protected string $theme = 'ec_address_book';

  /**
   * Cache contexts.
   *
   * @var array
   */
  protected array $cache = [
    'contexts' => [
      'user',
      'user.permissions',
      'url',
    ],
  ];

  /**
   * A logger instance.
   *
   * @var LoggerInterface
   */
  protected LoggerInterface $logger;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    AddressBookInterface $address_book,
    EntityFormBuilderInterface $entity_form_builder,
    EntityTypeManagerInterface $entity_type_manager,
    LoggerInterface $logger,
  ) {
    parent::__construct(
      $address_book,
      $entity_form_builder,
      $entity_type_manager,
    );

    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('commerce_order.address_book'),
      $container->get('entity.form_builder'),
      $container->get('entity_type.manager'),
      $container->get('logger.factory')->get('ec_address_book')
    );
  }

  /**
   * Displays the address book page.
   *
   * @param UserInterface $user
   *   The user entity.
   *
   * @return array
   *   A render array.
   */
  public function page(UserInterface $user): array {
    try {
      $profile_storage = $this->entityTypeManager->getStorage('profile');
    }
    catch (\Exception $e) {
      $this->logger->error($e->getMessage());
    }

    if (empty($profile_storage)) {
      return [
        '#theme' => $this->theme,
        '#cache' => $this->cache,
      ];
    }

    try {
      $profiles = $profile_storage->loadByProperties([
        'uid' => $user->id(),
        'type' => 'customer',
        'status' => 1,
      ]);
    }
    catch (\Exception $e) {
      $this->logger->error($e->getMessage());
    }

    // Prepare addresses data
    $addresses = [];
    if (empty($profiles)) {
      return [
        '#theme' => $this->theme,
        '#cache' => $this->cache,
      ];
    }

    foreach ($profiles as $profile) {
      $address_field = $profile->get('address')->first();

      if ($address_field) {
        $addresses[] = [
          'profile_id' => $profile->id(),
          'is_default' => $profile->isDefault(),
          'given_name' => $address_field->getGivenName(),
          'family_name' => $address_field->getFamilyName(),
          'organization' => $address_field->getOrganization(),
          'address_line1' => $address_field->getAddressLine1(),
          'address_line2' => $address_field->getAddressLine2(),
          'locality' => $address_field->getLocality(),
          'administrative_area' => $address_field->getAdministrativeArea(),
          'postal_code' => $address_field->getPostalCode(),
          'country_code' => $address_field->getCountryCode(),
          'edit_url' => Url::fromRoute('commerce_order.address_book.edit_form', [
            'user' => $user->id(),
            'profile' => $profile->id(),
          ])->toString(),
          'delete_url' => Url::fromRoute('commerce_order.address_book.delete_form', [
            'user' => $user->id(),
            'profile' => $profile->id(),
          ])->toString(),
          'set_default_url' => !$profile->isDefault() ? Url::fromRoute('commerce_order.address_book.set_default', [
            'user' => $user->id(),
            'profile' => $profile->id(),
          ])->toString() : NULL,
        ];
      }
    }

    // Sort: default first.
    usort($addresses, function($a, $b) {
      return $b['is_default'] <=> $a['is_default'];
    });

    $add_url = Url::fromRoute('commerce_order.address_book.add_form', [
      'user' => $user->id(),
      'profile_type' => 'customer',
    ])->toString();

    return [
      '#theme' => $this->theme,
      '#addresses' => $addresses,
      '#add_url' => $add_url,
      '#cache' => $this->cache,
    ];
  }

  /**
   * Checks access for the address book page.
   *
   * @param UserInterface $user
   *   The user entity.
   * @param AccountInterface $account
   *   The current user account.
   *
   * @return AccessResultInterface
   *   The access result.
   */
  public function access(UserInterface $user, AccountInterface $account): AccessResultInterface {
    return $this->checkOverviewAccess($user, $account);
  }

}
