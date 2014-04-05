<div id="tab1-page-content">
			<div id="top-pane">
				Site Root : <input type="text" id="txt-site-root" value="<?php echo base_url();?>index.php"></input>
				<p>This must point to codeigniter's index.php</p>
				Module:<select id="select-module"></select> Action:<select id="select-action"></select>
			</div>
			<div id="middle-pane">
				<div id="api-module">
					<h3 id="api-data-module">Module:</h3>
					<p id="api-module-description">description</p>
				</div>
				<div id="right-pane">
					<h4>Response:</h4> 
					<div id="api-output-container">
						<div id="api-output">
							output
						</div>
					</div>
					<br />
					<code id="output-time-elapsed">Time elapsed: 0.00 ms</code>
				</div>
				<div id="left-pane">
					
					<div id="api-data">
						<h3 id="api-data-name">Action:</h3>
						<b><span>METHOD: </span></b><code id="api-data-method"></code><br />
						<b><span>URL: </span></b><code id="api-data-url"></code>
						<p id="api-action-description">description</p>
						<h4>Params</h4>
						<table id="table-param-description">
						
						</table>
						<br />
						<table id="table-param-container" border="1" cellspacing="0" cellpadding="0">
						
						</table>
						<br>
						<button class="btn btn-primary" id="btn-test-api">Send Request</button>
						<div id="sending-status"></div>
						<h4>Output</h4>
					</div>
				</div>
			</div>
			<div id="bottom-pane">
				<br />
				<h4>Log:</h4>
				<div id="log-box">
					
				</div>
			</div>
		</div>