let React = window.React
let ReactDOM = window.ReactDOM
let moment = window.moment
let createReactClass = require('create-react-class')
let createDOMPurify = require('dompurify')
let DOMPurify = createDOMPurify(window)

import ReactModal from 'react-modal'
import Textarea from 'react-textarea-autosize'
import AutosizeInput from 'react-input-autosize'

import Header from './Header.js'
import Pagination from './Pagination.js'
import Helpers from './Helpers.js'

if (FormCraftGlobal.ajaxurl.indexOf('?') > -1) {
	FormCraftGlobal.ajaxurl = `${FormCraftGlobal.ajaxurl}&`
} else {
	FormCraftGlobal.ajaxurl = `${FormCraftGlobal.ajaxurl}?`
}


let FormCraftEntries = createReactClass({
	getInitialState() {
		return {
			source: {
				page: 1,
				pages: 1,
				action: 'formcraft_get_entries',
				query: '',
				sortWhat: 'created',
				sortOrder: 'DESC',
				whichForm: 0,
				perPage: 11
			},
			entryList: {
				entries: [],
				loading: false,
				toggleChecked: false
			},
			entryView: {
				entryData: false,
				loading: false,
				hideEmpty: true
			},
			editedEntry: {
			},
			formList: []
		}
	},
	componentDidMount() {
		this.refreshList()
		this.getFormList()
	},
	getFormList: function() {
		let formSource = {
			max: 999,
			sortWhat: 'name',
			sortOrder: 'ASC',
			action: 'formcraft_get_forms'
		}
		this.serverRequest = jQuery.getJSON(`${FormCraftGlobal.ajaxurl}${jQuery.param(formSource)}`, (response) => {
			this.setState({
				formList: response.forms || []
			})
		})
	},
	refreshList: function(newParams = {}) {
		this.setState({
			entryList: Object.assign(this.state.entryList, { loading: true }),
			source: Object.assign(this.state.source, newParams)
		})
		let combinedParams = Object.assign({}, this.state.source, newParams)
		this.serverRequest = jQuery.getJSON(`${FormCraftGlobal.ajaxurl}${jQuery.param(combinedParams)}`, function (result) {
			this.setState({
				entryList: {
					pages: result.pages,
					total: result.total,
					entries: result.entries || [],
					toggleChecked: false,
					loading: false
				}
			})
		}.bind(this))
	},
	fetchEntry: function(entryID) {
		this.setState({
			entryView: {
				loading: true,
				hideEmpty: this.state.entryView.hideEmpty,
				entryData: {
					id: entryID
				}
			}
		})
		let entrySource = {
			entryID: entryID,
			action: 'formcraft_get_entry_content'
		}
		this.serverRequest = jQuery.getJSON(`${FormCraftGlobal.ajaxurl}${jQuery.param(entrySource)}`, function (result) {
			let newResult = {}
			result.content.forEach((field) => {
				newResult[field.page_name] = newResult[field.page_name] || {}
				if (field.value && typeof field.value === 'string') {
					field.value = DOMPurify.sanitize(field.value, {
						ALLOWED_TAGS: ['a', 'img'],
						ALLOWED_ATTR: ['target', 'href', 'src']
					})
				}
				if (field.type === 'matrix') {
					field.value = field.value.map((lineItem) => {
						return `${lineItem.question}: ${lineItem.value}`
					})
				}
				if (field.type === 'fileupload') {
					field.value = field.value.map((lineItem, lineIndex) => {
						return <div key={lineIndex}><a href={field.url[lineIndex]} target='_blank'>{lineItem}</a><br/></div>
					})
				}
				if (typeof field.value !== 'string' && typeof field.value[0] === 'string') {
					field.value = field.value.join("\r\n")
				}
				if (field.value && typeof field.value === 'string' && field.value.substring(0, 10) === 'data:image') {
					field.value = <img src={field.value}/>
				}
				field.isEditable = true
				if (field.type === 'fileupload' || field.type === 'signature') {
					field.isEditable = false
				}
				field.isEmpty = field.value === ''
				field.value = field.value || '(empty)'
				newResult[field.page_name][field.identifier] = field
			})
			this.setState({
				entryView: {
					entryData: {
						content: newResult,
						form: result.form,
						created_date: result.created_date,
						created_time: result.created_time,
						id: result.id,
						form_name: result.form_name,
						visitor: result.visitor
					},
					hideEmpty: this.state.entryView.hideEmpty,
					loading: false
				}
			})
		}.bind(this))
	},
	trashEntries() {
		let deleteEntries = []
		this.state.entryList.entries.forEach((entry) => {
			if (entry.isChecked) {
				deleteEntries.push(entry.id)
			}
		})
		if (deleteEntries.length === 0) return
		this.setState({
			entryList: Object.assign(this.state.entryList, { loading: true })
		})
		let deleteSource = {
			entries: deleteEntries,
			action: 'formcraft_delete_entries'
		}
		this.serverRequest = jQuery.getJSON(`${FormCraftGlobal.ajaxurl}${jQuery.param(deleteSource)}`, () => {
			this.refreshList()
		})
	},
	updatePage: function(newPage) {
		this.refreshList({ page: newPage })
	},
	toggleChecked(element) {
		this.setState({
			entryList: Object.assign({}, this.state.entryList, { toggleChecked: element.target.checked })
		}, () => {
			if (this.state.entryList.toggleChecked) {
				this.state.entryList.entries.forEach((x, index) => {
					this.updateChecked(index, true)
				})
			} else {
				this.state.entryList.entries.forEach((x, index) => {
					this.updateChecked(index, false)
				})
			}
		})
	},
	updateChecked(entryIndex, element) {
		let isChecked = typeof element === 'object' ? element.target.checked : element
		this.state.entryList.entries[entryIndex].isChecked = isChecked
		let checkedNos = this.state.entryList.entries.reduce((checked, current) => {
			if (current.isChecked) {
				checked.push(current.id)
			}
			return checked
		}, [])
		this.state.entryList.totalChecked = checkedNos.length
		this.setState({
			entryList: this.state.entryList
		})
	},
	updateSearch(type, element) {
		element.preventDefault()
		let value = element.target.getElementsByTagName('input')[0].value
		this.refreshList({ page: 1, query: value })
	},
	updateConfig(type, element) {
		let value = type === 'whichForm' ? parseInt(element.target.value, 10) : element.target.value
		this.setState({
			config: Object.assign(this.state.source, { [type]: value })
		})
		if (type === 'whichForm') {
			this.refreshList({ page: 1, whichForm: value })
		}
	},
	onSort: function(type) {
		this.refreshList({
			sortWhat: type,
			sortOrder: this.state.source.sortOrder === 'ASC' ? 'DESC' : 'ASC'
		})
	},
	toggleHideEmpty() {
		let entryView = Object.assign({}, this.state.entryView, { hideEmpty: !this.state.entryView.hideEmpty })
		this.setState({
			entryView: entryView
		})
	},
	toggleEditForm() {
		if (!this.state.entryView.editing) {
			let entryView = Object.assign({}, this.state.entryView, { editing: true })
			this.setState({
				entryView: entryView
			})
		} else {
			let data = {
				action: 'formcraft_update_entry_content',
				entryID: this.state.entryView.entryData.id,
				entryData: this.state.editedEntry
			}
			let entryView = Object.assign({}, this.state.entryView)
			entryView.loading = true
			this.setState({
				entryView
			})
			this.serverRequest = jQuery.post(`${FormCraftGlobal.ajaxurl}${jQuery.param(data)}`, () => {
				this.fetchEntry(data.entryID)
			})
		}
	},
	editFieldsChange(type, element) {
		let editedEntry = Object.assign({}, this.state.editedEntry)
		editedEntry[type] = element.target.value
		this.setState({ editedEntry })
	},
	render() {
		return (
			<div>
				<Header/>
				<div>
					<div className='block entry-list-block padding-right width-4'>
						<EntryList trashEntries={this.trashEntries} updateSearch={this.updateSearch} onSort={this.onSort} toggleChecked={this.toggleChecked} updateChecked={this.updateChecked} refreshList={this.refreshList} fetchEntry={this.fetchEntry} updateConfig={this.updateConfig} updatePage={this.updatePage} {...this.state}/>
					</div>
					<div className='block entry-view-block width-6'>
						<EntryView editFieldsChange={this.editFieldsChange} toggleEditForm={this.toggleEditForm} toggleHideEmpty={this.toggleHideEmpty} {...this.state}/>
					</div>
				</div>
			</div>
		)
	}
})

