
<?php
class shieldsquare_config
{
	//PHP connector v4.4


	/*
	 *  Enter your Subscriber id  .
	 */
	public $_sid = "cb63167e-f47a-4a60-9939-d0932001a280";

	/*

	 * Please specify the mode in which you want to operate
	 * 
	 * public $_mode = "Active";
	 * or
	 * public $_mode = "Monitor";
 	 */
	public $_mode = "Monitor";


    /*
     * Asynchronous HTTP Data Post  

     * Setting this value to true will reduce the page load time when you are in Monitor mode. 
     * This uses Linux CURL to POST the HTTP data using the EXEC command. 
     * Note: Enable this only if you are hosting your applications on Linux environments.   

     */
	public $_async_http_post = true;
		
	
	/*
	 * Curl Timeout in Milliseconds
	 */
	public $_timeout_value = 100;

	/*
	 * PHPSESSID is the default session ID for PHP, please change it if needed
	 */
	public $_sessid = 'PHPSESSID';

	/*
	 * Change this value if your servers are behind a firewall or proxy
	 */
	public $_ipaddress = 'REMOTE_ADDR';

	/*
	 * Enter the relative URL of the JavaScript Data Collector
	 * 
	 * $_js_url parameter is only used for backward compatibility. Kindly ignore.
	 */
	public $_js_url = '/getData.php';

	/*
	 * Set the ShieldSquare domain based on your Server Locations
  	 *    Asia/India     -  'ss_sa.shieldsquare.net'
	 *    Europe         -  'ss_ew.shieldsquare.net'
     *    Australia      -  'ss_au.shieldsquare.net'
     *    South America  -  'ss_br.shieldsquare.net'
     *    North America  -  'ss_scus.shieldsquare.net'
     */
	public $_ss2_domain = 'ss_sa.shieldsquare.net';
	
	/*
	 * Set the DNS cache time in seconds
	 * Default is one hour
	 * Set -1 to disable caching
	 * Note: To use this feature your application server [Apache/Nginx] 
	 * should have write access to folder specified in $_domain_cache_file. 
	 */
	public $_domain_ttl = 3600;

	/*
	 * Set DNS Cache file path
	 * Default is /tmp/ folder.
	 * Note: To use this feature your application server [Apache/Nginx] 
	 * should have write access to folder specified. 
	 * Also add '/' in the end of the path 
	 * eg. /tmp/
	 */
	public $_domain_cache_file = '/tmp/';

}
?>

