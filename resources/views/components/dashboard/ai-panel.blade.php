<div class="fixed bottom-2 right-3 z-50">
    <div class="bottom-15 absolute right-0 flex hidden max-h-[35rem] min-h-[20rem] w-80 flex-col rounded-lg border border-gray-200 bg-white shadow-xl sm:w-96" id="ai-panel">
        <div class="flex items-center justify-between rounded-t-lg border-b bg-gray-50 px-4 py-2">
            <h3 class="text-lg font-semibold text-gray-800">AI Agent</h3>
            <button class="text-gray-500 hover:text-gray-800" id="close-panel-btn">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="flex-grow overflow-auto p-4" id="chat-container">
            <div class="mb-4 flex items-start gap-2.5">
                <div class="leading-1.5 flex w-full max-w-[320px] flex-col rounded-e-xl rounded-es-xl border-gray-200 bg-gray-100 p-4">
                    <p class="text-sm font-normal text-gray-900">Halo! Tuliskan topik yang ingin Anda diskusikan!</p>
                </div>
            </div>
        </div>

        <div class="rounded-b-lg border-t bg-white p-4">
            <form class="relative" id="ai-form">
                <input class="w-full rounded-full border bg-white py-2 pl-4 pr-12 text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500" id="prompt-input" type="text" placeholder="Tulis topik..." required>
                <button class="absolute inset-y-0 right-0 flex h-full w-12 items-center justify-center text-blue-600 hover:text-blue-800" type="submit">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <button class="rounded-full bg-blue-600 px-4 py-3 text-white shadow-lg hover:bg-blue-700 focus:bg-blue-600/80" id="ai-agent-btn">
        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.25 21.75V19.5H9.75V21.75H7.5V19.5C6.4 19.5 5.505 19.1025 4.8075 18.3075C4.11 17.5125 3.75 16.59 3.75 15.375V9.375C3.75 8.16 4.11 7.2375 4.8075 6.4425C5.505 5.6475 6.4 5.25 7.5 5.25H16.5C17.6 5.25 18.495 5.6475 19.1925 6.4425C19.89 7.2375 20.25 8.16 20.25 9.375V15.375C20.25 16.59 19.89 17.5125 19.1925 18.3075C18.495 19.1025 17.6 19.5 16.5 19.5V21.75H14.25ZM7.875 12.75H9.375V11.25H7.875V12.75ZM11.25 12.75H12.75V11.25H11.25V12.75ZM14.625 12.75H16.125V11.25H14.625V12.75ZM7.5 3.75V2.25H9.75V3.75H7.5ZM14.25 3.75V2.25H16.5V3.75H14.25Z"></path>
        </svg>
    </button>
</div>

