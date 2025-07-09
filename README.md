# contact-form-rest-api
This is the simple Plugin to implement WordPress Headless Contact Entries.

### Features:
- Easy To use
- Works with any site
- Basic Auth Included 
- Manage, Contact and all

### Currently in Development Please Make sure Before Download

## Installation

```bash
docker exec -it <wordpress_container_name> bash

# From inside the container:
cd /var/www/html/wp-content/plugins/contact-form-rest-api

# Install test environment using wp scaffold
wp scaffold plugin-tests contact-form-rest-api
```

Or manually download WordPress test library and configure `WP_TESTS_DIR`.

---

### 6. **Run PHPUnit**

Once everything is set up, you can run:

```bash
docker exec -it <wordpress_container_name> phpunit
```

### Setup Test
```
DB_NAME=wordpress_test \
DB_USER=wpuser \
DB_PASS=wppass \
DB_HOST=mysql \
WP_VERSION=6.5 \
WP_TESTS_DIR=/tmp/wordpress-tests-lib \
bash bin/install-wp-tests.sh
```

### Want New Features ? or Found New Issue Please let me know.
https://vanpariyar.github.io/ Or Make Issue on Github.

OR You can Contribute :+1:

Jay Swaminarayan