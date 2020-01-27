let React = window.React
let ReactDOM = window.ReactDOM
let moment = window.moment
let createReactClass = require('create-react-class')

import Dropzone from 'react-dropzone'
import ReactModal from 'react-modal'
import AutosizeInput from 'react-input-autosize'
import { Line } from 'react-chartjs-2'

import Header from './Header.js'
import Pagination from './Pagination.js'
import Helpers from './Helpers.js'

if (FormCraftGlobal.ajaxurl.indexOf('?') > -1) {
	FormCraftGlobal.ajaxurl = `${FormCraftGlobal.ajaxurl}&`
} else {
	FormCraftGlobal.ajaxurl = `${FormCraftGlobal.ajaxurl}?`
}


let FormCraftDashboard = createReactClass({
	getInitialState() {
		return {
			source: {
				page: 1,
				query: '',
				sortWhat: 'modified',
				sortOrder: 'DESC',
				action: 'formcraft_get_forms',
				max: 11
			},
			formsList: {
				pages: 1,
				total: 0,
				forms: [],
				loading: false
			}
		}
	},
	refreshList: function(newParams = {}) {
		this.setState({
			formsList: Object.assign(this.state.formsList, { loading: true }),
			source: Object.assign(this.state.source, newParams)
		})
		let combinedParams = Object.assign({}, Object.assign({}, this.state.source), newParams)
		this.serverRequest = jQuery.getJSON(`${FormCraftGlobal.ajaxurl}${jQuery.param(combinedParams)}`, (response) => {
			this.setState({
				formsList: {
					pages: response.pages,
					total: response.total,
					forms: response.forms || [],
					loading: false
				}
			})
		})
	},
	onTrash(form) {
		let r = confirm('Are you sure you want to delete this form?')
		if (r === true) {
			this.setState({
				formsList: Object.assign(this.state.formsList, { loading: true })
			})
			this.serverRequest = jQuery.getJSON(`${FormCraftGlobal.ajaxurl}action=formcraft_delete_form&form=${form}`, (result) => {
				this.setState({
					formsList: Object.assign(this.state.formsList, { loading: false })
				})
				if (result.success) {
					this.refreshList()
				}
			})
		}
	},
	render() {
		return (
			<div>
				<Header/>
				<div>
					<div className='block padding-right width-4'>
						<FormCover onTrash={this.onTrash} refreshList={this.refreshList} {...this.state.source} {...this.state.formsList}/>
					</div>
					<div className='block width-6'>
						<AnalyticsCover {...this.state.formsList}/>
					</div>
				</div>
			</div>
		)
	}
})


