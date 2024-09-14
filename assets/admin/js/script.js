jQuery( document ).ready( function( $ )
{
	function inputTotags()
	{
		var check_if_input_available = setInterval( function()
		{
			if ( $( '.vc_ui-panel.vc_active' ).attr( 'data-vc-shortcode' ) !== 'animated_headline_vc' )
			{
				clearInterval( check_if_input_available ); return;
			}

			if ( $( 'input[name="animation_texts"]' ).length )
			{

				$( 'input[name="animation_texts"]' ).tagsInput({
					placeholder : 'Add Animation Words',
					unique : false
				} );
				
				clearInterval( check_if_input_available );
			}

		}, 500 );

		var check_if_preview_container_available = setInterval( function()
		{
			if ( $( '.vc_ui-panel.vc_active' ).attr( 'data-vc-shortcode' ) !== 'animated_headline_vc' )
			{
				
				clearInterval( check_if_preview_container_available ); return;
			}

			if ( $( 'div.raw_html_container' ).length )
			{
				doAnimation( $( 'select[name="animation_type"]' ) );
				
				clearInterval( check_if_preview_container_available );
			}

		}, 500 );
	}

	// vc backend builder
	$( document ).on( 'click', '.animated_headline_vc_o, .vc_control-btn-edit', function( event )
	{	
		inputTotags();
	} );
	
	$( 'iframe' ).load( function()
	{	
		$( this ).contents().find( ".vc_control-btn-edit" ).on( 'click', function( event )
		{
			inputTotags();
		} );
	} );

	$( document ).on( 'change', '.animation_type', function( event )
	{
		doAnimation( $( this ) );
	} );

	function doAnimation( selector )
	{
		var animation_type = $( selector ).val();

		if ( animation_type == '' )
		{
			$( '.raw_html_container' ).empty();
			
			return;
		}

		switch ( animation_type )
		{
			case 'rotate-2':
			case 'rotate-3':
			case 'type':
			case 'scale':
				animation_type += " letters";
			break;
		}

		var title = $( '.vc_shortcode-param input[name="title"]' ).val();

		var animationTexts = '';

		$( '.tagsinput .tag' ).each( function( index, el )
		{
			var addclass = ( index == 0 ) ? "is-visible" : "";
			
			animationTexts += '<b class="'+ addclass +'">' + $( el ).find( '.tag-text' ).text() + '</b>';
		} );

		var html = '<h1 class="cd-headline '+animation_type+'"><span>' + title + '</span>';
			html += '<span class="cd-words-wrapper">';
			html +=  animationTexts;
			html += '</span></h1>';
	
		$( '.raw_html_container' ).html( html );

		initHeadline();
	}
} );