<?php

namespace Cbx\Phpspreadsheet;

class PDUpdater
{
	private $file;
	private $plugin;
	private $basename;
	private $active;
	private $username;
	private $repository;
	private $authorize_token;
	private $github_response;

	public function __construct($file)
	{
		$this->file = $file;
		add_action('admin_init', [$this, 'set_plugin_properties']);

		return $this;
	}//end function __construct

	public function set_plugin_properties()
	{
		$this->plugin = get_plugin_data($this->file);
		$this->basename = plugin_basename($this->file);
		$this->active = is_plugin_active($this->basename);
	}//end function set_plugin_properties

	public function set_username($username)
	{
		$this->username = $username;
	}//end function set_username

	public function set_repository($repository)
	{
		$this->repository = $repository;
	}//end function set_repository

	public function authorize($token)
	{
		$this->authorize_token = $token;
	}//end function authorize

	public function initialize()
	{
		add_filter('pre_set_site_transient_update_plugins', [$this, 'modify_transient'], 10, 1);
		add_filter('plugins_api', [$this, 'plugin_popup'], 10, 3);
		add_filter('upgrader_post_install', [$this, 'after_install'], 10, 3);
		add_filter("http_request_args", [$this, "addHeaders"], 10, 3);
	}//end function get_repository_info

	public function modify_transient($transient)
	{
		if (property_exists($transient, 'checked')) {
			if ($checked = $transient->checked) {
				$this->get_repository_info();
				if (isset($this->github_response['tag_name'])) {
					$tag_name = str_replace("v", "", $this->github_response['tag_name']);
					$tag_name = str_replace("V", "", $tag_name);
					$out_of_date = version_compare($tag_name, $checked[$this->basename], 'gt');

					if ($out_of_date) {
						$new_files = $this->github_response['zipball_url'];
						$slug = current(explode('/', $this->basename));

						$plugin = [
							'url' => $this->plugin['PluginURI'],
							'slug' => $slug,
							'package' => $new_files,
							'new_version' => $tag_name
						];
						// print_r($plugin);
						// exit;
						$transient->response[$this->basename] = (object) $plugin;
					}
				}

			}
		}

		return $transient;
	}//end function initialize

	private function get_repository_info()
	{
		if (is_null($this->github_response)) {
			$request_uri = sprintf('https://api.github.com/repos/%s/%s/releases', $this->username, $this->repository);

			// Switch to HTTP Basic Authentication for GitHub API v3
			$curl = curl_init();

			curl_setopt_array($curl, [
				CURLOPT_URL => $request_uri,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => [
					"Authorization: token " . $this->authorize_token,
					"User-Agent: PDUpdater/1.2.3"
				]
			]);

			$response = curl_exec($curl);

			curl_close($curl);

			$response = json_decode($response, true);


			if (is_array($response)) {
				$response = current($response);
			}

			if ($this->authorize_token && isset($response['zipball_url'])) {
				$response['zipball_url'] = add_query_arg(
					'access_token',
					$this->authorize_token,
					$response['zipball_url']
				);
				$this->github_response = $response;
			}
		}
	}//end function modify_transient

	public function plugin_popup($result, $action, $args)
	{
		if ($action !== 'plugin_information') {
			return false;
		}

		if (!empty($args->slug)) {
			if ($args->slug == current(explode('/', $this->basename))) {
				$this->get_repository_info();

				if (isset($this->github_response['zipball_url'])) {
					$slug = current(explode('/', $this->basename));

					$tag_name = str_replace("v", "", $this->github_response['tag_name']);
					$tag_name = str_replace("V", "", $tag_name);

					$plugin = [
						'name' => isset($this->plugin['Name']) ? $this->plugin['Name'] : '',
						'slug' => $slug,
						'requires' => '5.3',
						'tested' => '5.4',
						'version' => $tag_name,
						'author' => $this->plugin['Author'],
						'author_profile' => $this->plugin['AuthorURI'],
						'last_updated' => $this->github_response['published_at'],
						'homepage' => $this->plugin['PluginURI'],
						'short_description' => isset($this->plugin['Description']) ? $this->plugin['Description'] : '',
						'sections' => [
							'Description' => isset($this->plugin['Description']) ? $this->plugin['Description'] : '',
							'Updates' => isset($this->github_response['body']) ? $this->github_response['body'] : '',
						],
						'download_link' => $this->github_response['zipball_url']
					];

					return (object) $plugin;
				}

			}
		}

		return $result;
	}//end function plugin_popup

	/**
	 * Take care on after plugin install
	 *
	 * @param $response
	 * @param $hook_extra
	 * @param $result
	 *
	 * @return mixed
	 */
	public function after_install($response, $hook_extra, $result)
	{
		global $wp_filesystem;

		$install_directory = plugin_dir_path($this->file);
		$wp_filesystem->move($result['destination'], $install_directory);
		$result['destination'] = $install_directory;

		if ($this->active) {
			activate_plugin($this->basename);
		}

		return $result;
	}//end function after_install

	/**
	 * Add github authorization token for plugin download from github
	 *
	 * @param $parsed_args
	 * @param $url
	 *
	 * @return mixed
	 */
	public function addHeaders($parsed_args, $url)
	{
		if (empty($parsed_args['headers'])) {
			$parsed_args['headers'] = [];
		}

		if (strpos($url, "https://api.github.com/repos/{$this->username}/{$this->repository}") !== false) {
			$parsed_args['headers']['Authorization'] = "token $this->authorize_token";
		}

		return $parsed_args;
	}//end function addHeaders
}//end class PDUpdater