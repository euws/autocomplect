<?php

namespace Application\Form;

use Zend\Form\Form;

class RegistrationForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('login');

        $this->add([
            'name' => 'email',
            'type' => 'email',
            'required' => true,
        ]);
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'required' => true,
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Зарегистрироваться',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}