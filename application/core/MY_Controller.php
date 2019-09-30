<?php

class MY_Controller extends CI_Controller
{
    protected $data = array();
    protected $dashboard_breadcrumb = array();
    protected $pelanstrategik_breadcrumb = array();
    protected $skt_breadcrumb = array();
    protected $sector_breadcrumb = array();
    protected $bahagian_breadcrumb = array();

    function __construct()
    {
        parent::__construct();

		$this->load->library('form_validation');

        $this->data['page_title'] = 'API Dashboard';

        //check logged in
		if (!$this->dns_auth->logged_in()) {
            redirect('auth/login');
        }

        //set and get logged in user data

        $data = new stdClass();

        $data->the_user = $this->user_data();
        $this->the_user = $data->the_user;
        $this->load->vars($data);

        //set breadcrumb
        $this->set_breadcrumb();

        //set validation error style
        $this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');

        //set date time
        $this->current_datetime = date("Y-m-d H:i:s");

    }

    private function user_data()
    {
        $user_data = array();

        if ($this->dns_auth->is_admin()) {
            $user_data = $this->dns_auth->user();
        } else {
            $user_data = $this->dns_auth->user();
        }

        return $user_data;
    }

    protected function render($the_view = null, $template = 'master_template')
    {
        if ($template == 'json' || $this->input->is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode($this->data);
        } elseif (is_null($template)) {
            $this->load->view($the_view, $this->data);
        } else {
            $default_base = 'templates/' . $template;

            $this->data['content'] = (is_null($the_view)) ? '' : $this->load->view($the_view,$this->data, TRUE);

            $this->load->view($default_base, $this->data);
        }
    }

    protected function set_breadcrumb()
    {
        //breadcrumb

        $this->dashboard_breadcrumb = array(
            'Utama' => site_url('dashboard')
        );
    }

}

class Admin_Controller extends MY_Controller
{
    function __construct()
    {
        parent::__construct();

        //only admin allow accessed

        if (!$this->dns_auth->is_admin()) {
            $this->session->set_flashdata('error', 'Hanya Admin di benarkan akses');
            redirect('dashboard');
        }
    }
}

class Public_Controller extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }
}