let FormCover = createReactClass({
	getInitialState: function() {
		return {
			newFormView: 'blank',
			allForms: [],
			newFormSource: {
				name: '',
				templatePath: '',
				duplicateFormID: 0
			}
		}
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
				allForms: response.forms || []
			})
		})
	},
	componentDidMount: function() {
		document.addEventListener('keydown', this.escFunction, false)
		document.addEventListener('touchstart', this.handleClickOutside)
		document.addEventListener('mousedown', this.handleClickOutside)
		this.props.refreshList()
		this.getFormList()
		this.updateNewFormView('blank')
	},
	updatePage: function(newPage) {
		this.props.refreshList({ page: newPage })
	},
	updateSearch: function(e) {
		e.preventDefault()
		this.props.refreshList({ page: 1, query: e.target.getElementsByTagName('input')[0].value })
	},
	onSort: function(type) {
		this.props.refreshList({
			sortWhat: type,
			sortOrder: this.props.sortOrder === 'ASC' ? 'DESC' : 'ASC'
		})
	},
	handleClickOutside(event) {
		if (this.state.showModal || this.state.showRateUs) {
			if (!jQuery(event.target).hasClass('FormCraftModal') && !jQuery(event.target).parents('.FormCraftModal').length) {
				this.handleCloseModal()
			}
		}
	},
	escFunction: function(x) {
		if (x.keyCode === 27) {
			this.handleCloseModal()
		}
	},
	handleOpenModal() {
		this.setState({ showModal: true, showing: false }, () => {
			setTimeout(() => {
				this.setState({ showModal: true, showing: true })
			}, 0)
		})
	},
	handleCloseModal() {
		this.setState({ hiding: true }, () => {
			setTimeout(() => {
				this.setState({ showModal: false, hiding: false })
			}, 500)
		})
	},
	updateNewFormView(type) {
		if (type === 'template') {
			this.setState({
				newFormView: type,
				modalStyle: {
					height: jQuery('#wpwrap').height() - 32,
					width: 740,
					marginLeft: parseInt(jQuery('#adminmenuback').width(), 10) / 2
				}
			})
		} else {
			this.setState({
				newFormView: type,
				modalStyle: {
					height: 440,
					width: 640,
					marginLeft: parseInt(jQuery('#adminmenuback').width(), 10) / 2
				}
			})
		}
	},
	showTemplate(templatePath) {
		let newFormSource = Object.assign({}, this.state.newFormSource, { templatePath })
		this.setState({ newFormSource })
		let params = {
			action: 'formcraft_get_template',
			path: templatePath
		}
		jQuery.getJSON(`${FormCraftGlobal.ajaxurl}${jQuery.param(params)}`, (response) => {
			if (response.html) {
				this.setState({
					templateHTML: response.html
				})
			}
		})
	},
	onDrop(files) {
		let newFormSource = Object.assign({}, this.state.newFormSource, { file: files[0] })
		this.setState({ newFormSource })
	},
	createNewForm(e) {
		e.preventDefault()
		let newFormSource = Object.assign({}, this.state.newFormSource, { type: this.state.newFormView })
		let data = new FormData()
		for (let key in newFormSource) {
			data.append(key, newFormSource[key])
		}
		this.setState({
			newFormLoading: true,
			newFormError: false,
			newFormSuccess: false
		})
		jQuery.ajax({
			url: `${FormCraftGlobal.ajaxurl}action=formcraft_new_form`,
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			method: 'POST',
			type: 'POST',
			success: (response) => {
				if (response.success) {
					this.setState({
						newFormLoading: false,
						newFormSuccess: response.success
					})
					if (response.redirect) {
						window.location = window.location.href.replace(window.location.hash, '') + response.redirect
					}
				} else if (response.failed) {
					this.setState({
						newFormLoading: false,
						newFormError: response.failed
					})
				}
			}
		})
	},
	render: function() {
		return (
			<div>
				<ReactModal style={{ content: this.state.modalStyle }} isOpen={this.state.showModal} contentLabel='NewForm' className='FormCraftModal FormCraftNewFormModal' overlayClassName={`formcraft-css FormCraftModalOverlay ${this.state.hiding ? 'HidingModal' : ''} ${this.state.showing ? 'ShowingModal' : ''}`}>
					<i onClick={this.handleCloseModal} className='CloseIcon formcraft-icon'>close</i>
					<div className='NewFormOptions'>
						<span
						className={`NewFormOption ${this.state.newFormView === 'blank' ? 'isActive' : ''}`}
						onClick={this.updateNewFormView.bind(null, 'blank')}>
							<span className='document'></span>
							<span className='NewFormText'>Blank</span>
						</span>
						<span
						className={`NewFormOption ${this.state.newFormView === 'template' ? 'isActive' : ''}`}
						onClick={this.updateNewFormView.bind(null, 'template')}>
							<span className='document-lines'></span>
							<span className='NewFormText'>Template</span>
						</span>
						<span
						className={`NewFormOption ${this.state.newFormView === 'duplicate' ? 'isActive' : ''}`}
						onClick={this.updateNewFormView.bind(null, 'duplicate')}>
							<span className='two-document'></span>
							<span className='NewFormText'>Duplicate</span>
						</span>
						<span
						className={`NewFormOption ${this.state.newFormView === 'import' ? 'isActive' : ''}`}
						onClick={this.updateNewFormView.bind(null, 'import')}>
							<span className='document-import'></span>
							<span className='NewFormText'>Import</span>
						</span>
					</div>
					<div className='NewFormView'>
						<div className='NewForm-Blank' style={{ display: this.state.newFormView === 'blank' ? 'block' : 'none' }}>
						(blank form)
						</div>
						<div className='NewForm-Template' style={{ display: this.state.newFormView === 'template' ? 'block' : 'none' }}>
							<div className='TemplateBrowser'>
							{
								Object.keys(FormCraftGlobal.templates).map((templateGroup, templateGroupIndex) => {
									return (
										<div className='TemplateGroup' key={templateGroupIndex}>
											<div className='TemplateGroupHead'>{templateGroup}</div>
											{
												FormCraftGlobal.templates[templateGroup].map((template, templateIndex) => {
													return (
														<button key={templateIndex} onClick={this.showTemplate.bind(null, template.path)} className={`${this.state.newFormSource.templatePath === template.path ? 'isActive' : ''}`}>
															{template.name}
														</button>
													)
												})
											}
										</div>
									)
								})
							}
							</div>
							<div className='ZoomInfo'>80% Zoom</div>
							{
								this.state.templateHTML ?
								<div className='TemplateView formcraft-css' dangerouslySetInnerHTML={{ __html: this.state.templateHTML }}>
								</div>
								:
								<div className='NoTemplate'>
								Select Form Template To View
								</div>
							}
						</div>
						<div className='NewForm-Duplicate' style={{ display: this.state.newFormView === 'duplicate' ? 'block' : 'none' }}>
							<select className='formcraft-button white' value={this.state.newFormSource.duplicateFormID} onChange={(e) => {
								this.setState({ newFormSource: Object.assign({}, this.state.newFormSource, { duplicateFormID: e.target.value }) })
							}}>
								<option value={0}>Select Form</option>
							{
								this.state.allForms.map((form) => {
									return <option value={form.id} key={form.id}>{form.name}</option>
								})
							}
							</select>
						</div>
						<div className='NewForm-Import' style={{ display: this.state.newFormView === 'import' ? 'block' : 'none' }}>
							<Dropzone accept='.txt' multiple={false} onDrop={this.onDrop} className='formcraft-button white' acceptClassName='has-file'>
							{
								this.state.newFormSource.file ?
								this.state.newFormSource.file.name : 'Upload Template File'
							}
							{
								this.state.newFormSource.file ?
								<i className='formcraft-icon'>check</i> : <i className='formcraft-icon'>arrow_upward</i>
							}
							</Dropzone>
						</div>
					</div>
					<form className='NewFormFooter' onSubmit={this.createNewForm}>
						{
							this.state.newFormLoading ?
							<div className='formcraft-loader'></div> :
							''
						}
						{
							this.state.newFormError ?
							<div className='NewFormError IsRed'>{this.state.newFormError}</div> :
							''
						}
						{
							this.state.newFormSuccess ?
							<div className='NewFormSuccess IsGreen'>{this.state.newFormSuccess}</div> :
							''
						}
						<span className='NewFormName'>
							<AutosizeInput className='formcraft-input-button large' type='text' placeholder='Form name' value={this.state.newFormSource.name} onChange={(e) => {
								this.setState({ newFormSource: Object.assign({}, this.state.newFormSource, { name: e.target.value }) })
							}
							}/>
						</span>
						<button className='formcraft-button large'>
						Create Form
						</button>
					</form>
				</ReactModal>
				<div className='formcraft_card formcraft_table_list'>
					<FormList updateSearch={this.updateSearch} handleOpenModal={this.handleOpenModal} {...this.props} {...this.state} onTrash={this.props.onTrash} onSort={this.onSort}/>
					<Pagination updatePage={this.updatePage} page={this.props.page} pages={this.props.pages}/>
					{this.props.children}
				</div>
			</div>
		)
	}
})
let FormList = createReactClass({
	getDefaultProps() {
		return {
			forms: []
		}
	},
	getInitialState() {
		return {
			tempSearch: ''
		}
	},
	render: function() {
		let tbody = <div className='NoResults'>No Forms Found</div>
		if (this.props.forms.length > 0) {
			tbody = this.props.forms.map((form) => {
				let editLink = `admin.php?page=formcraft-dashboard&id=${form.id}`
				return (
					<div key={form.id} className='tr canHover'>
						<a style={{ width: '11%' }} href={editLink}>
							{form.id}
						</a>
						<a title={`Edit ${form.name}`} style={{ width: '44%' }} href={editLink}>
							{form.name}
						</a>
						<a style={{ width: '35%' }} href={editLink}>
							{moment.unix(form.modified).fromNow()}
						</a>
						{
							this.props.onTrash ?
							<i style={{ width: '10%' }} onClick={this.props.onTrash.bind(null, form.id)} className='formcraft-icon TrashIcon'>close</i>
							:
							''
						}
					</div>
				)
			})
		}
		return (
			<div className='formcraft_table formcraft_form_list'>
				<div className='block-header'>
					<span className='block-title'>Forms</span>
					{
						this.props.loading ?
						<div className='formcraft-loader'></div> :
						''
					}
					<button onClick={this.props.handleOpenModal} className='float-right formcraft-button small'>
						<i className='formcraft-icon'>add</i>
						New Form
					</button>
					<form className='float-right type-search formcraft-input-button small' onSubmit={this.props.updateSearch}>
						<AutosizeInput placeholder='Search' value={this.state.tempSearch} onChange={(e) => {
							this.setState({ tempSearch: e.target.value })
						}}/>
					</form>
				</div>
				<div className='tr thead'>
					<span style={{ width: '11%' }} className='sortable' onClick={this.props.onSort.bind(null, 'ID')}>
					ID
						{this.props.sortWhat === 'ID' && this.props.sortOrder === 'ASC' ? <i className='formcraft-icon'>keyboard_arrow_up</i> : ''}
						{this.props.sortWhat === 'ID' && this.props.sortOrder === 'DESC' ? <i className='formcraft-icon'>keyboard_arrow_down</i> : ''}
					</span>
					<span style={{ width: '44%' }} className='sortable' onClick={this.props.onSort.bind(null, 'name')}>
					Name
						{this.props.sortWhat === 'name' && this.props.sortOrder === 'ASC' ? <i className='formcraft-icon'>keyboard_arrow_up</i> : ''}
						{this.props.sortWhat === 'name' && this.props.sortOrder === 'DESC' ? <i className='formcraft-icon'>keyboard_arrow_down</i> : ''}
					</span>
					<span style={{ width: '45%' }} className='sortable' onClick={this.props.onSort.bind(null, 'modified')}>
					Last Edit
						{this.props.sortWhat === 'modified' && this.props.sortOrder === 'ASC' ? <i className='formcraft-icon'>keyboard_arrow_down</i> : ''}
						{this.props.sortWhat === 'modified' && this.props.sortOrder === 'DESC' ? <i className='formcraft-icon'>keyboard_arrow_up</i> : ''}
					</span>
				</div>
				<div className='tbody' style={{ opacity: this.props.loading ? '.5' : '1' }}>
					{tbody}
				</div>
			</div>
		)
	}
})

