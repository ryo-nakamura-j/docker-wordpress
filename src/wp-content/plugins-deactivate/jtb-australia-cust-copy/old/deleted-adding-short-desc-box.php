<?php


add_action( 'add_meta_boxes', 'meta_box_add_short_desc' );
add_action( 'save_post', 'meta_box_add_short_desc_save' );



function meta_box_add_short_desc(){
    add_meta_box( 'short-description-for-list-page', __( 'Short Description for List Page', 'textdomain' ),  'meta_box_print', 'page','side','low' );
}
function meta_box_print(){
    // $post is already set, and contains an object: the WordPress post
    global $post;
    $values = get_post_custom( $post->ID );
    $text = isset( $values['short_description'] ) ? $values['short_description'] : '';
     
    // We'll use this nonce field later on when saving.
    wp_nonce_field( 'short_description_nonce', 'meta_box_nonce' );
    ?>
<label for="short_description">Short desc. for list page</label>
<!--<input type="text" name="short_description" id="short_description" />-->

<textarea type="text" name="short_description" id="short_description"  rows="10" cols="30">
<?php echo $text[0]; ?>
</textarea>

<?php
}
function meta_box_add_short_desc_save( $post_id ){
    // Bail if we're doing an auto save
    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
     
    // if our nonce isn't there, or we can't verify it, bail
    if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'short_description_nonce' ) ) return;
     
    // if our current user can't edit this post, bail
    if( !current_user_can( 'edit_post' ) ) return;
     
    // now we can actually save the data
    $allowed = array( 
        'a' => array( // on allow a tags
            'href' => array() // and those anchors can only have href attribute
        )
    );
     
    // Make sure your data is set before trying to save it
    if( isset( $_POST['short_description'] ) )
        update_post_meta( $post_id, 'short_description', wp_kses( $_POST['short_description'], $allowed ) );
}


?>
