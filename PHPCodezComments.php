<?php
/**
* Plugin Name: PHPCodez Comments
* Plugin URI: http://phpcodez.com/
* Description: A Widget That Displays Comments
* Version: 0.1
* Author: Pramod T P
* Author URI: http://phpcodez.com/
*/

add_action( 'widgets_init', 'wpc_comments_widgets' );

function wpc_comments_widgets() {
	register_widget( 'wpccommentsWidget' );
}

class wpccommentsWidget extends WP_Widget {
	function wpccommentsWidget() {
		$widget_ops = array( 'classname' => 'wpcClass', 'description' => __('A Widget That Displays comments.', 'wpcClass') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'wpc-comments' );
		$this->WP_Widget( 'wpc-comments', __('PHPCodez comments', ''), $widget_ops, $control_ops );
	}

	
	function widget( $args, $instance ) {
		extract( $args );
		global $wpdb;
		if($instance['comment_count']) $commentLimit =" LIMIT 0,".$instance['comment_count'];
		
		if($instance['comment_approved']) $commentApproved =" AND  comment_approved='1'";
		
		if($instance['comments_random'])
				$commentOrderBy =" ORDER BY rand()";
		elseif($instance['comment_order'] 	)		
			$commentOrderBy =" ORDER BY comment_ID	".$instance['comment_order'];
		if($instance['comment_exclude']) $commentExlucde .=" AND  comment_ID NOT IN(".$instance['comment_exclude'].")  ";
		
		if($instance['show_posts'] or $instance['show_author'] or $instance['show_comment']  ) {
	    	$commentQuery = "SELECT * from $wpdb->comments WHERE 1  $commentApproved  $commentExlucde $commentOrderBy $commentLimit";
		    $comments = $wpdb->get_results($commentQuery);
		}	
		
?>
	<div class="arch_box">
		<?php if($instance['comment_title']) { ?>
		<div class="side_hd">
			<h2><?php echo $instance['comment_title'] ?></h2>
		</div>
	<?php } ?>
		<div class="sider_mid">
			<ul>
				<?php foreach ($comments as $comment) {$url = '<a href="'. get_permalink($comment->comment_post_ID).'#comment-'.$comment->comment_ID .'">';?>
					<li>
						<?php if($instance['showAuthorImage']) {?>
							<?php 
								if($instance['authorImageWidth']){
									$dimession	=	$instance['authorImageWidth']; 
								}elseif($instance['authorImageHeight']){ 
									$dimession	=	$instance['authorImageHeight']; 
								}
							?>
							<?php echo get_avatar($comment->user_id,$dimession); ?>
						<?php } ?>
						<?php if($instance['show_author']){ ?>
							<a href="<?php $comment->comment_author_url; ?>">
								<?php echo empty($instance['cauthor_count'])?$comment->comment_author:substr($comment->comment_author,0,$instance['cauthor_count']); ?>
							 </a>
							<?php } ?>
						<?php if($instance['show_posts']){ ?>On<?php echo $url; ?>
							<?php echo empty($instance['cpost_count'])?get_the_title($comment->comment_post_ID):substr(get_the_title($comment->comment_post_ID),0,$instance['cpost_count']); ?>
							</a>
						<?php } ?>
						<?php if($instance['show_comment']){ ?><br /><?php echo $url; ?>
							<?php echo  empty($instance['cmtext_count'])?$comment->comment_content:substr($comment->comment_content,0,$instance['cmtext_count']); ?></a>
						<?php } ?>
					</li>
				<?php $haveComments=1; } ?>
				<?php if(!$haveComments){ ?>
					<li>No comments Are Added Yet</li>
				<?php } ?>
			</ul>	
		</div>	
	</div>
<?php

}


function update( $new_instance, $old_instance ) {
	$instance = $old_instance;
	
	$instance['comment_title']		=  $new_instance['comment_title'] ;
	$instance['comments_random']	=  $new_instance['comments_random'] ;
	$instance['comment_count'] 		=  $new_instance['comment_count'] ;
	$instance['show_posts']	 		=  $new_instance['show_posts'] ;
	$instance['show_author']	 	=  $new_instance['show_author'] ;
	$instance['comment_order'] 		=  $new_instance['comment_order'] ;
	$instance['comment_exclude'] 	=  $new_instance['comment_exclude'] ;
	$instance['show_comment'] 		=  $new_instance['show_comment'] ;
	
	$instance['cpost_count'] 		=  $new_instance['cpost_count'] ;
	$instance['cauthor_count'] 		=  $new_instance['cauthor_count'] ;
	$instance['cmtext_count'] 		=  $new_instance['cmtext_count'] ;
	
	$instance['showAuthorImage'] 	=  $new_instance['showAuthorImage'] ;
	$instance['authorImageWidth']	=  $new_instance['authorImageWidth'] ;
	$instance['authorImageHeight'] 	=  $new_instance['authorImageHeight'] ;
	
	$instance['comment_approved'] 	=  $new_instance['comment_approved'] ;
	return $instance;
}

function form( $instance ) {?>
	<p>
		<label for="<?php echo $this->get_field_id( 'comment_title' ); ?>"><?php _e('Title', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'comment_title' ); ?>" name="<?php echo $this->get_field_name( 'comment_title' ); ?>" value="<?php echo $instance['comment_title'] ?>"  type="text" style="width:80%" />
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id( 'comments_random' ); ?>"><?php _e('Show Random Comments', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'comments_random' ); ?>" name="<?php echo $this->get_field_name( 'comments_random' ); ?>" value="1" <?php if($instance['comments_random']) echo 'checked="checked"'; ?> type="checkbox" />
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_name( 'comment_order' ); ?>"><?php _e('Order BY', 'wpclass'); ?></label>
		<select id="<?php echo $this->get_field_name( 'comment_order' ); ?>" name="<?php echo $this->get_field_name( 'comment_order' ); ?>">
			<option value="ASC" <?php if($instance['comment_order']=="ASC") echo 'selected="selected"'; ?>>ASC</option>
			<option value="DESC" <?php if($instance['comment_order']=="DESC") echo 'selected="selected"'; ?>>DESC</option>
		</select>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'comment_count' ); ?>"><?php _e('Number of comments . for "0" or "No Value" It will list all the comments', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'comment_count' ); ?>" name="<?php echo $this->get_field_name( 'comment_count' ); ?>" value="<?php echo $instance['comment_count'] ?>"  type="text" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'comment_exclude' ); ?>"><?php _e('Exclude Comments - Enter post ids to be excluded (example 5,78,90)', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'comment_exclude' ); ?>" name="<?php echo $this->get_field_name( 'comment_exclude' ); ?>" value="<?php echo $instance['comment_exclude'] ?>"  type="text" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'show_posts' ); ?>"><?php _e('Display Post Name', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'show_posts' ); ?>" name="<?php echo $this->get_field_name( 'show_posts' ); ?>" value="1" <?php if($instance['show_posts']) echo 'checked="checked"'; ?> type="checkbox" />
		<label for="<?php echo $this->get_field_id( 'cpost_count' ); ?>"><?php _e('Limit Text', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'cpost_count' ); ?>" name="<?php echo $this->get_field_name( 'cpost_count' ); ?>" value="<?php echo $instance['cpost_count'] ?>"  type="text" style="width:60px" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'show_author' ); ?>"><?php _e('Display Author Name', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'show_author' ); ?>" name="<?php echo $this->get_field_name( 'show_author' ); ?>" value="1" <?php if($instance['show_author']) echo 'checked="checked"'; ?> type="checkbox" />
		<label for="<?php echo $this->get_field_id( 'cauthor_count' ); ?>"><?php _e('Limit Text', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'cauthor_count' ); ?>" name="<?php echo $this->get_field_name( 'cauthor_count' ); ?>" value="<?php echo $instance['cauthor_count'] ?>"  type="text"  style="width:60px"/>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'show_comment' ); ?>"><?php _e('Display Comments', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'show_comment' ); ?>" name="<?php echo $this->get_field_name( 'show_comment' ); ?>" value="1" <?php if($instance['show_comment']) echo 'checked="checked"'; ?> type="checkbox" />
		<label for="<?php echo $this->get_field_id( 'cmtext_count' ); ?>"><?php _e('Limit Text', 'wpclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'cmtext_count' ); ?>" name="<?php echo $this->get_field_name( 'cmtext_count' ); ?>" value="<?php echo $instance['cmtext_count'] ?>"  type="text" style="width:60px" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'showAuthorImage' ); ?>"><?php _e('Show Author Image', 'wpcclass'); ?></label>
		<input type="checkbox" value="1" id="<?php echo $this->get_field_id( 'showAuthorImage' ); ?>" name="<?php echo $this->get_field_name( 'showAuthorImage' ); ?>" <?php if($instance['showAuthorImage']) echo 'checked="checked"'; ?>   />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'authorImageWidth' ); ?>"><?php _e('Image Width', 'wpcclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'authorImageWidth' ); ?>" name="<?php echo $this->get_field_name( 'authorImageWidth' ); ?>" value="<?php echo $instance['authorImageWidth']; ?>" style="width:20%; border:1px solid #ccc" />
		<label for="<?php echo $this->get_field_id( 'authorImageHeight' ); ?>"><?php _e('Height', 'wpcclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'authorImageHeight' ); ?>" name="<?php echo $this->get_field_name( 'authorImageHeight' ); ?>" value="<?php echo $instance['authorImageHeight']; ?>" style="width:20%; border:1px solid #ccc" />
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id( 'comment_approved' ); ?>"><?php _e('Show Only Approved Comments', 'wpcclass'); ?></label>
		<input id="<?php echo $this->get_field_id( 'comment_approved' ); ?>" name="<?php echo $this->get_field_name( 'comment_approved' ); ?>" value="1" <?php if($instance['comment_approved']) echo 'checked="checked"'; ?> type="checkbox" />
	</p>
<?php
	}
}

?>