/**
* Create Analytics View Elements
*/
let AnalyticsCover = createReactClass({
	getInitialState() {
		return {
			period: 'w',
			chartHeight: 250,
			chartWidth: 250,
			views: 0,
			submissions: 0,
			submissionsConversion: 0,
			payments: 0,
			paymentsConversion: 0,
			chartData: false,
			chartOptions: {
				scales: {
					xAxes: [{
						ticks: {
							beginAtZero: true,
							autoSkip: true,
							maxTicksLimit: 7,
							maxRotation: 0,
							minRotation: 0
						}
					}],
					yAxes: [{
						ticks: {
							beginAtZero: true,
							autoSkip: true,
							maxTicksLimit: 8
						}
					}]
				},
				tooltips: {
					titleFontSize: 12,
					bodyFontSize: 12,
					xPadding: 12,
					yPadding: 12,
					caretSize: 6,
					cornerRadius: 3,
					displayColors: false,
					titleFontColor: '#fff',
					backgroundColor: 'rgb(85, 102, 119)',
					bodyFontColor: '#fff',
					intersect: false,
					mode: 'index',
					borderWidth: 1,
					borderColor: 'rgb(85, 102, 119)'
				},
				hover: {
					intersect: false,
					mode: 'index'
				},
				animation: {
					duration: 500
				},
				legend: {
					display: false
				}
			}
		}
	},
	setChartDates: function(preset = 'w') {
		this.setState({ period: preset })
		if (preset === 'w') {
			let date = new Date()
			if (date.getDay() === 0) {
				jQuery('#chart-from').datepicker('setDate', -7)
				jQuery('#chart-to').datepicker('setDate', date)
			} else if (date.getDay() === 1) {
				jQuery('#chart-from').datepicker('setDate', date)
				jQuery('#chart-to').datepicker('setDate', 8 - date.getDay())
			} else {
				jQuery('#chart-from').datepicker('setDate', 1 - date.getDay())
				jQuery('#chart-to').datepicker('setDate', 8 - date.getDay())
			}
		} else if (preset === 'm') {
			let dateTo = new Date(new Date().getUTCFullYear(), new Date().getUTCMonth() + 1, 0)
			let dateFrom = new Date(new Date().getUTCFullYear(), new Date().getUTCMonth(), 1)
			jQuery('#chart-from').datepicker('setDate', dateFrom)
			jQuery('#chart-to').datepicker('setDate', dateTo)
		} else if (preset === 'y') {
			let dateFrom = new Date(new Date().getUTCFullYear(), 0, 1)
			let dateTo = new Date(new Date().getUTCFullYear(), 11, 31)
			jQuery('#chart-from').datepicker('setDate', dateFrom)
			jQuery('#chart-to').datepicker('setDate', dateTo)
		}
		if (preset !== 'c') {
			this.refreshChart()
		}
	},
	plotChart: function(labels, views, submissions, payments) {

		let commonOptions = {
			lineTension: 0.15,
			borderCapStyle: 'butt',
			borderJoinStyle: 'miter',
			pointRadius: 0,
			pointHoverRadius: 3,
			borderWidth: 2
		}

		let toPlotMain = {}
		toPlotMain.labels = labels
		toPlotMain.datasets = []
		toPlotMain.datasets.push(Object.assign({
			label: 'Views',
			borderColor: 'rgba(237, 133, 66, 1)',
			pointBackgroundColor: 'rgba(237, 133, 66, 1)',
			backgroundColor: 'rgba(237, 133, 66, 0.1)',
			data: views
		}, commonOptions))

		toPlotMain.datasets.push(Object.assign({
			label: 'Submissions',
			borderColor: 'rgb(100, 151, 243)',
			pointBackgroundColor: 'rgb(100, 151, 243)',
			backgroundColor: 'rgba(100, 151, 243, 0.1)',
			data: submissions
		}, commonOptions))

		if (payments.reduce((x, y) => x + y) > 0) {
			toPlotMain.datasets.push(Object.assign({
				label: 'Charges',
				borderColor: 'rgba(93, 168, 93, 1)',
				pointBackgroundColor: 'rgba(93, 168, 93, 1)',
				backgroundColor: 'rgba(93, 168, 93, 0.1)',
				data: payments
			}, commonOptions))
		}
		this.setState({ chartData: toPlotMain })
	},
	resetAnalytics: function() {
		if (confirm('Sure? This action can\'t be reversed.') !== true) {
			return false
		}
		jQuery.getJSON(`${FormCraftGlobal.ajaxurl}action=formcraft_reset_analytics`, function() {
			this.refreshChart()
		}.bind(this))
	},
	refreshChart: function() {
		let dateFrom = encodeURIComponent(jQuery.datepicker.formatDate('yy-mm-dd', jQuery('#chart-from').datepicker('getDate')))
		let to = encodeURIComponent(jQuery.datepicker.formatDate('yy-mm-dd', jQuery('#chart-to').datepicker('getDate')))
		let form = encodeURIComponent(jQuery('#chart-form').val())
		this.setState({ loading: true })
		jQuery.getJSON(`${FormCraftGlobal.ajaxurl}action=formcraft_get_stats&from=${dateFrom}&to=${to}&form=${form}`, function(response) {
			this.setState({ loading: false })
			let views = response.views.reduce((x, y) => x + y)
			let submissions = response.submissions.reduce((x, y) => x + y)
			let submissionsConversion = views === 0 ? 0 : parseFloat((submissions / views) * 100).toFixed(2)
			let payments = response.payments.reduce((x, y) => x + y)
			let paymentsConversion = views === 0 ? 0 : parseFloat((payments / views) * 100).toFixed(2)
			this.setState({
				views,
				submissions,
				submissionsConversion,
				payments,
				paymentsConversion
			})
			this.plotChart(response.labels, response.views, response.submissions, response.payments)
		}.bind(this))
	},
	componentDidMount: function() {

		let height = jQuery('#chart-cover').height()
		let width = jQuery('#chart-cover').width()
		this.setState({ chartHeight: height, chartWidth: width })

		let options = {}
		options.beforeShow = function(element) {
			jQuery(element).addClass('isActive')
			jQuery('#ui-datepicker-div').removeClass('ui-datepicker').addClass('formcraft-datepicker')
		}
		options.onClose = (e, element) => {
			jQuery(`#${jQuery(element).attr('id')}`).removeClass('isActive')
			if (jQuery(element).attr('id') === 'chart-from') {
				let minDate = jQuery('#chart-from').datepicker('getDate')
				jQuery('#chart-to').datepicker('option', 'minDate', minDate)
				jQuery('#chart-to').trigger('focus')
			}
			if (jQuery(element).attr('id') === 'chart-to') {
				this.refreshChart()
			}
		}
		options.onSelect = function() {
			jQuery(this).trigger('change').trigger('input')
		}
		options.nextText = '❯'
		options.prevText = '❮'
		options.hideIfNoPrevNext = true
		options.changeYear = true
		options.changeMonth = true
		options.showAnim = false
		options.yearRange = 'c-2:c+2'
		options.dateFormat = 'd M, yy'
		jQuery('#chart-from, #chart-to').datepicker(options)
		jQuery('#ui-datepicker-div').removeClass('ui-datepicker').addClass('formcraft-datepicker')
		this.setChartDates('m')
	},
	render: function() {
		return (
			<div>
				<div className='formcraft_card formcraft_table'>
					<div className='block-header'>
						<span className='block-title'>Form Analytics</span>
							{
								this.state.loading ?
								<div className='formcraft-loader'></div> :
								''
							}
							<button className='float-right formcraft-button small' onClick={this.resetAnalytics}>
								<i className='formcraft-icon'>show_chart</i>
								reset analytics data
							</button>
					</div>
					<div className='tr thead'>
						<div style={{ width: '40%' }}>
							<label style={{ textTransform: 'initial' }} className={this.state.period === 'w' ? 'isActive' : 'notActive'}>
								<input checked={this.state.period === 'w'} onChange={this.setChartDates.bind(null, 'w')} value='week' name='analytics-when' type='radio'/>
								1w
							</label>
							<label style={{ textTransform: 'initial' }} className={this.state.period === 'm' ? 'isActive' : 'notActive'}>
								<input checked={this.state.period === 'm'} onChange={this.setChartDates.bind(null, 'm')} value='month' name='analytics-when' type='radio'/>
								1m
							</label>
							<label style={{ textTransform: 'initial' }} className={this.state.period === 'y' ? 'isActive' : 'notActive'}>
								<input checked={this.state.period === 'y'} onChange={this.setChartDates.bind(null, 'y')} value='year' name='analytics-when' type='radio'/>
								1y
							</label>
							<label className={this.state.period === 'c' ? 'isActive' : 'notActive'}>
								<input checked={this.state.period === 'c'} onChange={this.setChartDates.bind(null, 'c')} value='custom' name='analytics-when' type='radio'/>
								Custom
							</label>
						</div>
						<div className='hasDivider' style={{ marginLeft: '-2.5%', width: '35%', display: this.state.period === 'c' ? 'inline-block' : 'none' }}>
							<input placeholder='(from)' type='text' id='chart-from' className='datepicker-field'/>
							<input placeholder='(to)' type='text' id='chart-to' className='datepicker-field'/>
						</div>
						<div style={{ width: '25%', float: 'right' }}>
							<select onChange={this.refreshChart} id='chart-form'>
							<option value='0'>All Forms</option>
								{this.props.forms.map((form) => {
									return (<option value={form.id} key={form.id}>{form.name}</option>)
								})}
							</select>
						</div>
					</div>
					<div className='tbody analytics_body' style={{ height: '37.45em' }}>
							<div className='block width-2'>
								<span className='one' style={{ borderColor: 'inherit' }}>{this.state.views}</span>
								<span style={{ color: 'rgba(237, 133, 66, 1)' }} className='two'>form views</span>
							</div>
							<div className='block width-2'>
								<span className='one'>{this.state.submissions}</span>
								<span style={{ color: 'rgb(100, 151, 243)' }} className='two'>submissions</span>
							</div>
							<div className='block width-2'>
								<span className='one'>{this.state.submissionsConversion}%</span>
								<span style={{ color: 'rgb(100, 151, 243)' }} className='two'>conversion</span>
							</div>
							<div style={{ opacity: this.state.payments > 0 ? '1' : '0.5' }} className='block width-2'>
								<span className='one'>{this.state.payments}</span>
								<span style={{ color: 'rgb(93, 168, 93)' }} className='two'>charges</span>
							</div>
							<div style={{ opacity: this.state.payments > 0 ? '1' : '0.5' }} className='block width-2'>
								<span className='one'>{this.state.paymentsConversion}%</span>
								<span style={{ color: 'rgb(93, 168, 93)' }} className='two'>conversion</span>
							</div>
							<div id='chart-cover'>
								{
									this.state.chartData ?
									<Line data={this.state.chartData} options={this.state.chartOptions} height={this.state.chartHeight} width={this.state.chartWidth}/> :
									''
								}
							</div>
					</div>
				</div>
			</div>
		)
	}
})


jQuery(document).ready(function() {
	ReactDOM.render(<FormCraftDashboard/>, document.getElementById('formcraft_dashboard'))
})
