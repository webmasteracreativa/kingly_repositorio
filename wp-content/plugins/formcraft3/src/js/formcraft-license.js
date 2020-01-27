let React = window.React
let ReactDOM = window.ReactDOM
let moment = window.moment
let createReactClass = require('create-react-class')

import Header from './Header.js'
import Helpers from './Helpers.js'

if (FormCraftGlobal.ajaxurl.indexOf('?') > -1) {
	FormCraftGlobal.ajaxurl = `${FormCraftGlobal.ajaxurl}&`
} else {
	FormCraftGlobal.ajaxurl = `${FormCraftGlobal.ajaxurl}?`
}

let FormCraftLicense = createReactClass({
	getInitialState() {
		return {
			loading: false,
			email: FormCraftGlobal.email,
			key: FormCraftGlobal.key,
			failed: '',
			success: '',
			keyVerified: FormCraftGlobal.keyVerified !== '',
			keyInfo: {
				purchased: FormCraftGlobal.purchased,
				registered: FormCraftGlobal.registered,
				expires: FormCraftGlobal.expires,
				expires_days: Math.ceil(FormCraftGlobal.expires_days)
			}
		}
	},
	componentDidMount() {
		// console.log(FormCraftGlobal)
	},
	submitKey(e) {
		if (this.state.loading) return false
		this.setState({ loading: true, failed: '', success: '' })
		e.preventDefault()
		let licenseSource = {
			key: this.state.key,
			email: this.state.email,
			action: 'formcraft_verify_license'
		}
		this.serverRequest = jQuery.getJSON(`${FormCraftGlobal.ajaxurl}${jQuery.param(licenseSource)}`, (response) => {
			if (response.failed) {
				this.setState({
					failed: response.failed
				})
			} else if (response.purchased) {
				this.setState({
					success: 'License Key verified',
					keyVerified: true,
					keyInfo: {
						purchased: response.purchased,
						registered: response.registered,
						expires: response.expires,
						expires_days: Math.ceil(response.expires_days)
					}
				})
			}
		})
			.always(() => {
				this.setState({ loading: false })
			})
	},
	render() {
		return (
			<div>
				<Header/>
				<div className='formcraft-license-cover'>
					<form onSubmit={this.submitKey}>
						<div className='ValidKey'>
							{
								this.state.keyVerified ?
									<div>
										<i className='formcraft-icon'>check</i>
										Verified
									</div> : ''
							}
						</div>
						<input placeholder='Your Email' type='text' value={this.state.email} onChange={ (e) => this.setState({ email: e.target.value }) }/>
						<input placeholder='Your License Key' type='text' value={this.state.key} onChange={ (e) => this.setState({ key: e.target.value }) }/>
						<button className={`formcraft-button large loading-${this.state.loading}`}>
							<span>
								{
									this.state.keyVerified ?
										'Update Key Info' : 'Verify Key'
								}
								<div className='formcraft-loader'></div>
							</span>
						</button>
						{
							this.state.failed ?
								<div className='ResponseMessages'>
									<div className='IsRed'>{this.state.failed}</div>
								</div>
								: ''
						}
						{
							!this.state.failed && !this.state.loading && this.state.keyVerified && this.state.keyInfo ?
								<div className='KeyInfo'>
									<div style={{ display: this.state.moreInfo ? 'block' : 'none' }}>
										<div><span>Purchased On</span> <span className='float-right'>{this.state.keyInfo.purchased}</span></div>
										<div><span>Last Check</span> <span className='float-right'>{this.state.keyInfo.registered}</span></div>
										{
											this.state.keyInfo.expires_days < 0 ?
												<div className='IsRed'><span>Expired On</span> <span className='float-right'>{this.state.keyInfo.expires}</span></div>
												:
												<div><span>Expires On</span> <span className='float-right'>{this.state.keyInfo.expires}</span></div>
										}
									</div>
									<div className='MoreInfo' style={{ display: !this.state.moreInfo ? 'block' : 'none' }} onClick={() => this.setState({ moreInfo: true })}>
										More Info
									</div>
									<div className='ExpiresDays'>
										<div className={`DigitCover ${this.state.keyInfo.expires_days < 0 ? 'IsRed' : ''}`}>
											{
												this.state.keyInfo.expires_days.toString().split('').map((digit, index) => {
													return digit === '-' ? '' : <span key={index}>{digit}</span>
												})
											}
										</div>
										{
											this.state.keyInfo.expires_days < 0 ?
												<div className='DaysToGo IsRed'>days too late</div> :
												<div className='DaysToGo'>days left</div>
										}
									</div>
									<a className='formcraft-button green small' target='_blank' href={`http://formcraft-wp.com/buy/?addons=346&key=${this.state.key}`}>Renew License Key</a>
									{
										this.state.keyInfo.expires_days < 0 ?
											<div className='description-key'>renewing the license key gives you access to auto plugin updates and free customer support</div>
											:
											''
									}
								</div> : ''
						}
					</form>
				</div>
			</div>
		)
	}
})

jQuery(document).ready(function() {
	ReactDOM.render(<FormCraftLicense/>, document.getElementById('formcraft_dashboard'))
})
