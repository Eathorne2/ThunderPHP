<?php

namespace ThunderAuthentication;

defined('ROOTPATH') or die('Direct script access denied');

auth_admin_guard('auth.view_roles');
set_value(['roles' => auth_all_roles()]);
