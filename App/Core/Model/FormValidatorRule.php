<?php

namespace App\Core\Model;

enum FormValidatorRule {
    case NotEmpty;
    case IsInteger;
    case IsLess;
    case IsGreater;
    case IsEmail;
}
