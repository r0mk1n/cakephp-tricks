<?php
	if ( !empty( $filter ) ):
		$unique_filters = array_unique( array_values( $filter ) );
		$is_filtered = false;
		foreach ( $unique_filters as $unique_val ) {
			if ( $unique_val != '' && $unique_val != 'all' ) {
				$is_filtered = true;
				break;
			}
		}
?>
<?php if ( $is_filtered ): ?>
    <div class="info-message">Data filtration now is turned on.</div>
<?php endif; ?>
<?php endif; ?>
