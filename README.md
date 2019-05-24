# Puzzle Advert Bundle

Project based on Symfony project for managing advert accounts and advert security.

## **Installation**

---

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```yaml
composer require webundle/puzzle-advert-bundle
```

## **Configurations**

### **Step 1: Enable**
Enable admin bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Puzzle\AdvertBundle\AdvertBundle(),
        );

        // ...
    }

    // ...
}
```

### **Step 2: Security**
Configure security by adding it in the `app/config/security.yml` file of your project:
```yaml
security:
    role_hierarchy:
        ...
        # Advert
        ROLE_ADVERT: ROLE_ADMIN
        ROLE_SUPER_ADMIN: [..,ROLE_ADVERT]

    access_control:
        ...
        # Advert
        - {path: ^%admin_prefix%advert, host: "%admin_host%", roles: ROLE_ADVERT }

```

### **Step 3: Register default routes**
Register default routes by adding it in the `app/config/routing.yml` file of your project:
```yaml
....
advert:
    resource: "@AdvertBundle/Resources/config/routing.yml"
    prefix:   /

```
See all advert routes by typing: `php bin/console debug:router | grep advert`

### **Step 3: Configure**
Configure admin bundle by adding it in the `app/config/config.yml` file of your project:
```yaml
admin:
    ...
    modules_available: '..,advert'
    navigation:
        nodes:
            ...
            # Advert
            advert:
                label: 'advert.title'
                description: 'advert.description'
                translation_domain: 'messages'
                attr:
                    class: 'fa fa-newspaper-o'
                parent: ~
                user_roles: ['ROLE_ADVERT']
            advert_post:
                label: 'advert.post.sidebar'
                description: 'advert.post.sidebar'
                translation_domain: 'messages'
                path: 'puzzle_admin_advert_post_list'
                sub_paths: ['puzzle_admin_advert_post_create', 'puzzle_admin_advert_post_update', 'puzzle_admin_advert_post_show', 'puzzle_admin_advert_postulate_list']
                parent: advert
                user_roles: ['ROLE_ADVERT']
            advert_advertiser:
                label: 'advert.advertiser.sidebar'
                description: 'advert.advertiser.sidebar'
                translation_domain: 'messages'
                path: 'puzzle_admin_advert_advertiser_list'
                sub_paths: ['puzzle_admin_advert_advertiser_create', 'puzzle_admin_advert_advertiser_update', 'puzzle_admin_advert_advertiser_show']
                parent: advert
                user_roles: ['ROLE_ADVERT']
            advert_category:
                label: 'advert.category.sidebar'
                description: 'advert.category.sidebar'
                translation_domain: 'messages'
                path: 'puzzle_admin_advert_category_list'
                sub_paths: ['puzzle_admin_advert_category_create', 'puzzle_admin_advert_category_update', 'puzzle_admin_advert_category_show']
                parent: advert
                user_roles: ['ROLE_ADVERT']
```
