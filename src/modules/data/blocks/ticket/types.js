/* eslint-disable max-len */
/**
 * Internal dependencies
 */
import { PREFIX_TICKETS_STORE } from '@moderntribe/tickets/data/utils';

//
// ─── TICKETS TYPES ──────────────────────────────────────────────────────────────
//

export const SET_TICKETS_INITIAL_STATE = `${ PREFIX_TICKETS_STORE }/SET_TICKETS_INITIAL_STATE`;
export const RESET_TICKETS_BLOCK = `${ PREFIX_TICKETS_STORE }/RESET_TICKETS_BLOCK`;

export const SET_TICKETS_HEADER_IMAGE = `${ PREFIX_TICKETS_STORE }/SET_TICKETS_HEADER_IMAGE`;
export const SET_TICKETS_IS_SELECTED = `${ PREFIX_TICKETS_STORE }/SET_TICKETS_IS_SELECTED`;
export const SET_TICKETS_IS_SETTINGS_OPEN = `${ PREFIX_TICKETS_STORE }/SET_TICKETS_IS_SETTINGS_OPEN`;
export const SET_TICKETS_IS_SETTINGS_LOADING = `${ PREFIX_TICKETS_STORE }/SET_TICKETS_IS_SETTINGS_LOADING`;
export const SET_TICKETS_PROVIDER = `${ PREFIX_TICKETS_STORE }/SET_TICKETS_PROVIDER`;
export const SET_TICKETS_SHARED_CAPACITY = `${ PREFIX_TICKETS_STORE }/SET_TICKETS_SHARED_CAPACITY`;
export const SET_TICKETS_TEMP_SHARED_CAPACITY = `${ PREFIX_TICKETS_STORE }/SET_TICKETS_TEMP_SHARED_CAPACITY`;

//
// ─── HEADER IMAGE SAGA TYPES ────────────────────────────────────────────────────
//

export const FETCH_TICKETS_HEADER_IMAGE = `${ PREFIX_TICKETS_STORE }/FETCH_TICKETS_HEADER_IMAGE`;
export const UPDATE_TICKETS_HEADER_IMAGE = `${ PREFIX_TICKETS_STORE }/UPDATE_TICKETS_HEADER_IMAGE`;
export const DELETE_TICKETS_HEADER_IMAGE = `${ PREFIX_TICKETS_STORE }/DELETE_TICKETS_HEADER_IMAGE`;

//
// ─── CHILD TICKET TYPES ─────────────────────────────────────────────────────────
//

export const REGISTER_TICKET_BLOCK = `${ PREFIX_TICKETS_STORE }/REGISTER_TICKET_BLOCK`;
export const REMOVE_TICKET_BLOCK = `${ PREFIX_TICKETS_STORE }/REMOVE_TICKET_BLOCK`;
export const REMOVE_TICKET_BLOCKS = `${ PREFIX_TICKETS_STORE }/REMOVE_TICKET_BLOCKS`;

export const SET_TICKET_TITLE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TITLE`;
export const SET_TICKET_DESCRIPTION = `${ PREFIX_TICKETS_STORE }/SET_TICKET_DESCRIPTION`;
export const SET_TICKET_PRICE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_PRICE`;
export const SET_TICKET_SKU = `${ PREFIX_TICKETS_STORE }/SET_TICKET_SKU`;
export const SET_TICKET_IAC_SETTING = `${ PREFIX_TICKETS_STORE }/SET_TICKET_IAC_SETTING`;
export const SET_TICKET_START_DATE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_START_DATE`;
export const SET_TICKET_START_DATE_INPUT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_START_DATE_INPUT`;
export const SET_TICKET_START_DATE_MOMENT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_START_DATE_MOMENT`;
export const SET_TICKET_END_DATE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_END_DATE`;
export const SET_TICKET_END_DATE_INPUT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_END_DATE_INPUT`;
export const SET_TICKET_END_DATE_MOMENT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_END_DATE_MOMENT`;
export const SET_TICKET_START_TIME = `${ PREFIX_TICKETS_STORE }/SET_TICKET_START_TIME`;
export const SET_TICKET_END_TIME = `${ PREFIX_TICKETS_STORE }/SET_TICKET_END_TIME`;
export const SET_TICKET_START_TIME_INPUT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_START_TIME_INPUT`;
export const SET_TICKET_END_TIME_INPUT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_END_TIME_INPUT`;
export const SET_TICKET_CAPACITY_TYPE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_CAPACITY_TYPE`;
export const SET_TICKET_CAPACITY = `${ PREFIX_TICKETS_STORE }/SET_TICKET_CAPACITY`;

export const SET_TICKET_TEMP_TITLE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_TITLE`;
export const SET_TICKET_TEMP_DESCRIPTION = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_DESCRIPTION`;
export const SET_TICKET_TEMP_PRICE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_PRICE`;
export const SET_TICKET_TEMP_SKU = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_SKU`;
export const SET_TICKET_TEMP_IAC_SETTING = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_IAC_SETTING`;
export const SET_TICKET_TEMP_START_DATE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_START_DATE`;
export const SET_TICKET_TEMP_START_DATE_INPUT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_START_DATE_INPUT`;
export const SET_TICKET_TEMP_START_DATE_MOMENT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_START_DATE_MOMENT`;
export const SET_TICKET_TEMP_END_DATE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_END_DATE`;
export const SET_TICKET_TEMP_END_DATE_INPUT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_END_DATE_INPUT`;
export const SET_TICKET_TEMP_END_DATE_MOMENT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_END_DATE_MOMENT`;
export const SET_TICKET_TEMP_START_TIME = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_START_TIME`;
export const SET_TICKET_TEMP_END_TIME = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_END_TIME`;
export const SET_TICKET_TEMP_START_TIME_INPUT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_START_TIME_INPUT`;
export const SET_TICKET_TEMP_END_TIME_INPUT = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_END_TIME_INPUT`;
export const SET_TICKET_TEMP_CAPACITY_TYPE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_CAPACITY_TYPE`;
export const SET_TICKET_TEMP_CAPACITY = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_CAPACITY`;

