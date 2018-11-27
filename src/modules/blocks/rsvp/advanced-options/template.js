/**
 * External dependencies
 */
import React, { Fragment, PureComponent } from 'react';
import PropTypes from 'prop-types';
import uniqid from 'uniqid';

/**
 * WordPress dependencies
 */
import { Dashicon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import RSVPDuration from '../duration/template';
import RSVPAttendeeRegistration from '../attendee-registration/container';
import { Accordion } from '@moderntribe/common/elements';
import MoveDelete from './move-delete/container';
import './style.pcss';

class RSVPAdvancedOptions extends PureComponent {
	static propTypes = {
		isDisabled: PropTypes.bool.isRequired,
		hasTicketsPlus: PropTypes.bool,
		hasBeenCreated: PropTypes.bool,
		clientId: PropTypes.string,
	};

	constructor( props ) {
		super( props );
		this.accordionId = uniqid();
	}

	getContent = () => (
		<Fragment>
			<RSVPDuration />
			{ this.props.hasTicketsPlus && <RSVPAttendeeRegistration /> }
			{ this.props.hasBeenCreated && (
				<MoveDelete clientId={ this.props.clientId } />
			) }
		</Fragment>
	);

	getHeader = () => (
		<Fragment>
			<Dashicon
				className="tribe-editor__rsvp__advanced-options-header-icon"
				icon="arrow-down"
			/>
			<span className="tribe-editor__rsvp__advanced-options-header-text">
				{ __( 'Advanced Options', 'events-gutenberg' ) }
			</span>
		</Fragment>
	);

	getRows = () => ( [
		{
			accordionId: this.accordionId,
			content: this.getContent(),
			contentClassName: 'tribe-editor__rsvp__advanced-options-content',
			header: this.getHeader(),
			headerAttrs: { disabled: this.props.isDisabled },
			headerClassName: 'tribe-editor__rsvp__advanced-options-header',
		},
	] );

	render() {
		return (
			<Accordion
				className="tribe-editor__rsvp__advanced-options"
				rows={ this.getRows() }
			/>
		);
	}
}

export default RSVPAdvancedOptions;
