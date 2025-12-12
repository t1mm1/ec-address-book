# EC Address Book

A custom Drupal module that provides a customizable address book display for Drupal Commerce with enhanced template support.

<b>This module is styled with Tailwind CSS 3 by default. The styles can be customized or replaced with your own.
</b>

## Description

EC Address Book extends Drupal Commerce's address book functionality by allowing you to implement custom templates for displaying user addresses. The module integrates with the Address and Profile modules to provide a flexible way to present customer address information.

## Features

- Custom templating for address book display
- Integration with Drupal Commerce address book
- Support for multiple addresses per user
- Default address management
- Full CRUD operations (Create, Read, Update, Delete)
- User-friendly address display with custom Twig templates
- Compatible with Drupal 10 and 11

## Requirements

- Drupal: ^10 || ^11
- Commerce Core (commerce:commerce)
- Address module (address:address)
- Profile module (profile:profile)

## Installation

1. Download or clone this module into your Drupal `modules/custom` directory:
   ```bash
   cd modules/custom
   git clone https://github.com/t1mm1/ec-address-book.git ec_address_book
   ```

2. Enable the module using Drush:
   ```bash
   drush en ec_address_book
   ```

   Or enable it through the Drupal admin interface at:
   `Administration > Extend`

3. Clear the cache:
   ```bash
   drush cr
   ```

## Usage

### Accessing the Address Book

Once installed, users can access their address book at:
```
/user/{uid}/address-book
```

### Template Customization

The module uses a custom Twig template for rendering the address book. To customize the display:

1. Copy the `address-book_html.twig` template to your theme's templates directory
2. Modify the template according to your needs
3. Clear the cache

The template receives the following variables:
- `addresses` - Array of address objects with the following properties:
  - `profile_id` - Profile entity ID
  - `is_default` - Boolean indicating if this is the default address
  - `given_name` - First name
  - `family_name` - Last name
  - `organization` - Organization/company name
  - `address_line1` - Street address line 1
  - `address_line2` - Street address line 2
  - `locality` - City
  - `administrative_area` - State/province
  - `postal_code` - ZIP/postal code
  - `country_code` - Country code
  - `edit_url` - URL to edit the address
  - `delete_url` - URL to delete the address
  - `set_default_url` - URL to set as default (null if already default)
- `add_url` - URL to add a new address

### Access Control

The module implements custom access control that extends the Commerce address book access system. Users can only access their own address books unless they have administrative permissions.

## Module Structure

```
ec_address_book/
├── ec_address_book.info.yml          # Module definition
├── ec_address_book.module            # Module hooks
├── ec_address_book.routing.yml       # Route definitions
├── src/
│   └── Controller/
│       └── AddressBookController.php # Main controller
└── templates/
    └── address-book_html.twig        # Twig template
```

## Development

### Extending the Controller

The `AddressBookController` extends the Commerce `AddressBookController` and can be further customized:

```php
<?php

namespace Drupal\your_module\Controller;

use Drupal\ec_address_book\Controller\AddressBookController as BaseAddressBookController;

class CustomAddressBookController extends BaseAddressBookController {
  // Your customizations here
}
```

### Logging

The module includes logging functionality. Errors are logged to the `ec_address_book` channel and can be viewed in the Drupal logs:

```bash
drush watchdog:show --type=ec_address_book
```

## Configuration

No additional configuration is required. The module works out of the box with the Commerce and Profile modules.

## Troubleshooting

### Addresses not displaying

1. Ensure users have customer profiles with the type `customer`
2. Verify that the Address field is properly configured on the customer profile
3. Check that profiles are published (status = 1)
4. Review logs for any errors: `drush watchdog:show --type=ec_address_book`

### Access denied errors

Ensure that users have the appropriate permissions to access their address book. The module checks for `_address_book_access` permission.

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## Support

For bug reports and feature requests, please use the issue queue on GitHub.

## License

This project is licensed under the GPL-2.0+.

## Credits

Developed for enhanced Drupal Commerce address book functionality.

## Changelog

### 1.0.0
- Initial release
- Custom address book display
- Twig template support
- Integration with Commerce, Address, and Profile modules
- Support for Drupal 10 and 11
