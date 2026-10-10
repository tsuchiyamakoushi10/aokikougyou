<?php
/**
 * サイト内検索フォーム。
 */

$uid = wp_unique_id( 'search-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $uid ); ?>">サイト内検索</label>
	<input type="search" id="<?php echo esc_attr( $uid ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="サイト内を検索">
	<button type="submit" aria-label="検索"><?php echo aoki_icon( 'search' ); // phpcs:ignore ?></button>
</form>
