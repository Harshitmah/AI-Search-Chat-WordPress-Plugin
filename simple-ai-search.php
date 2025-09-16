<?php
/*
Plugin Name: AI Search Chat
Description: ChatGPT-like AI search box for your website. Use shortcode [ai_search].
Version: 7.3
Author: Harshit Maheshwari
*/

if (!defined('ABSPATH')) exit;

// === Shortcode ===
function ai_search_shortcode() {
    ob_start(); ?>
        <style>
        /* Custom scrollbar for chat window */
        #ai-search-messages::-webkit-scrollbar {
            width: 10px;
            background: #232b36;
        }
        #ai-search-messages::-webkit-scrollbar-thumb {
            background: #444a57;
            border-radius: 8px;
        }
        #ai-search-messages::-webkit-scrollbar-thumb:hover {
            background: #555b66;
        }
        #ai-search-messages {
            scrollbar-width: thin;
            scrollbar-color: #444a57 #232b36;
        }
        <style>
        :root {
            --next100-blue: #1e90ff;
            --next100-green: #00e676;
            --next100-red: #ff1744;
            --next100-bg: #181c24;
            --next100-dark: #10131a;
            --next100-gray: #232b36;
        }
        #ai-search-box {
            width: 90vw;
            max-width: 100%;
            min-width: 520px;
            margin: 40px auto;
            background: #000;
            border-radius: 18px;
            box-shadow: 0 2px 24px #0004;
            padding: 0 0 20px 0;
        }
        #ai-search-messages {
            background: var(--next100-dark);
            color: #fff;
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
            margin: 0 0 10px 0;
            padding: 24px 8px 16px 8px;
            border-radius: 18px 18px 0 0;
            display: none;
            scroll-behavior: smooth;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .ai-msg, .user-msg {
            min-width: 5% !important;
            max-width: 100% !important;
            width: fit-content;
            margin-bottom: 0;
            padding: 14px 20px;
            border-radius: 16px;
            font-size: 1.08em;
            line-height: 1.6;
            word-break: break-word;
            display: block;
            clear: both;
        }
        .user-msg {
            background: #ff1744;
            color: #fff;
            margin-top: 20px;
            margin-right: 0;
             box-shadow: 0 1px 8px #0002;
            width: fit-content;
            min-width: 85%;
            max-width: 90% !important;
        }
        .ai-msg {
            color: #fff;
            align-self: flex-start;
            text-align: left;
            /* border-bottom-left-radius: 6px; */
            margin-right: auto;
            margin-top: 20px;
            margin-left: 0;
            width: fit-content;
            min-width: 85%;
            max-width: 90% !important;
        }
        /* Remove old clearfix */
        #ai-search-messages:after { display: none; }
        #ai-search-form {
            display: flex;
            gap: 10px;
            padding: 16px;
            border-radius: 0 0 18px 18px;
            background: #313131ff;
            box-shadow: 0 -1px 4px #0001;
        }
        #ai-search-input {
            flex: 1;
            padding: 14px;
            border-radius: 8px;
            border: none;
            background: #313131ff;
            color: #fff;
            font-size: 1em;
        }
        #ai-search-input:focus {
            outline: 2px solid var(--next100-blue);
        }
        #ai-search-form button {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: #ff1744;
            color: #fff;
            font-size: 1.7em;
            cursor: pointer;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-top: 7px;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px #ff416c44;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
       
        </style>
    <div id="ai-search-box">
    <div id="ai-greeting-bar" style="padding: 24px 32px 0 32px; background: transparent !important;"></div>
        <div id="ai-search-messages"></div>
        <form id="ai-search-form" autocomplete="off">
            <span id="ai-upload-btn" style="width:44px;height:44px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:var(--next100-gray);color:#fff;font-size:1.7em;cursor:pointer;margin-right:8px;margin-top:7px;">+</span>
            <input type="file" id="ai-upload-input" style="display:none" multiple>
            <div id="ai-upload-preview" style="display:flex;gap:8px;align-items:center;"></div>
            <input type="text" id="ai-search-input" placeholder="How can I help you today?">
            <button type="submit" style="font-size: 1.1em;">🡱</button>
        </form>
    </div>
    <!-- Load marked.js for markdown rendering -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("ai-search-form");
        const input = document.getElementById("ai-search-input");
        const messages = document.getElementById("ai-search-messages");
        const greetingBar = document.getElementById("ai-greeting-bar");
        const uploadBtn = document.getElementById("ai-upload-btn");
        const uploadInput = document.getElementById("ai-upload-input");
        const uploadPreview = document.getElementById("ai-upload-preview");
        let userId = '';
    // Session state: greeting/chat history visibility
    const SESSION_KEY = 'ai_search_greeted';
    const SESSION_USED_KEY = 'ai_search_used';
        // File upload button logic
        uploadBtn.addEventListener('click', function() {
            uploadInput.click();
        });
        uploadInput.addEventListener('change', function() {
            uploadPreview.innerHTML = '';
            if (uploadInput.files.length > 0) {
                for (let i = 0; i < uploadInput.files.length; i++) {
                    const file = uploadInput.files[i];
                    const fileSpan = document.createElement('span');
                    fileSpan.style.display = 'flex';
                    fileSpan.style.alignItems = 'center';
                    fileSpan.style.background = '#222b';
                    fileSpan.style.borderRadius = '8px';
                    fileSpan.style.padding = '4px 10px';
                    fileSpan.style.fontSize = '0.98em';
                    fileSpan.style.color = '#fff';
                    fileSpan.style.marginRight = '4px';
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            fileSpan.innerHTML = `<img src='${e.target.result}' style='max-width:40px;max-height:40px;border-radius:6px;margin-right:8px;'> ${file.name}`;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        fileSpan.innerHTML = `<span style='font-size:1.1em;margin-right:6px;'>&#128206;</span> ${file.name}`;
                    }
                    uploadPreview.appendChild(fileSpan);
                }
            }
        });
        // User identification: use WP user if logged in, else ask for email
        let userName = '';
        <?php if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            echo 'userId = "wpuser_' . esc_js($current_user->ID) . '";';
            echo 'userName = "' . esc_js($current_user->display_name) . '";';
        } ?>
        // Greeting logic
        function getGreeting() {
            const now = new Date();
            const hour = now.getHours();
            if (hour < 12) return "Good morning";
            if (hour < 18) return "Good afternoon";
            return "Good evening";
        }
        function showGreeting() {
            if (userName) {
                // If user has chatted before in this session, show welcome back
                if (sessionStorage.getItem(SESSION_USED_KEY)) {
                    greetingBar.innerHTML = `<div style="text-align:center;margin-bottom:40px;font-size:2em;font-weight:600;color:#fff;background:transparent !important;">Hey, Welcome back ${userName}!</div>`;
                } else {
                    greetingBar.innerHTML = `<div style="text-align:center;margin-bottom:40px;font-size:2em;font-weight:600;color:#fff;background:transparent !important;">${getGreeting()}, ${userName}</div>`;
                }
            } else {
                greetingBar.innerHTML = '';
            }
        }
    // Initial state: show greeting, always hide chat history until user submits a message
    showGreeting();
    messages.style.display = "none";
        // Load chat history for this user
        function loadHistory() {
            if (!userId) return;
            fetch("<?php echo admin_url('admin-ajax.php'); ?>?action=ai_search_history&user_id=" + encodeURIComponent(userId))
                .then(res => res.json())
                .then(data => {
                    messages.innerHTML = '';
                    // Always keep chat history hidden until user submits a message
                    // Only render history, but do not show it
                    if (data.history && data.history.length) {
                        // User avatar logic (same as in submit)
                        let userAvatar = '';
                        <?php if (is_user_logged_in()) {
                            $current_user = wp_get_current_user();
                            $display_name = trim($current_user->display_name);
                            $initials = '';
                            if ($display_name) {
                                $parts = preg_split('/\s+/', $display_name);
                                $first = isset($parts[0][0]) ? strtoupper($parts[0][0]) : '';
                                $last = isset($parts[1][0]) ? strtoupper($parts[1][0]) : '';
                                $initials = $first . $last;
                            }
                            echo 'userAvatar = `<span style=\'display:inline-flex;width:36px;height:36px;border-radius:50%;background:#000;color:#fff;font-weight:500;font-size:1em;align-items:center;justify-content:center;margin-right:10px;box-shadow:0 1px 4px #0002;white-space:nowrap;\'> ' . esc_html($initials) . '</span>`;';
                        } else {
                            echo 'userAvatar = `<span style=\'display:inline-flex;width:36px;height:36px;border-radius:50%;background:#000;color:#fff;font-weight:500;font-size:1em;align-items:center;justify-content:center;margin-right:10px;box-shadow:0 1px 4px #0002;white-space:nowrap;\'>GU</span>`;';
                        } ?>
                        data.history.forEach(msg => {
                            if (msg.role === 'user') {
                                messages.innerHTML += `<div class=\"user-msg\"><div style='display:flex;align-items:flex-start;'>${userAvatar}<span>${msg.content}</span></div></div>`;
                            } else {
                                // Render markdown for AI messages
                                let aiHTML = window.marked ? marked.parse(msg.content) : msg.content;
                                messages.innerHTML += `<div class=\"ai-msg\">${aiHTML}</div>`;
                            }
                        });
                        // Do NOT show history yet
                        messages.style.display = "none";
                    }
                });
        }
        // If user is known, load history
        if (userId) loadHistory();
        form.addEventListener("submit", function(e) {
            e.preventDefault();
            let question = input.value.trim();
            if (!question && uploadInput.files.length === 0) return;
            // Remove greeting bar when user starts conversation
            greetingBar.innerHTML = '';
            // Show result area
            messages.style.display = "block";
            // Persist state for session: user has chatted
            sessionStorage.setItem(SESSION_KEY, '1');
            sessionStorage.setItem(SESSION_USED_KEY, '1');
            // Show user message as a bubble
            let userMsgHTML = '';
            // User avatar logic
            let userAvatar = '';
            <?php if (is_user_logged_in()) {
                $current_user = wp_get_current_user();
                $display_name = trim($current_user->display_name);
                $initials = '';
                if ($display_name) {
                    $parts = preg_split('/\s+/', $display_name);
                    $first = isset($parts[0][0]) ? strtoupper($parts[0][0]) : '';
                    $last = isset($parts[1][0]) ? strtoupper($parts[1][0]) : '';
                    $initials = $first . $last;
                }
                echo 'userAvatar = `<span style=\'display:inline-flex;width:36px;height:36px;border-radius:50%;background:#000;color:#fff;font-weight:500;font-size:1em;align-items:center;justify-content:center;margin-right:10px;box-shadow:0 1px 4px #0002;white-space:nowrap;\'>' . esc_html($initials) . '</span>`;';
            } else {
                echo 'userAvatar = `<span style=\'display:inline-flex;width:36px;height:36px;border-radius:50%;background:#000;color:#fff;font-weight:500;font-size:1em;align-items:center;justify-content:center;margin-right:10px;box-shadow:0 1px 4px #0002;white-space:nowrap;\'>GU</span>`;';
            } ?>
            if (question) {
                userMsgHTML += `<div style='display:flex;align-items:flex-start;'>${userAvatar}<span>${question}</span></div>`;
            }
            // Show uploaded files as user message (with image preview if image)
            if (uploadInput.files.length > 0) {
                for (let i = 0; i < uploadInput.files.length; i++) {
                    const file = uploadInput.files[i];
                    if (file.type.startsWith('image/')) {
                        const url = URL.createObjectURL(file);
                        userMsgHTML += `<div style='display:flex;align-items:center;background:#222b;border-radius:8px;padding:6px 12px;margin:4px 0;font-size:0.98em;color:#fff;'><img src='${url}' style='max-width:60px;max-height:60px;border-radius:6px;margin-right:8px;'> ${file.name}</div>`;
                    } else {
                        userMsgHTML += `<div style='display:flex;align-items:center;background:#222b;border-radius:8px;padding:6px 12px;margin:4px 0;font-size:0.98em;color:#fff;'><span style='font-size:1.1em;margin-right:6px;'>&#128206;</span> ${file.name}</div>`;
                    }
                }
            }
            if (userMsgHTML) {
                messages.innerHTML += `<div class=\"user-msg\">${userMsgHTML}</div>`;
            }
            input.value = "";
            uploadInput.value = "";
            uploadPreview.innerHTML = '';
            messages.scrollTo({ top: messages.scrollHeight, behavior: "smooth" });
            // Call AJAX (does not send files, just question)
            if (question) {
                fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
                    method: "POST",
                    headers: {"Content-Type": "application/x-www-form-urlencoded"},
                    body: "action=ai_search&question=" + encodeURIComponent(question) + "&user_id=" + encodeURIComponent(userId)
                })
                .then(res => res.json())
                .then(data => {
                    // Render markdown in AI response using marked.js
                    let formatted = window.marked ? marked.parse(data.answer) : data.answer;
                    messages.innerHTML += `<div class=\"ai-msg\">${formatted}</div>`;
                    messages.scrollTo({ top: messages.scrollHeight, behavior: "smooth" });
                    // Do NOT call loadHistory() here, or it will overwrite the just-rendered markdown
                })
                .catch(err => {
                    messages.innerHTML += `<div class=\"ai-msg\" style='color:#ff6b6b;background:#2a1a1a;'>Error: Could not get response.</div>`;
                    messages.scrollTo({ top: messages.scrollHeight, behavior: "smooth" });
                });
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('ai_search', 'ai_search_shortcode');

// Store and retrieve chat history per user
add_action('wp_ajax_ai_search_history', function() {
    $user_id = sanitize_text_field($_GET['user_id'] ?? '');
    $history = get_option('ai_search_history_' . md5($user_id), []);
    wp_send_json(['history' => $history]);
});

// === AJAX handler ===
add_action('wp_ajax_ai_search', 'ai_search_handler');
add_action('wp_ajax_nopriv_ai_search', 'ai_search_handler');
function ai_search_handler() {
    $question = sanitize_text_field($_POST['question']);
    $user_id = sanitize_text_field($_POST['user_id'] ?? '');
    $api_key = get_option('ai_search_api_key');
    if (!$api_key) {
        wp_send_json(['answer' => '⚠️ No API key set. Please configure it in Settings → AI Search.']);
    }
    // Custom logic: If the question is about an image, logo, or brand colors, give a more helpful answer
    $q_lower = strtolower($question);
    if (strpos($q_lower, 'logo') !== false || strpos($q_lower, 'image') !== false || strpos($q_lower, 'brand color') !== false || strpos($q_lower, 'brand colours') !== false || strpos($q_lower, 'brand colors') !== false) {
        $answer = "I'm unable to view or analyze images or logos directly. However, if you describe the logo, image, or provide the brand name, I can help you identify typical colors, design elements, or provide information based on your description.";
        // Save to history
        $history = get_option('ai_search_history_' . md5($user_id), []);
        $history[] = ["role" => "user", "content" => $question];
        $history[] = ["role" => "assistant", "content" => $answer];
        update_option('ai_search_history_' . md5($user_id), $history);
        wp_send_json(['answer' => $answer]);
    }
    // Retrieve history for context
    $history = get_option('ai_search_history_' . md5($user_id), []);
    $messages = [];
    $messages[] = ["role" => "system", "content" => "You are an AI assistant that knows everything about this website and helps users in a clear way."];
    foreach ($history as $msg) {
        $messages[] = $msg;
    }
    $messages[] = ["role" => "user", "content" => $question];
    $response = wp_remote_post("https://api.openai.com/v1/chat/completions", [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body' => json_encode([
            "model" => "gpt-4o-mini",
            "messages" => $messages,
            "max_tokens" => 1200,
        ]),
        'timeout' => 30,
    ]);
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        wp_send_json(['answer' => 'Error: Could not reach OpenAI API. ' . esc_html($error_message)]);
    }
    $body = wp_remote_retrieve_body($response);
    if (!$body) {
        wp_send_json(['answer' => 'Error: No response from OpenAI API.']);
    }
    $data = json_decode($body, true);
    if (!is_array($data) || !isset($data['choices'][0]['message']['content'])) {
        $api_error = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown API error.';
        wp_send_json(['answer' => 'Error: Invalid response from OpenAI API. ' . esc_html($api_error)]);
    }
    $answer = $data['choices'][0]['message']['content'];
    // Save new message and response to history
    $history[] = ["role" => "user", "content" => $question];
    $history[] = ["role" => "assistant", "content" => $answer];
    update_option('ai_search_history_' . md5($user_id), $history);
    wp_send_json(['answer' => $answer]);
}

// === Admin Settings Page for API Key ===
add_action('admin_menu', function() {
    add_options_page('AI Search Settings', 'AI Search', 'manage_options', 'ai-search-settings', 'ai_search_settings_page');
});

function ai_search_settings_page() {
    ?>
    <div class="wrap">
        <h1>AI Search Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('ai_search_settings_group');
            do_settings_sections('ai-search-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

add_action('admin_init', function() {
    register_setting('ai_search_settings_group', 'ai_search_api_key');
    add_settings_section('ai_search_main', 'OpenAI API', null, 'ai-search-settings');
    add_settings_field('ai_search_api_key_field', 'API Key', 'ai_search_api_key_field_html', 'ai-search-settings', 'ai_search_main');
});

function ai_search_api_key_field_html() {
    $value = get_option('ai_search_api_key', '');
    echo '<input type="text" name="ai_search_api_key" value="' . esc_attr($value) . '" style="width:400px">';
}
