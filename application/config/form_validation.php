<?php


///////////////////////////
// Form based validation //
///////////////////////////

$config = array(

    'login' => array(
        array(
            'field' => 'user_id',
            'label' => 'User Id',
            'rules' => 'trim|required|max_length[15]',
            'errors' => array(
                'required' => 'User Id is required.',
                'max_length' => 'Max length limit'
            )
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'trim|required|max_length[20]'
        )
    )

);


/////////////////////
// Error delimiter //
/////////////////////

// $config['error_prefix'] = '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
// $config['error_suffix'] = '</div>';

$config['error_prefix'] = '<p><small>';
$config['error_suffix'] = '</small></p>';
