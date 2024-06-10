<?php

$files = maybe_unserialize(get_post_meta($post->ID, '__wpdm_files', true));

if (!is_array($files)) $files = array();

include __DIR__.'/attach-file/upload-file.php';
if(!defined("WPDM_DISABLE_REMOTE_URL_ATTACHMENT") || WPDM_DISABLE_REMOTE_URL_ATTACHMENT === false)
	include __DIR__.'/attach-file/remote-url.php';
if(!defined("WPDM_DISABLE_MEDIA_ATTACHMENT") || WPDM_DISABLE_MEDIA_ATTACHMENT === false)
	include __DIR__.'/attach-file/media-library-file.php';
if(!defined("WPDM_DISABLE_SERVER_FILE_ATTACHMENT") || WPDM_DISABLE_SERVER_FILE_ATTACHMENT === false)
	include __DIR__.'/attach-file/server-file.php';

do_action("wpdm_attach_file_metabox");
