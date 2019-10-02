<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Flash Message Library
 *
 * @category   Session
 * @package    CodeIgniter
 * @subpackage Libraries
 * @author     Edward Mann <the@eddmann.com>
 * @release    1.0.0
 * @license    MIT License Copyright (c) 2012 Edward Mann
 * @link       http://eddmann.com/
 */
class Flash {

	/**
	 * The current CodeIgniter instance.
	 *
	 * @var object
	 * @access private
	 */
	private $_ci;

	/**
	 * The messages retrieved from the session.
	 *
	 * @var array
	 * @access private
	 */
	private $_session_messages = array();

	/**
	 * The messages stored for displaying on this request.
	 *
	 * @var array
	 * @access private
	 */
	private $_messages = array();

	/**
	 * The permitted config options for alteration.
	 *
	 * @var array
	 * @access private
	 */
	private $_config_whitelist = array(
		'session_name', 'default_style', 'styles', 'split_default', 
		'merge_form_errors', 'form_error_message'
	);

	/**
	 * The session name used for flashdata.
	 *
	 * @var string
	 * @access public
	 */
	public $session_name = 'flash';

	/**
	 * The default styling used if none specified.
	 *
	 * @var array
	 * @access public
	 */
	public $default_style = array('<div>', '</div>');

	/**
	 * The different styles used for specific message types.
	 *
	 * @var array
	 * @access public
	 */
	public $styles = array();

	/**
	 * Split the displayed messages by default.
	 *
	 * @var boolean
	 * @access public
	 */
	public $split_default = FALSE;

	/**
	 * Merge form validation errors with error messages.
	 *
	 * @var boolean
	 * @access public
	 */
	public $merge_form_errors = TRUE;

	/**
	 * Optionally display only a single form error message.
	 *
	 * @var string
	 * @access public
	 */
	public $form_error_message = NULL;

	/**
	 * Constructer
	 *
	 * Retrieves the CI instance in use and then attempts to
	 * override the config options based on the config file, then
	 * passed in options.
	 *
	 * @param array $config Any config options to override
	 * 
	 * @access public
	 */
	public function __construct(array $config = array())
	{
		$this->_ci =& get_instance();

		$this->_ci->load->library('session');

		if (is_array(config_item('flash')))
			$config = array_merge(config_item('flash'), $config);

		if ( ! empty($config))
			$this->_initialize($config);
	}

	/**
	 * Initalize Class
	 *
	 * Overrides the whitelisted default config options with the
	 * ones passed in as a param.
	 *
	 * @param array $config Any config options to override
	 *
	 * @return void
	 * @access private
	 */
	private function _initialize(array $config = array())
	{
		foreach ($config as $config_key => $config_value) {
			if (in_array($config_key, $this->_config_whitelist)) {
				$this->{$config_key} = $config_value;
			}
		}
	}

	/**
	 * Add Message
	 *
	 * Adds a message to the specified types array - you have
	 * the option to display the message on this request, else it
	 * will be stored in flashdata for the next one.
	 *
	 * @param mixed  $message     The message to be added
	 * @param mixed  $data        Array or string to be used to format the message
	 * @param string $type        The type of message being added
	 * @param bool   $display_now Display the message on this request or the next
	 *
	 * @throws Exception If none scalar message or none string type entered
	 * @return object $this
	 * @access private
	 */
	private function _add_message($message, $data = '', $type = 'default', $display_now = FALSE)
	{
		// all messages must be scalar types (int, float, string or boolean)
		// and the type must be a string, if either invalid an exception is raised
		if ( ! is_scalar($message) OR ! is_string($type))
			throw new Exception('Invalid message type/value entered.');

		// apply formatting based on type
		$message = (is_array($data)) ?
			vsprintf($message, $data) : sprintf($message, $data);

		if ($display_now === FALSE) {
			$this->_session_messages[$type][] = $message;
			$this->_ci->session->set_flashdata($this->session_name, $this->_session_messages);
		}
		else {
			$this->_messages[$type][] = $message;
		}

		return $this;
	}

	public function remove()
	{
		$this->_ci->session->set_flashdata($this->session_name, NULL);
	}