@push('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Seleksi Elemen
            const aiAgentButton = document.getElementById('ai-agent-btn');
            const aiPanel = document.getElementById('ai-panel');
            const closePanelButton = document.getElementById('close-panel-btn');
            const aiForm = document.getElementById('ai-form');
            const promptInput = document.getElementById('prompt-input');
            const chatContainer = document.getElementById('chat-container');

            // Fungsi Buka/Tutup Panel
            const togglePanel = () => {
                aiPanel.classList.toggle('hidden');
            };

            aiAgentButton.addEventListener('click', togglePanel);
            closePanelButton.addEventListener('click', togglePanel);

            // Fungsi untuk menambahkan pesan ke UI
            const addMessageToChat = (message, sender, fullContent = '') => {
                const messageWrapper = document.createElement('div');
                const messageBubble = document.createElement('div');
                messageBubble.classList.add('leading-1.5', 'p-4', 'w-full', 'text-sm', 'font-normal');

                if (sender === 'user') {
                    messageWrapper.classList.add('flex', 'justify-end', 'mb-4');
                    messageBubble.classList.add('bg-blue-500', 'text-white', 'rounded-s-xl', 'rounded-ee-xl');
                    messageBubble.textContent = message;
                } else { // sender === 'ai'
                    messageWrapper.classList.add('flex', 'items-start', 'gap-2.5', 'mb-4');
                    messageBubble.classList.add('bg-gray-100', 'text-gray-900', 'rounded-e-xl', 'rounded-es-xl');

                    // Sisipkan konten HTML dari AI
                    messageBubble.innerHTML = message;

                    // Tambahkan tombol "Ganti Konten"
                    if (fullContent) {
                        const replaceButton = document.createElement('button');
                        replaceButton.textContent = 'Ganti Konten Editor';
                        // Simpan konten lengkap di data attribute
                        replaceButton.dataset.content = fullContent;
                        // Tambahkan kelas untuk styling dan identifikasi
                        replaceButton.className = 'replace-content-btn block w-full text-left mt-4 p-2 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors';

                        messageBubble.appendChild(replaceButton);
                    }
                }

                messageWrapper.appendChild(messageBubble);
                chatContainer.appendChild(messageWrapper);
                chatContainer.scrollTop = chatContainer.scrollHeight;
            };

            // Fungsi Indikator Loading
            const showLoadingIndicator = () => {
                const loadingHtml = `
                <div id="ai-loading" class="flex items-start gap-2.5 mb-4">
                    <div class="leading-1.5 flex w-full max-w-[120px] flex-col rounded-e-xl rounded-es-xl border-gray-200 bg-gray-100 p-4">
                        <div class="flex items-center space-x-2">
                            <div class="h-2 w-2 bg-gray-400 rounded-full animate-pulse [animation-delay:-0.3s]"></div>
                            <div class="h-2 w-2 bg-gray-400 rounded-full animate-pulse [animation-delay:-0.15s]"></div>
                            <div class="h-2 w-2 bg-gray-400 rounded-full animate-pulse"></div>
                        </div>
                    </div>
                </div>`;
                chatContainer.insertAdjacentHTML('beforeend', loadingHtml);
                chatContainer.scrollTop = chatContainer.scrollHeight;
            };

            const removeLoadingIndicator = () => {
                document.getElementById('ai-loading')?.remove();
            };

            aiForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const prompt = promptInput.value.trim();
                if (!prompt) return;

                addMessageToChat(prompt, 'user');
                promptInput.value = '';
                showLoadingIndicator();

                try {
                    const response = await fetch(`/dashboard/posts/generateAiContent?prompt=${encodeURIComponent(prompt)}&type=text`);
                    removeLoadingIndicator();

                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

                    const result = await response.json();

                    if (result.success && result.data) {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(`<root>${result.data}</root>`, "application/xml");
                        const title = doc.querySelector('AiTitle')?.textContent || '';
                        const mainContent = doc.querySelector('AiMain')?.innerHTML || '';

                        // Konten yang akan ditampilkan di bubble chat
                        const aiHtmlResponseForBubble = `
                            <h4 class="font-semibold mb-2 text-base">${title}</h4>
                            <div class="prose prose-sm max-w-none">${mainContent}</div>`;

                        // Konten lengkap yang akan disisipkan ke editor
                        const fullContentForEditor = `<h3>${title}</h3>${mainContent}`;

                        // Tampilkan respons AI di chat dengan data untuk tombol
                        addMessageToChat(aiHtmlResponseForBubble, 'ai', fullContentForEditor);
                    } else {
                        addMessageToChat('Maaf, terjadi kesalahan: ' + (result.message || 'Format respons tidak sesuai.'), 'ai');
                    }
                } catch (error) {
                    console.error("Fetch error:", error);
                    removeLoadingIndicator();
                    addMessageToChat('Gagal terhubung ke server. Silakan coba lagi.', 'ai');
                }
            });

            // Event Delegation untuk Tombol "Ganti Konten"
            chatContainer.addEventListener('click', (e) => {
                const replaceButton = e.target.closest('.replace-content-btn');

                if (replaceButton) {
                    // Cek apakah instance editor tersedia
                    const contentToSet = replaceButton.dataset.content;
                    // Gunakan API CKEditor untuk mengatur data
                    window.editorInstance.setData(contentToSet);

                    // Beri feedback visual
                    replaceButton.textContent = 'Konten Telah Diganti!';
                    replaceButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                    replaceButton.classList.add('bg-blue-600');

                    // Nonaktifkan tombol setelah diklik
                    replaceButton.disabled = true;

                }
            });
        });
    </script>
@endpush
