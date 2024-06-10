<?php
namespace sgpb;
require_once(dirname(__FILE__).'/SGPopup.php');

class FblikePopup extends SGPopup
{
	public function getOptionValue($optionName, $forceDefaultValue = false)
	{
		return parent::getOptionValue($optionName, $forceDefaultValue);
	}

	public function getPopupTypeOptionsView()
	{
		return array(
			'filePath' => SG_POPUP_TYPE_OPTIONS_PATH.'facebook.php',
			'metaboxTitle' => 'Facebook Settings',
			'short_description' => 'Select your Facebook page URL for liking and sharing it'
		);
	}

	private function getScripts()
	{
		/*get WordPress localization name*/
		$locale = $this->getSiteLocale();
		ob_start();
		?>
		<script>
			jQuery(window).on('sgpbDidOpen', function (e) {
				var sgpbOldCB = window.fbAsyncInit;
				window.fbAsyncInit = function () {
					if (typeof sgpbOldCB === 'function') {
						sgpbOldCB();
					}
					FB.init({
						appId: <?php echo esc_html(SGPB_FACEBOOK_APP_ID);?>
				    });
				};
				(function(d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) return;
					js = d.createElement(s); js.id = id;
					js.src = 'https://connect.facebook.net/<?php echo $locale;?>/all.js#xfbml=1&version=v2.11&appId=<?php echo SGPB_FACEBOOK_APP_ID;?>';
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));
			});
		</script>
		<?php
		$scripts = ob_get_contents();
		ob_get_clean();

		return $scripts;
	}

	private function getButtonConfig($shareUrl, $layout, $shareButtonStatus)
	{
		ob_start();
		?>
			<div class='sg-fb-buttons-wrapper sgpb-fb-wrapper-<?php echo $layout;?>'>
				<div class="fb-like"
				     data-href="<?php echo $shareUrl; ?>"
				     data-layout="<?php echo $layout; ?>"
				     data-action="like"
				     data-size="small"
				     data-show-faces="true"
				     data-share="<?php echo $shareButtonStatus; ?>">
				</div>
			</div>
		<?php
		$buttonConfig = ob_get_contents();
		ob_get_clean();

		return $buttonConfig;
	}

	private function getFblikeContent()
	{
		$options = $this->getOptions();
		$shareUrl = $options['sgpb-fblike-like-url'];
		$layout = $options['sgpb-fblike-layout'];
		$shareButtonStatus = true;
		if (!empty($options['sgpb-fblike-dont-show-share-button'])) {
			$shareButtonStatus = false;
		}

		$scripts = $this->getScripts();
		$buttonConfig = $this->getButtonConfig($shareUrl, $layout, $shareButtonStatus);
		ob_start();
		?>
		<div id="sg-facebook-like">
			<div id="fb-root"></div>
			<?php echo $buttonConfig; ?>
			<?php echo $scripts; ?>
		</div>
		<?php
		$content = ob_get_contents();
		ob_get_clean();

		return $content;
	}

	public function getPopupTypeContent()
	{
		$fbLikeContent = $this->getFblikeContent();
		$popupContent = $this->getContent();
		$popupContent .= $fbLikeContent;

		return $popupContent;
	}

	public function getExtraRenderOptions()
	{
		return array();
	}
}