	/**
	 * Get Messages
	 *
	 * Returns the specifed type of messages as an array, else returns all available
	 * messages. Optionally allows you to return single types of messages as an associative
	 * array, which is internally used for displaying.
	 * 
	 * @param string  $type            the message type to return, or empty for all
	 * @param boolean $single_as_assoc return single types as an associative array
	 * 
	 * @return array The specifed types messages or empty array
	 * @access public
	 */
	public function get($type = '', $single_as_assoc = FALSE)
	{
		$session_messages = $this->_ci->session->flashdata($this->session_name);
		$messages         = $this->_messages;

		// sets the session message to an array if not already the case
		if ( ! is_array($session_messages)) {
			$session_messages = array();
		}

		// attempt to display form errors if no type or form/error types passed in
		if ($type === '' OR $type === 'form' OR ($this->merge_form_errors AND $type === 'error')) {
			$this->_ci->load->library('form_validation');

			// check to see if any form validation errors are present
			if ($errors = trim(validation_errors(' ', '|'))) {
				if ($this->form_error_message !== NULL) {
					// create single item array with error message
					$form_errors = array($this->form_error_message);
				}
				else {
					// create array from validation errors string
					$form_errors = explode('|', substr($errors, 0, -1));
				}

				// merge into errors array if configured to
				if ($this->merge_form_errors AND ($type === '' OR $type === 'error')) {
					if ( ! isset($messages['error']))
						$messages['error'] = array();

					$messages['error'] = array_merge($messages['error'], $form_errors);
				}
				else {
					if ( ! isset($messages['form']))
						$messages['form'] = array();

					$messages['form'] = array_merge($messages['form'], $form_errors);
				}
			}
		}

		// set the messages to a specific type if option present, else set to empty array
		if ($type !== '') {
			if (isset($session_messages[$type])) {
				// create associative array with type if desired
				$session_messages = ($single_as_assoc) ?
					array($type => $session_messages[$type]) : $session_messages[$type];
			}
			else {
				$session_messages = array();
			}

			if (isset($messages[$type])) {
				// create associative array with type if desired
				$messages = ($single_as_assoc) ?
					array($type => $messages[$type]) : $messages[$type];
			}
			else {
				$messages = array();
			}
		}

		// merge session messages into current requests array
		$messages = array_merge_recursive($session_messages, $messages);

		return $messages;
	}

	/**
	 * Display Messages
	 * 
	 * Returns the HTML to display the specified type in either split or joined 
	 * message format. If no type specified all types are returned. 
	 * If 'form' is passed in as the type the form validation class is used 
	 * to retrieve the errors.
	 *
	 * @param string  $type  The message type to display
	 * @param boolean $split Display messages split or joined
	 * 
	 * @return string The message HTML
	 * @access public
	 */
	public function display($type = '', $split = NULL)
	{
		// set split option to default if no option passed in
		if ($split === NULL)
			$split = $this->split_default;

		// returns an associative array with specified messages in
		$messages = $this->get($type, TRUE);

		$output = '';

		if ( ! empty($messages)) {
			// loop through all message types if array not empty
			foreach ($messages as $type => $messages) {
				// set the selected style based on type or use default
				$selected_style = (isset($this->styles[$type])) ?
					$this->styles[$type] : $this->default_style;

				// output beginning style if split is false
				if ( ! $split)
					$output .= $selected_style[0] . '<ul>';

				foreach ($messages as $message) {
					// output full message style with message if split is true
					if ($split) {
						$output .= $selected_style[0] . $message . $selected_style[1];
					}
					// output as a list element if not
					else {
						$output .= '<li>' . $message . '</li>';
					}
				}
				
				// output ending style if split is false
				if ( ! $split)
					$output .= '</ul>' . $selected_style[1];
			}
		}

		return $output;
	}

	/**
	 * Call (Magic Method)
	 *
	 * Used to allow the user to call the class with a message type as the function name.
	 * When called it internally invokes the private add message function.
	 *
	 * @param string $name      The message type name
	 * @param array  $arguments The arguments passed into the method
	 *
	 * @throws BadMethodCallException If no arguments are passed in
	 * @return object $this
	 * @access public
	 */
	public function __call($name, $arguments)
	{
		if ( ! empty($arguments)) {
			// set display status based on function call name and set message
			$name    = preg_replace('/_now$/', '', $name, 1, $display_now);
			$message = $arguments[0];
			$data = (isset($arguments[1])) ? $arguments[1] : '';

			// call the private add message method with provided arguments
			return $this->_add_message($message, $data, $name, (bool)$display_now);
		}
		// throw a bad method exception if no arguments passed
		else {
			throw new BadMethodCallException();
		}
	}

	/**
	 * Get (Magic Method)
	 *
	 * Used to allow the user to call the class with a message type as the property name.
	 * When called it internally invokes the get function.
	 * 
	 * @param string $name The type of messages to return
	 * 
	 * @return array The specifed types messages or empty array
	 * @access public
	 */
	public function __get($name)
	{
		return $this->get($name);
	}

}


/* End of file flash.php */
/* Location: ./sparks/flash/1.0.0/libraries/flash.php */
