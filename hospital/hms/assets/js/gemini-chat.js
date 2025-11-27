document.addEventListener('DOMContentLoaded', function () {
	const toggleBtn = document.getElementById('chatbot-toggle');
	const panel = document.getElementById('chatbot-panel');
	const closeBtn = document.getElementById('chatbot-close');
	const form = document.getElementById('chatbot-form');
	const input = document.getElementById('chatbot-input');
	const sendBtn = document.getElementById('chatbot-send');
	const messages = document.getElementById('chatbot-messages');
	const statusEl = document.getElementById('chatbot-status');
	const history = [];
	let isOpen = false;
	let isSending = false;

	if (
		!toggleBtn ||
		!panel ||
		!messages ||
		!form ||
		!input ||
		!sendBtn ||
		!statusEl
	) {
		return;
	}

	const sanitize = (text) =>
		text
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;')
			.replace(/\n/g, '<br>');

	const appendMessage = (role, text) => {
		const wrapper = document.createElement('div');
		wrapper.className = 'chat-message ' + role;
		const bubble = document.createElement('span');
		bubble.innerHTML = sanitize(text);
		wrapper.appendChild(bubble);
		messages.appendChild(wrapper);
		messages.scrollTop = messages.scrollHeight;
	};

	const setStatus = (text) => {
		if (!statusEl) return;
		if (text) {
			statusEl.textContent = text;
			statusEl.classList.add('active');
		} else {
			statusEl.textContent = '';
			statusEl.classList.remove('active');
		}
	};

	const togglePanel = (state) => {
		const nextState = typeof state === 'boolean' ? state : !isOpen;
		isOpen = nextState;
		panel.classList.toggle('open', isOpen);
		if (isOpen) {
			setTimeout(() => input && input.focus(), 100);
		}
	};

	appendMessage('assistant', 'नमस्ते! मैं Nisha  हूँ, बताइए मैं कैसे मदद कर सकता हूँ?');

	toggleBtn.addEventListener('click', function () {
		togglePanel();
	});

	if (closeBtn) {
		closeBtn.addEventListener('click', function () {
			togglePanel(false);
		});
	}

	const sendMessage = async (text) => {
		if (!text || isSending) {
			return;
		}
		isSending = true;
		sendBtn.disabled = true;
		setStatus('Nisha सोच रही है...');

		appendMessage('user', text);
		history.push({ role: 'user', text });

		try {
			const response = await fetch('api/gemini-chat.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
				},
				credentials: 'same-origin',
				body: JSON.stringify({ message: text, history }),
			});

			const data = await response.json();

			if (!response.ok || data.error) {
				throw new Error(data.error || 'अज्ञात त्रुटि');
			}

			const reply = (data.reply || 'माफ़ कीजिए, अभी उत्तर उपलब्ध नहीं है।').trim();
			appendMessage('assistant', reply);
			history.push({ role: 'assistant', text: reply });
			setStatus('उत्तर तैयार है।');
			setTimeout(() => setStatus(''), 2000);
		} catch (error) {
			const message = error.message || 'सर्वर त्रुटि';
			appendMessage('assistant', 'क्षमा करें, कोई समस्या आई: ' + message);
			setStatus(message);
		} finally {
			isSending = false;
			sendBtn.disabled = false;
		}
	};

	if (form) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			const text = (input.value || '').trim();
			if (!text) {
				return;
			}
			input.value = '';
			sendMessage(text);
		});
	}
});

