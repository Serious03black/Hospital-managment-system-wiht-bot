<!-- <footer>
				<div class="footer-inner">
					<div class="pull-left">
			<span class="text-bold text-uppercase"> Hospital Management System</span>
					</div>
					<div class="pull-right">
						<span class="go-top"><i class="ti-angle-up"></i></span>
					</div>
				</div>
			</footer> -->

<div id="chatbot-panel" aria-live="polite">
	<div id="chatbot-header">
		<h5></h5>
		<button type="button" id="chatbot-close" aria-label="Close chat">&times;</button>
	</div>
	<div id="chatbot-messages" role="log"></div>
	<div id="chatbot-status"></div>
	<form id="chatbot-form">
		<input type="text" id="chatbot-input" placeholder="अपना सवाल लिखें..." autocomplete="off" />
		<button type="submit" id="chatbot-send">भेजें</button>
	</form>
</div>
<button type="button" id="chatbot-toggle">
	<i class="fa fa-comments"></i>
</button>

<style>
#chatbot-toggle {
	position: fixed;
	right: 24px;
	bottom: 24px;
	background: #28a745;
	color: #fff;
	border: none;
	border-radius: 999px;
	padding: 12px 24px;
	font-weight: 600;
	box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
	cursor: pointer;
	z-index: 1050;
}

#chatbot-toggle:hover {
	background: #218838;
}

#chatbot-toggle:focus {
	outline: none;
}

#chatbot-panel {
	position: fixed;
	right: 24px;
	bottom: 90px;
	width: 320px;
	max-width: calc(100% - 32px);
	background: #ffffff;
	border-radius: 16px;
	box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
	display: none;
	flex-direction: column;
	overflow: hidden;
	z-index: 1050;
}

#chatbot-panel.open {
	display: flex;
}

#chatbot-header {
	background: #0d6efd;
	color: #fff;
	padding: 14px 16px;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

#chatbot-header h5 {
	margin: 0;
	font-size: 16px;
}

#chatbot-close {
	background: transparent;
	border: none;
	color: #fff;
	font-size: 18px;
	cursor: pointer;
}

#chatbot-messages {
	padding: 12px;
	height: 320px;
	overflow-y: auto;
	background: #f8f9fa;
}

.chat-message {
	margin-bottom: 12px;
	font-size: 13px;
	line-height: 1.4;
}

.chat-message.user {
	text-align: right;
}

.chat-message.user span {
	display: inline-block;
	background: #d1e7dd;
	color: #0f5132;
	padding: 8px 12px;
	border-radius: 14px 14px 0 14px;
}

.chat-message.assistant span {
	display: inline-block;
	background: #fff;
	padding: 8px 12px;
	border-radius: 14px 14px 14px 0;
	box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

#chatbot-form {
	border-top: 1px solid #e5e5e5;
	padding: 10px;
	display: flex;
	gap: 8px;
	background: #fff;
}

#chatbot-input {
	flex: 1;
	border: 1px solid #ced4da;
	border-radius: 999px;
	padding: 8px 14px;
	font-size: 13px;
}

#chatbot-send {
	border: none;
	background: #0d6efd;
	color: #fff;
	border-radius: 999px;
	padding: 8px 16px;
	cursor: pointer;
	font-weight: 600;
}

#chatbot-send:disabled {
	opacity: 0.7;
	cursor: not-allowed;
}

#chatbot-status {
	font-size: 12px;
	color: #6c757d;
	padding: 0 16px 10px;
	display: none;
}

#chatbot-status.active {
	display: block;
}
</style>

<script src="assets/js/gemini-chat.js"></script>