export const SET_TICKET_SOLD = `${ PREFIX_TICKETS_STORE }/SET_TICKET_SOLD`;
export const SET_TICKET_AVAILABLE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_AVAILABLE`;
export const SET_TICKET_ID = `${ PREFIX_TICKETS_STORE }/SET_TICKET_ID`;
export const SET_TICKET_CURRENCY_SYMBOL = `${ PREFIX_TICKETS_STORE }/SET_TICKET_CURRENCY_SYMBOL`;
export const SET_TICKET_CURRENCY_POSITION = `${ PREFIX_TICKETS_STORE }/SET_TICKET_CURRENCY_POSITION`;
export const SET_TICKET_PROVIDER = `${ PREFIX_TICKETS_STORE }/SET_TICKET_PROVIDER`;
export const SET_TICKET_HAS_ATTENDEE_INFO_FIELDS = `${ PREFIX_TICKETS_STORE }/SET_TICKET_HAS_ATTENDEE_INFO_FIELDS`;
export const SET_TICKET_ATTENDEE_INFO_FIELDS = `${ PREFIX_TICKETS_STORE }/SET_TICKET_ATTENDEE_INFO_FIELDS`;
export const SET_TICKET_IS_LOADING = `${ PREFIX_TICKETS_STORE }/SET_TICKET_IS_LOADING`;
export const SET_TICKET_IS_MODAL_OPEN = `${ PREFIX_TICKETS_STORE }/SET_TICKET_IS_MODAL_OPEN`;
export const SET_TICKET_HAS_BEEN_CREATED = `${ PREFIX_TICKETS_STORE }/SET_TICKET_HAS_BEEN_CREATED`;
export const SET_TICKET_HAS_CHANGES = `${ PREFIX_TICKETS_STORE }/SET_TICKET_HAS_CHANGES`;
export const SET_TICKET_HAS_DURATION_ERROR = `${ PREFIX_TICKETS_STORE }/SET_TICKET_HAS_DURATION_ERROR`;
export const SET_TICKET_IS_SELECTED = `${ PREFIX_TICKETS_STORE }/SET_TICKET_IS_SELECTED`;
export const SET_TICKET_TYPE  = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TYPE`;

//
// ─── CHILD TICKET SAGA TYPES ────────────────────────────────────────────────────
//

export const SET_TICKET_DETAILS = `${ PREFIX_TICKETS_STORE }/SET_TICKET_DETAILS`;
export const SET_TICKET_TEMP_DETAILS = `${ PREFIX_TICKETS_STORE }/SET_TICKET_TEMP_DETAILS`;

export const HANDLE_TICKET_START_DATE = `${ PREFIX_TICKETS_STORE }/HANDLE_TICKET_START_DATE`;
export const HANDLE_TICKET_END_DATE = `${ PREFIX_TICKETS_STORE }/HANDLE_TICKET_END_DATE`;
export const HANDLE_TICKET_START_TIME = `${ PREFIX_TICKETS_STORE }/HANDLE_TICKET_START_TIME`;
export const HANDLE_TICKET_END_TIME = `${ PREFIX_TICKETS_STORE }/HANDLE_TICKET_END_TIME`;

export const FETCH_TICKET = `${ PREFIX_TICKETS_STORE }/FETCH_TICKET`;
export const CREATE_NEW_TICKET = `${ PREFIX_TICKETS_STORE }/CREATE_NEW_TICKET`;
export const UPDATE_TICKET = `${ PREFIX_TICKETS_STORE }/UPDATE_TICKET`;
export const DELETE_TICKET = `${ PREFIX_TICKETS_STORE }/DELETE_TICKET`;

export const SET_TICKET_INITIAL_STATE = `${ PREFIX_TICKETS_STORE }/SET_TICKET_INITIAL_STATE`;
