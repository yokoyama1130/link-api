<?php
require 'vendor/autoload.php';

use Cake\Auth\DefaultPasswordHasher;

$hasher = new DefaultPasswordHasher();
echo $hasher->hash('password123');