let EntryList = createReactClass({
	getInitialState() {
		return {
			tempSearch: ''
		}
	},
	render() {
		let entryList = this.props.entryList
		let entryView = this.props.entryView
		let selectedID = entryView.entryData.id ? entryView.entryData.id : 0
		let source = this.props.source
		let tbody = <div className='NoResults'>No Entries Found</div>
		if (entryList.entries.length > 0) {
			tbody = entryList.entries.map((entry, entryIndex) => {
				entry.isChecked = entry.isChecked || false
				return (
					<div key={entry.id} className={`tr canHover ${selectedID === entry.id ? 'isActive' : ''}`}>
						<label style={{ width: '9%' }}>
							<input checked={entry.isChecked} type='checkbox' onChange={this.props.updateChecked.bind(null, entryIndex)}/>
						</label>
						<a style={{ width: '12%' }} onClick={this.props.fetchEntry.bind(null, entry.id)}>
							{entry.id}
						</a>
						<a title={entry.form_name} style={{ width: '47%' }} onClick={this.props.fetchEntry.bind(null, entry.id)}>
							{entry.form_name}
						</a>
						<a style={{ width: '32%' }} onClick={this.props.fetchEntry.bind(null, entry.id)}>
							{moment.unix(entry.created).fromNow()}
						</a>
					</div>
				)
			})
		}
		return (
			<div className='formcraft_card formcraft_entry_list'>
				<div className='formcraft_table'>
					<div className='block-header'>
						<span className='block-title'>Entries</span>
						{
							entryList.loading ?
								<div className='formcraft-loader'></div> :
								''
						}
						{
							this.props.source.whichForm !== 0 ?
							<a target='_blank' className='float-right formcraft-button small'
							href={`${FormCraftGlobal.baseurl}?formcraft_export_entries=${this.props.source.whichForm}`}
							>
								<i className='formcraft-icon'>call_made</i>
								Export All
							</a>
							:
							<a title='Please select a form in place of (All Forms)' className='disabled float-right formcraft-button small' onClick={(e) => e.preventDefault}>
								<i className='formcraft-icon'>call_made</i>
								Export All
							</a>
						}
						<form className='float-right type-search formcraft-input-button small' onSubmit={this.props.updateSearch.bind(null, 'query')}>
							<AutosizeInput placeholder='Search' value={this.state.tempSearch} onChange={(e) => {
								this.setState({ tempSearch: e.target.value })
							}}/>
						</form>
						<button className='TrashEntries float-right formcraft-button small red' style={{ display: entryList.totalChecked ? 'inline-block' : 'none' }} onClick={this.props.trashEntries}>
							Trash
						</button>
					</div>
					<div className='tr thead'>
						<label style={{ width: '9%' }}>
							<input type='checkbox' onChange={this.props.toggleChecked} checked={entryList.toggleChecked}/>
						</label>
						<span style={{ width: '12%' }} className='sortable' onClick={this.props.onSort.bind(null, 'ID')}>
							ID
							{this.props.source.sortWhat === 'ID' && this.props.source.sortOrder === 'ASC' ? <i className='formcraft-icon'>keyboard_arrow_up</i> : ''}
							{this.props.source.sortWhat === 'ID' && this.props.source.sortOrder === 'DESC' ? <i className='formcraft-icon'>keyboard_arrow_down</i> : ''}
						</span>
						<select className='td' style={{ width: '47%' }} onChange={this.props.updateConfig.bind(null, 'whichForm')} value={this.props.source.whichForm}>
							<option value={0}>(All Forms)</option>
							{this.props.formList.map((form) => {
								return <option value={form.id} key={form.id}>{form.name}</option>
							})}
						</select>
						<span style={{ width: '32%' }} className='sortable' onClick={this.props.onSort.bind(null, 'created')}>
							Created
							{this.props.source.sortWhat === 'created' && this.props.source.sortOrder === 'ASC' ? <i className='formcraft-icon'>keyboard_arrow_up</i> : ''}
							{this.props.source.sortWhat === 'created' && this.props.source.sortOrder === 'DESC' ? <i className='formcraft-icon'>keyboard_arrow_down</i> : ''}
						</span>
					</div>
					<div className='tbody' style={{ opacity: entryList.loading ? '.5' : '1' }}>
						{tbody}
					</div>
				</div>
				<Pagination updatePage={this.props.updatePage} page={source.page} pages={entryList.pages}/>
				{this.props.children}
			</div>
		)
	}
})

