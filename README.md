# AI Search Chat – WordPress Plugin

**AI Search Chat** is a powerful and lightweight WordPress plugin that adds a ChatGPT-style AI search box to your website. Users can ask questions, upload files, and receive intelligent responses powered by OpenAI’s GPT-4o-mini model. It supports personalized greetings, session-based chat history, and markdown-rendered answers in a sleek and responsive interface.

---

## ✅ Features

- 💬 **AI-Powered Chat** using OpenAI’s GPT-4o-mini for contextual and human-like responses.
- 📂 **File Upload Support** for images and documents with previews in the chat window.
- 🔄 **Session Persistence** to recognize returning users and personalize greetings.
- 🗂 **Chat History** stored per user using WordPress’s options API.
- 🧑‍💻 **User Identification** leveraging WordPress logged-in data or default “Guest User”.
- 🖌 **Markdown Rendering** via `marked.js` for enriched content display.
- 🎨 **Custom UI/UX** with smooth animations, scrollbars, and mobile-friendly design.
- 🔑 **Admin Settings** panel for entering OpenAI API key easily.
- ⚙ **Shortcode Support** — simply add `[ai_search]` wherever you want the chatbox.

---

## 📦 Installation

1. Download or clone this repository into your WordPress plugins directory (`/wp-content/plugins/`).
   ```bash
   git clone https://github.com/yourusername/ai-search-chat.git
2. Go to **Settings → AI Search**, enter your OpenAI API key, and save changes.
3. Add the shortcode `[ai_search]` on any page or post where you want the chatbox to appear.

---

## 🚀 Usage

Insert the following shortcode into your content:

```plaintext
[ai_search]


The AI chat interface will appear with file upload buttons, chat messages, and dynamic responses powered by OpenAI.

---

## ⚙ Configuration

### API Key

Get your API key from OpenAI.

Enter it under Settings → AI Search in the WordPress dashboard.

### Customization

The chat interface is styled using embedded CSS but can be extended or overridden using additional styles in your theme or plugin.

### User Data

Recognizes logged-in users and displays their name and initials.

Guests see a default avatar with “GU” label.

---

## 🧠 How It Works

- **Chat Interface**: Built with HTML, CSS, and JavaScript for real-time messaging.
- **AJAX Communication**: Uses `admin-ajax.php` for sending questions and retrieving answers without page reloads.
- **Markdown Rendering**: Answers are formatted using marked.js for better readability.
- **Session Storage**: Stores flags in the browser to manage greetings and session state.
- **WordPress Options API**: Stores chat history securely using hashed user identifiers.

---

## 📂 Included Files

- `ai-search.php`: Main plugin file with shortcode, AJAX handlers, settings page, and chat logic.
- Embedded CSS for styling the interface.
- JavaScript for user interaction, file uploads, and API requests.

---

## 🔒 Security

- All user inputs are sanitized using `sanitize_text_field`.
- API key is securely stored in the WordPress database.
- AJAX requests are protected and validated via WordPress’s mechanisms.

---

## 📜 License

This plugin is licensed under the MIT License. Feel free to use, modify, and distribute it freely.

---

## 📷 Screenshots (Optional but recommended)

- Chat Interface – A clean, dark-themed chatbox with file upload and message previews.
- Admin Settings – Where users can input their OpenAI API key.

---

## 📧 Contact

**Author**: Harshit Maheshwari  
Feel free to reach out for suggestions, bug reports, or collaboration.

---

## 📌 Notes

- Requires an OpenAI API key to function.
- Works best on modern browsers and WordPress versions.
- Not recommended for high-traffic sites without API usage limits in place.
