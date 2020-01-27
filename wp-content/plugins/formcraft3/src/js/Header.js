let React = window.React
let ReactDOM = window.ReactDOM
let createReactClass = require('create-react-class')

let Header = createReactClass({
	render() {
		return (
			<div className='formcraft_header'>
				<h1>FormCraft</h1>
				<span className='version'>v{FormCraftGlobal['version']}</span>
				<div className='FormCraft-Notices'>
					{
						FormCraftGlobal.notices.map((notice, index) => {
							return (
								<div key={index}>
								{
									notice.link ?
									<a href={notice.link} className={notice.className}>{notice.message}</a>
									:
									<span className={notice.className} dangerouslySetInnerHTML={{ __html: notice.message }}></span>
								}
								</div>
							)
						})
					}
				</div>
			</div>
		)
	}
})

module.exports = Header
