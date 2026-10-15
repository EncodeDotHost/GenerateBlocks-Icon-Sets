/* Icon Sets for GenerateBlocks admin page. Plain ES2017, no build step. */
( function () {
	'use strict';

	const apiFetch = window.wp.apiFetch;
	const { __, sprintf } = window.wp.i18n;
	const data = window.gbisData;
	const NS = '/' + data.namespace;

	const $ = ( sel, root ) => ( root || document ).querySelector( sel );
	const $$ = ( sel, root ) => Array.from( ( root || document ).querySelectorAll( sel ) );

	const app = $( '#gbis-app' );
	const toolbar = $( '.gbis-toolbar' );
	const progressBox = $( '.gbis-progress' );
	const progress = $( 'progress', progressBox );
	const progressText = $( '.gbis-progress-text', progressBox );
	const log = $( '.gbis-log' );
	const selectedCount = $( '.gbis-selected-count' );

	const STATUS = {
		'not-installed': __( 'Not installed', 'icon-sets-for-generateblocks' ),
		installed: __( 'Installed', 'icon-sets-for-generateblocks' ),
		changed: __( 'Update available', 'icon-sets-for-generateblocks' ),
		foreign: __( 'Exists, not from this plugin', 'icon-sets-for-generateblocks' ),
	};

	let manifest = null;
	let files = new Map(); // path -> file object (with status, group_id)
	let busy = false;

	function el( tag, attrs, children ) {
		const node = document.createElement( tag );
		Object.entries( attrs || {} ).forEach( ( [ k, v ] ) => {
			if ( v === null || v === undefined || v === false ) {
				return;
			}
			if ( k === 'text' ) {
				node.textContent = v;
			} else if ( k === 'class' ) {
				node.className = v;
			} else if ( k.startsWith( 'on' ) ) {
				node.addEventListener( k.slice( 2 ), v );
			} else if ( v === true ) {
				node.setAttribute( k, '' );
			} else {
				node.setAttribute( k, v );
			}
		} );
		( children || [] ).forEach( ( c ) => c && node.appendChild( typeof c === 'string' ? document.createTextNode( c ) : c ) );
		return node;
	}

	function link( url, text ) {
		return el( 'a', { href: url, target: '_blank', rel: 'noopener', text } );
	}

	function errorMessage( err ) {
		if ( err && err.message ) {
			return err.message;
		}
		return __( 'Unknown error.', 'icon-sets-for-generateblocks' );
	}

	function addLog( text, type ) {
		log.prepend( el( 'li', { class: 'gbis-log-' + ( type || 'info' ), text } ) );
	}

	/* ---------- Loading and rendering ---------- */

	async function load( refresh ) {
		app.replaceChildren( el( 'p', { class: 'gbis-loading', text: __( 'Loading the set list from GitHub…', 'icon-sets-for-generateblocks' ) } ) );
		try {
			manifest = await apiFetch( { path: NS + '/manifest' + ( refresh ? '?refresh=1' : '' ) } );
		} catch ( err ) {
			app.replaceChildren( el( 'div', { class: 'notice notice-error inline' }, [ el( 'p', { text: sprintf( __( 'Could not load the set list: %s', 'icon-sets-for-generateblocks' ), errorMessage( err ) ) } ) ] ) );
			return;
		}
		files = new Map();
		manifest.sets.forEach( ( set ) => set.styles.forEach( ( style ) => style.files.forEach( ( f ) => {
			f.set = set;
			files.set( f.path, f );
		} ) ) );
		render();
	}

	function setCounts( set ) {
		let total = 0;
		let installed = 0;
		let changed = 0;
		set.styles.forEach( ( s ) => s.files.forEach( ( f ) => {
			total++;
			if ( f.status === 'installed' || f.status === 'changed' ) {
				installed++;
			}
			if ( f.status === 'changed' ) {
				changed++;
			}
		} ) );
		return { total, installed, changed };
	}

	function setSummaryText( set ) {
		const c = setCounts( set );
		let text = sprintf( __( '%1$d of %2$d groups installed', 'icon-sets-for-generateblocks' ), c.installed, c.total );
		if ( c.changed ) {
			text += ' · ' + sprintf( __( '%d with updates', 'icon-sets-for-generateblocks' ), c.changed );
		}
		return text;
	}

	function render() {
		const total = el( 'p', { class: 'gbis-total', text: sprintf( __( '%1$d sets, %2$d icons in total.', 'icon-sets-for-generateblocks' ), manifest.sets.length, manifest.total ) } );
		app.replaceChildren( total, ...manifest.sets.map( renderSet ) );
		updateToolbar();
	}

	function renderSet( set ) {
		const meta = el( 'div', { class: 'gbis-set-meta' }, [
			el( 'p', {}, [
				sprintf( __( 'Author: %s', 'icon-sets-for-generateblocks' ), set.author ),
				' · ',
				__( 'License: ', 'icon-sets-for-generateblocks' ),
				set.licenseUrl ? link( set.licenseUrl, set.license ) : set.license,
				...set.links.flatMap( ( l ) => [ ' · ', link( l.url, l.label ) ] ),
			] ),
			set.note ? el( 'p', { text: set.note } ) : null,
			set.attribution ? el( 'p', { class: 'gbis-attribution' }, [ el( 'strong', { text: __( 'Attribution required: ', 'icon-sets-for-generateblocks' ) } ), set.attribution ] ) : null,
		] );

		const summary = el( 'summary', {}, [
			el( 'span', { class: 'gbis-set-name', text: set.name } ),
			el( 'span', { class: 'gbis-set-license', text: set.license } ),
			el( 'span', { class: 'gbis-set-summary', text: setSummaryText( set ) } ),
		] );

		const details = el( 'details', { class: 'gbis-set', 'data-set': set.folder }, [ summary, meta, ...set.styles.map( ( style ) => renderStyle( set, style ) ) ] );
		set.summaryNode = $( '.gbis-set-summary', summary );
		return details;
	}

	function renderStyle( set, style ) {
		const all = el( 'input', {
			type: 'checkbox',
			'aria-label': sprintf( __( 'Select all groups in %s', 'icon-sets-for-generateblocks' ), style.name ),
			onchange: ( e ) => {
				$$( 'tbody input[type=checkbox]', table ).forEach( ( cb ) => {
					cb.checked = e.target.checked;
				} );
				updateToolbar();
			},
		} );

		const table = el( 'table', { class: 'widefat striped gbis-table' }, [
			el( 'thead', {}, [ el( 'tr', {}, [
				el( 'td', { class: 'check-column' }, [ all ] ),
				el( 'th', { text: __( 'Group', 'icon-sets-for-generateblocks' ) } ),
				el( 'th', { class: 'gbis-num', text: __( 'Icons', 'icon-sets-for-generateblocks' ) } ),
				el( 'th', { text: __( 'Status', 'icon-sets-for-generateblocks' ) } ),
				el( 'th', { text: __( 'Actions', 'icon-sets-for-generateblocks' ) } ),
			] ) ] ),
			el( 'tbody', {}, style.files.map( renderRow ) ),
		] );

		const heading = set.styles.length > 1 || style.name !== 'Default'
			? el( 'h3', { text: sprintf( __( '%1$s · %2$d icons', 'icon-sets-for-generateblocks' ), style.name, style.count ) } )
			: null;

		return el( 'div', { class: 'gbis-style' }, [ heading, table ] );
	}

	function renderRow( file ) {
		const row = el( 'tr', { 'data-path': file.path } );
		file.row = row;
		fillRow( file );
		return row;
	}

	function fillRow( file ) {
		const row = file.row;
		const checked = $( 'input[type=checkbox]', row );
		const cb = el( 'input', { type: 'checkbox', value: file.path, 'aria-label': file.group, onchange: updateToolbar } );
		cb.checked = checked ? checked.checked : false;

		const actions = el( 'td', { class: 'gbis-actions' } );
		const add = ( label, action ) => actions.appendChild( el( 'button', {
			type: 'button',
			class: 'button button-small',
			text: label,
			onclick: () => run( action, [ file ] ),
		} ) );

		if ( file.status === 'not-installed' ) {
			add( __( 'Install', 'icon-sets-for-generateblocks' ), 'install' );
		} else if ( file.status === 'foreign' ) {
			add( __( 'Replace', 'icon-sets-for-generateblocks' ), 'replace' );
		} else {
			add( file.status === 'changed' ? __( 'Update', 'icon-sets-for-generateblocks' ) : __( 'Reinstall', 'icon-sets-for-generateblocks' ), 'reinstall' );
			add( __( 'Remove', 'icon-sets-for-generateblocks' ), 'remove' );
		}

		row.replaceChildren(
			el( 'th', { class: 'check-column', scope: 'row' }, [ cb ] ),
			el( 'td', { text: file.group } ),
			el( 'td', { class: 'gbis-num', text: String( file.count ) } ),
			el( 'td', {}, [ el( 'span', { class: 'gbis-status gbis-status-' + file.status, text: STATUS[ file.status ] || file.status } ) ] ),
			actions
		);
	}

	/* ---------- Selection and toolbar ---------- */

	function selected() {
		return $$( '#gbis-app tbody input[type=checkbox]:checked' ).map( ( cb ) => files.get( cb.value ) ).filter( Boolean );
	}

	function changedFiles() {
		return Array.from( files.values() ).filter( ( f ) => f.status === 'changed' );
	}

	function updateToolbar() {
		const sel = selected();
		const owned = sel.filter( ( f ) => f.status === 'installed' || f.status === 'changed' );
		const installable = sel.filter( ( f ) => f.status === 'not-installed' || f.status === 'foreign' );
		$( '[data-action=install]', toolbar ).disabled = busy || ! installable.length;
		$( '[data-action=reinstall]', toolbar ).disabled = busy || ! owned.length;
		$( '[data-action=remove]', toolbar ).disabled = busy || ! owned.length;
		$( '[data-action=update-all]', toolbar ).disabled = busy || ! changedFiles().length;
		$( '[data-action=refresh]', toolbar ).disabled = busy;
		selectedCount.textContent = sel.length ? sprintf( __( '%d selected', 'icon-sets-for-generateblocks' ), sel.length ) : '';
		$$( '.gbis-actions button' ).forEach( ( b ) => {
			b.disabled = busy;
		} );
	}

	/* ---------- Actions ---------- */

	async function runOne( action, file ) {
		if ( action === 'remove' ) {
			await apiFetch( { path: NS + '/remove', method: 'POST', data: { group_id: file.group_id } } );
			return 'not-installed';
		}
		const res = await apiFetch( {
			path: NS + '/install',
			method: 'POST',
			data: { path: file.path, replace: action === 'replace' },
		} );
		return res.status;
	}

	async function run( action, list ) {
		if ( busy || ! list.length ) {
			return;
		}

		// Build the job list: [ action, file ].
		let jobs = [];
		if ( action === 'install' ) {
			const fresh = list.filter( ( f ) => f.status === 'not-installed' );
			const foreign = list.filter( ( f ) => f.status === 'foreign' );
			jobs = fresh.map( ( f ) => [ 'install', f ] );
			if ( foreign.length ) {
				// eslint-disable-next-line no-alert
				const ok = window.confirm( sprintf(
					__( '%d of the selected groups already exist in the Asset Library and were not installed by this plugin. Replace them with the repository version? Cancel skips them.', 'icon-sets-for-generateblocks' ),
					foreign.length
				) );
				if ( ok ) {
					jobs = jobs.concat( foreign.map( ( f ) => [ 'replace', f ] ) );
				}
			}
		} else if ( action === 'replace' ) {
			// eslint-disable-next-line no-alert
			if ( ! window.confirm( sprintf( __( 'Replace the existing group "%s" with the repository version?', 'icon-sets-for-generateblocks' ), list[ 0 ].group ) ) ) {
				return;
			}
			jobs = list.map( ( f ) => [ 'replace', f ] );
		} else if ( action === 'remove' ) {
			const owned = list.filter( ( f ) => f.status === 'installed' || f.status === 'changed' );
			// eslint-disable-next-line no-alert
			if ( ! owned.length || ! window.confirm( sprintf( __( 'Remove %d group(s) from the Asset Library? Icons already placed in blocks are not affected.', 'icon-sets-for-generateblocks' ), owned.length ) ) ) {
				return;
			}
			jobs = owned.map( ( f ) => [ 'remove', f ] );
		} else {
			// reinstall
			jobs = list.filter( ( f ) => f.status === 'installed' || f.status === 'changed' ).map( ( f ) => [ 'reinstall', f ] );
		}

		if ( ! jobs.length ) {
			return;
		}

		busy = true;
		updateToolbar();
		progressBox.hidden = false;
		progress.max = jobs.length;
		progress.value = 0;
		let failed = 0;

		for ( let i = 0; i < jobs.length; i++ ) {
			const [ act, file ] = jobs[ i ];
			progressText.textContent = sprintf( __( '%1$d of %2$d: %3$s', 'icon-sets-for-generateblocks' ), i + 1, jobs.length, file.group );
			try {
				file.status = await runOne( act, file );
				addLog( ( act === 'remove' ? __( 'Removed: ', 'icon-sets-for-generateblocks' ) : __( 'Installed: ', 'icon-sets-for-generateblocks' ) ) + file.group, 'success' );
			} catch ( err ) {
				failed++;
				addLog( file.group + ': ' + errorMessage( err ), 'error' );
			}
			fillRow( file );
			file.set.summaryNode.textContent = setSummaryText( file.set );
			progress.value = i + 1;
		}

		progressText.textContent = failed
			? sprintf( __( 'Done. %1$d succeeded, %2$d failed.', 'icon-sets-for-generateblocks' ), jobs.length - failed, failed )
			: sprintf( __( 'Done. %d succeeded.', 'icon-sets-for-generateblocks' ), jobs.length );

		$$( '#gbis-app input[type=checkbox]' ).forEach( ( cb ) => {
			cb.checked = false;
		} );
		busy = false;
		updateToolbar();
	}

	toolbar.addEventListener( 'click', ( e ) => {
		const btn = e.target.closest( 'button[data-action]' );
		if ( ! btn || btn.disabled ) {
			return;
		}
		const action = btn.dataset.action;
		if ( action === 'refresh' ) {
			load( true );
		} else if ( action === 'update-all' ) {
			run( 'reinstall', changedFiles() );
		} else {
			run( action, selected() );
		}
	} );

	/* ---------- Settings ---------- */

	const attributionBox = $( '#gbis-print-attribution' );
	const settingsStatus = $( '.gbis-settings-status' );
	attributionBox.addEventListener( 'change', async () => {
		settingsStatus.textContent = __( 'Saving…', 'icon-sets-for-generateblocks' );
		try {
			await apiFetch( { path: NS + '/settings', method: 'POST', data: { print_attribution: attributionBox.checked } } );
			settingsStatus.textContent = __( 'Saved.', 'icon-sets-for-generateblocks' );
		} catch ( err ) {
			attributionBox.checked = ! attributionBox.checked;
			settingsStatus.textContent = errorMessage( err );
		}
	} );

	load( false );
}() );
