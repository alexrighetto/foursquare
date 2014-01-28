<?php
ob_start();
require_once 'EpiCurl.php';
require_once 'EpiFoursquare.php';
$clientId = 'Y0JUIKDOCHBLE2Y20V5VBZIGUXYOUV3FY0C5PJUXQZCOVRHK';
$clientSecret = 'N4I0KVPXXR1XOPXDR4NT2IT3XMJBBMVZAKSKK03G5KZ4D42R';
$code = '2AC0MXFYJ2WNN3RHUJ53RAXPXVUSOEX4R3NPAYOQLJKO0KLT';
$accessToken = '2BWXHYOYG0DXU0MSJE1CLXZU2BPOJGCT0T1DPMMLA3LBHS4M';
$redirectUri = 'http://www.valdalpone.it/checkins';
$userId = '14086939';
$fsObj = new EpiFoursquare($clientId, $clientSecret, $accessToken);
$fsObjUnAuth = new EpiFoursquare($clientId, $clientSecret);
?>
<script type="text/javascript">
function viewSource() {
	document.getElementById("source").style.display = "block";
}
</script>

<h1>Simple test to make sure everything works ok</h1>

<h2><a href="javascript:void(0);" onclick="viewSource();">View the source of this file</a></h2>

<div id="source" style="display:none; padding:5px; border: dotted 1px #bbb; background-color:#ddd;">
<?php highlight_file(__FILE__); ?>
</div>

<hr>

<h2>Test an unauthenticated call to search for a venue</h2>
<?php $venue = $fsObjUnAuth->get('/venues/search', array('ll' => '40.7,-74')); ?>
<pre><?php var_dump($venue->response->groups[0]->items[0]); ?></pre>

<hr>

<?php if(!isset($_GET['code']) && !isset($_COOKIE['access_token'])) { ?>
<h2>Generate the authorization link</h2>
<?php $authorizeUrl = $fsObjUnAuth->getAuthorizeUrl($redirectUri); ?>
<a href="<?php echo $authorizeUrl; ?>"><?php echo $authorizeUrl; ?></a>

<?php } else { ?>
	<h2>Display your own badges</h2>
	<?php
	if(!isset($_COOKIE['access_token'])) {
		$token = $fsObjUnAuth->getAccessToken($_GET['code'], $redirectUri);
		setcookie('access_token', $token->access_token);
		$_COOKIE['access_token'] = $token->access_token;
	}
	$fsObjUnAuth->setAccessToken($_COOKIE['access_token']);
	$badges = $fsObjUnAuth->get('/users/self/badges');

	// Process the returned object and display the badge images					
	if (is_object($badges->response)) {
		foreach ($badges->response->badges as $badge) {		
			echo "<img src=\"".$badge->image->prefix.$badge->image->sizes->{1}.$badge->image->name."\" title=\"".$badge->name."\" />";
		}
	}
	?>
	<div style="height: 400px; overflow: auto; width: 100%; border: 2px solid #ccc;">
		<pre><?php var_dump($badges->response); ?></pre>
	</div>
<?php } ?>

<hr>

<h2>Get a test user's checkins</h2>
<?php
$creds = $fsObj->get("/users/{$userId}/checkins");
?>
<div style="height: 400px; overflow: auto; width: 100%; border: 2px solid #ccc;">
	<pre>
		<?php var_dump($creds->response); ?>
	</pre>
</div>