let EntryView = createReactClass({
	render() {
		let entryView = Object.assign({}, this.props.entryView)
		let entryData = entryView.entryData
		let tbodyRender = ''
		if (entryData.content) {
			tbodyRender = Object.keys(entryData.content).map((page, pageIndex) => {
				let thisPage =
				Object.keys(entryData.content[page]).map((field, fieldIndex) => {
					let thisField = entryData.content[page][field]
					return (
						entryView.hideEmpty && thisField.isEmpty ?
						<span key={fieldIndex} className='field-cover isEmpty' style={{ width: thisField.width ? thisField.width : '100%' }}>
						</span>
						:
						<span key={fieldIndex} className={`field-type-${thisField.type} field-cover`} style={{ width: thisField.width ? thisField.width : '100%' }}>
							<span className='field-label'>
								{thisField.label || '(no label)'}
							</span>
							{
								entryView.editing && thisField.isEditable ?
								<Textarea className='field-value' defaultValue={thisField.value} onChange={this.props.editFieldsChange.bind(null, thisField.identifier)}/>
								:
								typeof thisField.value !== 'string' ?
								<span className='field-value'>
									{thisField.value}
								</span>
								:
								<span className='field-value' dangerouslySetInnerHTML={{ __html: thisField.value }}>
								</span>
							}
						</span>
					)
				})
				return (
					<div key={pageIndex} className='field-page-cover'>
						<div className='field-page-title'>{page}</div>
						<div className='field-page-content'>{thisPage}</div>
					</div>
				)
			})
		}
		return (
			<div className='formcraft_card formcraft_entry_view'>
				<div className='formcraft_table'>
					<div className='block-header'>
						<span className='block-title'>
							{ entryData.content ? entryData.form_name : entryView.loading ? 'Loading' : 'Entry View' }
						</span>
						{
							entryView.loading ?
								<div className='formcraft-loader'></div> :
								''
						}
						{
							entryData.content ?
							<div className='float-right'>
								<button className={`formcraft-button small ${entryView.hideEmpty ? 'isEnabled' : ''}`} onClick={this.props.toggleHideEmpty}>
									Hide Empty Fields
								</button>
								{
									entryView.editing ?
									<button className='formcraft-button small green' onClick={this.props.toggleEditForm}>
										Save Changes
									</button>
									:
									<button className='formcraft-button small' onClick={this.props.toggleEditForm}>
										Edit Entry
									</button>
								}
								<button className='formcraft-button small' onClick={window.print}>
									Print
								</button>
							</div>
							:
							''
						}
					</div>
					{
						entryData.content ?
							<div className='tr thead'>
								<span style={{ width: '13%' }}>
									#{entryData.id}
								</span>
								<span style={{ width: '23%' }}>
									{entryData.created_date}
									&nbsp;at&nbsp;
									{entryData.created_time}
								</span>
								<span title={entryData.visitor.URL} style={{ width: '49%' }}>
									Referer: <a href={entryData.visitor.URL} target='_blank'>{entryData.visitor.URL}</a>
								</span>
								<span style={{ width: '14%' }}>
									IP: {entryData.visitor.IP}
								</span>
							</div>
							:
							<div className='tr thead'>
							</div>
					}
					<div className={`tbody page-count-${tbodyRender.length}`}>
						{tbodyRender}
					</div>
				</div>
			</div>
		)
	}
})

jQuery(document).ready(function() {
	ReactDOM.render(<FormCraftEntries/>, document.getElementById('formcraft_dashboard'))
})
