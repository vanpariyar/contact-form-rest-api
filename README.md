# Contact Form REST API

A WordPress plugin that provides a REST API for contact form submissions. This plugin allows you to submit contact forms via REST API endpoints and manage contact submissions through the WordPress admin.

## Features

- **REST API Endpoints**: Submit contact forms via REST API
- **Custom Post Type**: Contact submissions are stored as custom post types
- **Email Notifications**: Automatic email notifications for new submissions
- **Admin Interface**: Manage contact submissions in WordPress admin
- **Validation**: Built-in validation for required fields and email format
- **Security**: Proper sanitization and permission checks

## Installation

1. Upload the plugin files to `/wp-content/plugins/contact-form-rest-api/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The REST API endpoints will be automatically available

## API Endpoints

### Submit Contact Form

**Endpoint:** `POST /wp-json/contact-form/v1/submit`

**Description:** Submit a new contact form

**Required Fields:**
- `name` (string): Contact name
- `email` (string): Contact email address
- `message` (string): Contact message

**Optional Fields:**
- `phone` (string): Contact phone number
- `subject` (string): Contact subject

**Example Request:**
```bash
curl -X POST "https://your-site.com/wp-json/contact-form/v1/submit" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "1234567890",
    "subject": "General Inquiry",
    "message": "Hello, I would like to know more about your services."
  }'
```

**Success Response (201):**
```json
{
  "id": 123,
  "message": "Contact form submitted successfully",
  "status": "success"
}
```

**Error Response (400):**
```json
{
  "code": "missing_field",
  "message": "Missing required field: email",
  "data": {
    "status": 400
  }
}
```

### Get Contact Submissions (Admin Only)

**Endpoint:** `GET /wp-json/contact-form/v1/submissions`

**Description:** Retrieve all contact submissions (requires `edit_posts` capability)

**Query Parameters:**
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 10)

**Example Request:**
```bash
curl -X GET "https://your-site.com/wp-json/contact-form/v1/submissions?page=1&per_page=10" \
  -H "Authorization: Basic base64-encoded-credentials"
```

**Success Response (200):**
```json
[
  {
    "id": 123,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "1234567890",
    "subject": "General Inquiry",
    "message": "Hello, I would like to know more about your services.",
    "date": "2024-01-15 10:30:00",
    "status": "publish"
  }
]
```

### Get Single Contact Submission (Admin Only)

**Endpoint:** `GET /wp-json/contact-form/v1/submissions/{id}`

**Description:** Retrieve a specific contact submission (requires `edit_posts` capability)

**Example Request:**
```bash
curl -X GET "https://your-site.com/wp-json/contact-form/v1/submissions/123" \
  -H "Authorization: Basic base64-encoded-credentials"
```

**Success Response (200):**
```json
{
  "id": 123,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "1234567890",
  "subject": "General Inquiry",
  "message": "Hello, I would like to know more about your services.",
  "date": "2024-01-15 10:30:00",
  "status": "publish"
}
```

**Error Response (404):**
```json
{
  "code": "not_found",
  "message": "Contact submission not found",
  "data": {
    "status": 404
  }
}
```

## Authentication

### Public Endpoints
- Contact form submission (`POST /contact-form/v1/submit`) is publicly accessible

### Protected Endpoints
- Getting contact submissions requires authentication
- Use WordPress Basic Authentication or JWT tokens
- User must have `edit_posts` capability

### Basic Authentication Example
```bash
curl -X GET "https://your-site.com/wp-json/contact-form/v1/submissions" \
  -u "username:password"
```

## WordPress Admin

After activation, you'll find a new "Contacts" menu item in your WordPress admin. This allows you to:

- View all contact submissions
- Edit contact details
- Delete submissions
- Categorize contacts using contact categories

## Customization

### Email Notifications

The plugin automatically sends email notifications to the site admin when a new contact form is submitted. You can customize this by modifying the `send_notification_email` method in `api/controllers/contact.php`.

### Custom Fields

To add custom fields to the contact form:

1. Update the `get_endpoint_args_for_item_schema` method in `api/routes/contact.php`
2. Modify the `submit_contact_form` method to handle the new fields
3. Update the `prepare_contact_submission_for_response` method to include the new fields

### Styling

The admin interface can be customized by adding CSS to your theme or by modifying the admin customizer file.

## Development

### Running Tests

```bash
# Install WordPress test environment
./bin/install-wp-tests.sh wordpress_test root '' localhost latest

# Run tests
./vendor/bin/phpunit
```

### File Structure

```
contact-form-rest-api/
├── api/
│   ├── controllers/
│   │   └── contact.php          # Contact form processing logic
│   ├── routes/
│   │   └── contact.php          # REST API route definitions
│   └── basic-auth.php           # Basic authentication handler
├── admin/
│   └── customiser.php           # Admin interface customization
├── includes/
│   └── plugin_helpers.php       # Plugin activation/deactivation hooks
├── tests/
│   └── test-endpoint.php        # API endpoint tests
├── contact-form-rest-api.php    # Main plugin file
└── README.md                    # This file
```

## Error Handling

The API returns appropriate HTTP status codes:

- `200`: Success (GET requests)
- `201`: Created (POST requests)
- `400`: Bad Request (validation errors)
- `403`: Forbidden (insufficient permissions)
- `404`: Not Found (resource doesn't exist)
- `500`: Internal Server Error

## Security Considerations

- All input is sanitized using WordPress sanitization functions
- Email addresses are validated using WordPress `is_email()` function
- Admin endpoints require proper authentication and capabilities
- CSRF protection is handled by WordPress REST API framework

## Support

For issues and feature requests, please create an issue on the GitHub repository.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### Version 1.0.0
- Initial release
- Contact form submission via REST API
- Admin interface for managing submissions
- Email notifications
- Basic authentication support