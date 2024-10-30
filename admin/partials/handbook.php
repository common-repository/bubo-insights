<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php $bubo_insights_page = "handbook";  ?>

<main id="handbook" class="bubo_insights_admin_page" data-ajax="<?php echo esc_url( get_site_url() ); ?>/wp-admin/admin-ajax.php" data-nonce="<?php echo esc_attr( wp_create_nonce( 'bubo_insights_handbook' ) ); ?>">
    
	<div id="header">
        
        <nav id="bubo_insights_navbar">
            <?php do_action('bubo_insights_navbar', $bubo_insights_page ); ?>
        </nav>
		
		<h1>Bubo Insights Handbook</h1>
		<h2>This plugin is super cool, did you know?</h2>
		<i>In case you didn't, here you can find everything you need to know! ;)</i>
		
	</div>
	
	<div class="tab">
	  <button class="tablinks" onclick="bubo_insights_openTab(event, 'features')">Features</button>
	  <button class="tablinks" onclick="bubo_insights_openTab(event, 'setup')">Setup</button>
	  <button style="display:none;" class="tablinks" onclick="bubo_insights_openTab(event, 'tutorial')">Tutorial</button>
	  <button class="tablinks" onclick="bubo_insights_openTab(event, 'faqs')">FAQs</button>
	</div>
	
	<section id="features" class="tabcontent" >
		<details open>
			<summary>Top Features</summary>
			<p><ul>
                <li>Tracks Visitors, Page Visits, Referrers and Outgoing Clicks</li>
                <li>Easy to install even with no coding or technical knowledge</li>
                <li>Embedded in your WP website admin dashboard, no need to visit another website</li>
                <li>Data is self hosted and stored in your WP website database</li>
                <li>Simple, clean and responsive analytics dashboard</li>
                <li>No cookie banner needed as it is cookieless</li>
                <li>Privacy Friendly: doesn’t store user personal information</li>
			</ul></p>
		</details>
		<details>
			<summary>Simple yet Detailed</summary>
			<p>Is your website doing well? Are (real) people visiting it? Are you getting conversions?</p>
            <p>To know all of that you had to face hard times placing custom everywhere in your website, consult tutorials, deal with confusing metrics, set cookie banners and getting headaches… !</p>
            <p>With Bubo Insights you just press Install and you are ready to know what people do on your website without leading their privacy.</p>
            <p><b>Visitors' devices, Page Visits, Referrers, Outgoing Clicks</b> are the core metrics everyone should know about their website without too much hassle. That's why you should install Bubo Insights right now!</p>
		</details>
		<details>
			<summary>Responsive and Clean Dashboard</summary>
			<p>The dashboard is designed to give quick access to the most important information, no matter if you are on desktop or on a mobile device.</p>
		</details>
		<details>
			<summary>Self Hosted, No External Accounts, Unlimited Tracking</summary>
			<p>What people do on your website should be stored in your website and not on a third party cloud space, don’t you agree?</p>
            <p>Bubo Insights stores all the data collected in your Wordpress database.</p>
            <p>Only logged in editors (not just subscribers) of YOUR website can access them through the plugin’s dashboard.</p>
            <p>There are no imposed limits to the amount of visits or visitors that can be recorded, but please notice that the Wordpress database is not designed to hold several billions of entries without lagging a bit.</p>
		</details>
		<details>
			<summary>Compliant with GDPR and Privacy Laws by Design</summary>
			<p>100% data ownership. Data is entirely created and stored on your server.<br>The only sensible data of the user that is being initially collected is the user IP address.</p>
			<p>The IP address is merged with the User Agent into a string “(IP+UA)” and then hashed with a md5 hashing algorithm to generate a unique and anonymous ID.<br>This plugin stores this unique anonymous user ID and completely discards the IP address.<br>There is no way to trace back the user not even with quantistic computing.<br>Since no personal data is stored this plugin is compliant by design with any Privacy Law of present and future.</p>
			<p>In case you need a proof of the data collection, you can easily export ALL the database tables with ALL the data collected by this plugin in the plugin’s settings page.<br>Moreover, this plugin doesn’t use cookies or third party cookies to store information about the user.</p>
		</details>
	</section>
	
	<section id="setup" class="tabcontent" >
		<details open>
			<summary>Installation</summary>
			<p>To easily install Bubo Insights enter the WordPress dashboard and select **Plugins > Add New Plugin**. Search for "Bubo Insights" and install the first result you see there.<br>To install the zip file downloaded from this page:<br>
                <ol>
                    <li>Login to your WordPress dashboard</li>
                    <li>Visit the <b>Plugins > Add New Plugin</b></li>
                    <li>>Click the <b>Upload Plugin</b> button at the top</li>
                    <li>In the upload form that appears, click the <b>Choose file</b> button and select the <b>bubo-insights.zip</b> file you downloaded here</li>
                    <li>Click the <b>Install Now</b> button</li>
                    <li>Once the page reloads, click the blue <b>Activate</b> link</li>
                    <li>Purge your website cache.</li>
                </ol>
            <br>Please make sure the Date and Time are set correctly in WordPress.</p>
		</details>
	</section>
	
	<section id="tutorial" class="tabcontent" >
		<details open>
			<summary>Phase 1</summary>
			<p>Description WIP</p>
		</details>
		<details>
			<summary>Phase 2</summary>
			<p>Description WIP</p>
		</details>
		<details>
			<summary>Phase 3</summary>
			<p>Description WIP</p>
		</details>
	</section>
	
	<section id="faqs" class="tabcontent" >
		<details open>
			<summary>Is Bubo Insights free?</summary>
			<p>Yes! Bubo Insights’ core features are free.</p>
		</details>
		<details>
			<summary>Does it use Google Analytics?</summary>
			<p>No, Bubo Insights is an alternative to Google Analytics.</p>
		</details>
		<details>
			<summary>Can I use Bubo Insights and Google Analytics at the same time?</summary>
			<p>Yes, you can run them both at the same time without any problems.</p>
		</details>
		<details>
			<summary>Do I need an account?</summary>
			<p>No, you don’t need an account. No data is sent to another website.</p>
		</details>
		<details>
			<summary>Is technical knowledge required to operate Bubo Insights?</summary>
			<p>No, Bubo Insights focuses on simplicity. You don’t need any coding skills to use it. Right from your WordPress dashboard, you can install and use the plugin.</p>
		</details>
		<details>
			<summary>Does tracking start right away?</summary>
			<p>Yes, the moment you install Bubo Insights it will start tracking views. If you don't see any views right away, clear your site's cache and then visit your site in a private browser tab to record your first view. </p>
		</details>
		<details>
			<summary>What metrics does it track?</summary>
			<p>It tracks Visitors, Visits, Referrers, Outbound link clicks.</p>
		</details>
		<details>
			<summary>Do I need to use a cookie popup with it?</summary>
			<p>No, Bubo Insights does not use cookies.</p>
		</details>
		<details>
			<summary>Is there a tracking code?</summary>
			<p>Yes, but you don't have to add it yourself. It gets included on all of your site's pages automatically once Bubo Insights is activated.</p>
		</details>
		<details>
			<summary>Will Bubo Insights affect my site’s performance?</summary>
			<p>No, the tracking script is less than 3kb and it is embedded on all the website pages. So the difference in your site's performance after installing Bubo Insights will be virtually zero.</p>
		</details>
		<details>
			<summary>Are bot visits counted?</summary>
			<p>We filter out bot visits using best practice techniques.</p>
		</details>
		<details>
			<summary>Are logged-in users visits tracked?</summary>
			<p>Yes, all users visits are tracked. You can easily filter them out when you checking the website traffic analytics.</p>
		</details>
		<details>
			<summary>Is there a limit to the number of visitors I can track</summary>
			<p>No, there is no limit. The only limiting factor is your own database and server.</p>
		</details>
		<details>
			<summary>Where is the data stored?</summary>
			<p>The data is stored in your own WordPress database.</p>
		</details>
		<details>
			<summary>Can I export data?</summary>
			<p>Data can be exported to CSV files.</p>
		</details>
		<details>
			<summary>Can I give feedback about the plugin?</summary>
			<p>We value your feedback. You can submit a support request on the WordPress forums, and we will respond promptly.</p>
		</details>

	</section>
	
</main>