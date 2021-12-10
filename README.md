This package provides a new complete user bundle out of the box (it's based on FOSUserBundle ideas and some code).

*This bundle is under development, more features will be added soon, and existing ones may change.*

[![Latest Stable Version](https://poser.pugx.org/softspring/user-bundle/v/stable.svg)](https://packagist.org/packages/softspring/user-bundle)
[![Latest Unstable Version](https://poser.pugx.org/softspring/user-bundle/v/unstable.svg)](https://packagist.org/packages/softspring/user-bundle)
[![License](https://poser.pugx.org/softspring/user-bundle/license.svg)](https://packagist.org/packages/softspring/user-bundle)
[![Total Downloads](https://poser.pugx.org/softspring/user-bundle/downloads)](https://packagist.org/packages/softspring/user-bundle)
[![Build status](https://travis-ci.com/softspring/user-bundle.svg?branch=master)](https://travis-ci.com/softspring/user-bundle)

# Installation

## Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require softspring/user-bundle
```

# Configuration

## Custom User entity class

Create your entity class, implementing Softspring\UserBundle\Model\UserInterface:

    <?php

    namespace App\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Softspring\UserBundle\Model\User as UserModel;

    /**
     * @ORM\Entity()
     */
    class User extends UserModel
    {

    }

Set your configuration:

    # config/packages/sfs_user.yaml
    sfs_user:
        class: App\Entity\User

## Override login configuration

You can add a custom login form with:

    <?php
    namespace App\Form;

    use Softspring\UserBundle\Form\LoginFormInterface;

    class CustomLoginForm extends AbstractType implements LoginFormInterface
    {
    }

and configuring the service as:

    App\Form\CustomLoginForm:
        tags: ['form.type']

    Softspring\UserBundle\Form\LoginFormInterface:
        alias: App\Form\CustomLoginForm

Also you can customize the login template creating this file with your custom twig code:

    # templates/bundles/SfsUserBundle/login/login.html.twig

    {% trans_default_domain 'sfs_user' %}
    {% extends '@SfsUser/layout_fullscreen.html.twig' %}

    {% block content %}
        {{ form_start(login_form) }}
            {{ form_rest(login_form) }}
            <input type="submit" value="{{ 'login.actions.login.button'|trans }}" class="btn btn-primary btn-block" />
        {{ form_end(login_form) }}
    {% endblock content %}

## Override register configuration

You can add a custom register form with:

    <?php
    namespace App\Form;

    use Softspring\UserBundle\Form\LoginFormInterface;

    class CustomLoginForm extends AbstractType implements LoginFormInterface
    {
    }

and configuring the service as:

    App\Form\CustomRegisterForm:
        tags: ['form.type']

    Softspring\UserBundle\Form\RegisterFormInterface:
        alias: App\Form\CustomRegisterForm

Also you can customize the register template creating this file with your custom twig code:

    # templates/bundles/SfsUserBundle/register/register.html.twig

    {% trans_default_domain 'sfs_user' %}
    {% extends '@SfsUser/layout_fullscreen.html.twig' %}

    {% block content %}
        {{ form_start(register_form) }}
            {{ form_rest(register_form) }}
            <input type="submit" value="{{ 'register.actions.register.button'|trans }}" class="btn btn-primary btn-block" />
        {{ form_end(register_form) }}
    {% endblock content %}


If you do not set a custom configuration, Softspring\UserBundle\Entity\Base\User will be used.

# Commands

## Create user

Create user with:

    bin/console sfs:user:create username email@example.com username

New users are disabled by default, you can enable them on creation with --enabled option:

    bin/console sfs:user:create username email@example.com username  --role=ROLE_ADMIN  --enabled

You can also add some roles to your user on creation:

    bin/console sfs:user:create username email@example.com username --role=ROLE_ADMIN
    bin/console sfs:user:create username email@example.com username --role=ROLE_ADMIN --role=ROLE_OTHER
    bin/console sfs:user:create username email@example.com username --role=ROLE_ADMIN --role=ROLE_SUPER_ADMIN

# Configuration reference

    # Default configuration for "SfsUserBundle"
    sfs_user:
        class:                Softspring\UserBundle\Entity\Base\User
        mailer:
            from:
                address:              ~
                name:                 ~
        history:
            enabled:              true
            class:                Softspring\UserBundle\Entity\History\UserAccess
        confirm_email:
            enabled:              true

