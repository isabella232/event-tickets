/**
 * External dependencies
 */
import React from 'react';

/**
 * WordPress dependencies
 */
import { registerBlockType } from '@wordpress/blocks';

const { useBlockProps } = wp.blockEditor;

/**
 * Internal dependencies
 */
import { Tickets as TicketsIcon } from '@moderntribe/tickets/icons';
import {
	KEY_TICKET_HEADER,
	KEY_TICKET_CAPACITY,
	KEY_TICKET_DEFAULT_PROVIDER,
	KEY_TICKETS_LIST,
} from '@moderntribe/tickets/data/utils';
import Tickets from './container';
import Save from './save';
import { InnerBlocks } from "../../../../../../common/__mocks__/@wordpress/editor";

const block = {
	icon: <TicketsIcon/>,

	attributes: {
		sharedCapacity: {
			type: 'string',
			source: 'meta',
			meta: KEY_TICKET_CAPACITY,
		},
		header: {
			type: 'string',
			source: 'meta',
			meta: KEY_TICKET_HEADER,
		},
		provider: {
			type: 'string',
			source: 'meta',
			meta: KEY_TICKET_DEFAULT_PROVIDER,
		},
		tickets: {
			type: 'array',
			source: 'meta',
			meta: KEY_TICKETS_LIST,
		},
	},

	edit: function ( editProps ) {
		const blockProps = useBlockProps();
		return ( <div { ...blockProps }><Tickets { ...editProps }/></div> )
	},
	save: function ( saveProps ) {
		const blockProps = useBlockProps.save ();
		const currentPost = wp.data.select ( 'core/editor' ).getCurrentPost ();

		return ( <Save { ...saveProps } blockProps={ blockProps } currentPost={currentPost}/> );
	}
};

registerBlockType ( `tribe/tickets`, block );
