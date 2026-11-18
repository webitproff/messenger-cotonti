# Messenger Plugin 1.0.0 Guide for Cotonti

The Messenger 1.0.0 plugin is a Telegram-style messenger for a Cotonti site: personal dialogs, group chats by invitation, an unread counter in the header, and popup notifications on any page. 

---

[![Version](https://img.shields.io/badge/version-1.1.1-green.svg)](https://github.com/webitproff/messenger-cotonti/releases)
[![Cotonti Compatibility](https://img.shields.io/badge/Cotonti-v.1-orange.svg)](https://github.com/Cotonti/Cotonti)
[![PHP](https://img.shields.io/badge/PHP-8.5-purple.svg)](https://www.php.net/releases/8_5_0.php)
[![MySQL](https://img.shields.io/badge/MySQL-8.4-blue.svg)](https://www.mysql.com/)
[![Bootstrap v5.3.8](https://img.shields.io/badge/Bootstrap-v5.3.8-blueviolet.svg)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/license-BSD-blue.svg)](https://github.com/webitproff/messenger-cotonti/blob/main/LICENSE)

---

<img width="1672" height="941" alt="messenger-cotonti-by-webitproff" src="https://github.com/user-attachments/assets/22559c33-ea33-4205-ab9e-01ca520ab1c3" />

---

| Part | For whom | What's inside |
| --- | --- | --- |
| 1 | Regular registered site user | How to find a chat, write, invite someone, what the icons and counters mean, what limitations exist. No code. |
| 2 | Owner and chief administrator | Software and hardware requirements, installation, integration into site templates, settings, load, data storage, security, updates, checklists. |
| 3 | Cotonti extension developer | Architecture, hooks, DB schema, glossary of all functions, API reference, security analysis with found weaknesses, code examples, FAQ. |


### Conventions

- Button names on screen are written in **bold**.
- File paths and function names are written in `monospace`.
- "Recommendation" lines are advice, not requirements. "Limitation" lines describe what the plugin in version 1.0.0 cannot do.

## Part 1. User Guide

This part is written for a regular registered site visitor. No code or technical terms: only what you see on screen and which buttons to press.

### 1.1. What the Messenger Is and Why It's Needed

A messenger is private correspondence on the site. You can write to any other site member without leaving the site and without knowing their email or phone number. Everything works like familiar messengers: on the left is the list of your conversations, on the right is the conversation itself, at the bottom is the line where you type text.

What you can do:

- send personal messages to any registered site member;
- see that someone replied to you, even when you're reading a completely different page of the site;
- have a conversation with three or more people: invite another person into an already started chat;
- mute sounds and popups for conversations that don't interest you;
- see whether the other person has read your message and what they're typing right now.

What version 1.0.0 does not have (more in section 1.12): sending images and files, voice messages, editing and deleting sent messages, searching message text, blocking a correspondent.

### 1.2. Who Can Use It

Only members who have logged in to the site. If you're not logged in, when opening the messenger you'll see the message "To use the messenger, please log in" and a **Log in** button. Guests (visitors who haven't logged in) don't see the header link or the "Send a message" button. If you're logged in but don't see the messenger, the administrator may not have granted your group permissions to the plugin: contact the site administration.

### 1.3. Where the Messenger Is Located

Three paths lead to the messenger. Any of them brings you to the same window.

1. **The "Messages" link in the site header.** It's next to your name and the logout button (the exact location depends on the site's design). Next to it is a green circle with a number: that's how many unread messages and invitations you have. If there's nothing unread, no circle is shown.
2. **The round blue button with a paper plane in the bottom left corner of the screen.** It appears if the administrator hasn't placed a link in the header. Its purpose is the same: to open the messenger. It also shows the unread count.
3. **A popup notification.** When someone writes to you, a card slides out in the bottom right corner; clicking it opens the relevant conversation.

On another person's profile page there's also a **Send a message** button (see the next section).

### 1.4. How to Write Your First Message to Someone

The first message is most conveniently written from the profile.

1. Open the member's profile: click on their name in a comment, on the forum, in the user list, or anywhere else on the site where the name is a link.
2. On the profile page, find the blue button with a paper plane icon, **Send a message**. It's usually under the page heading. If the administrator has embedded it in the template, it may be in a different place in the person's information block.
3. Click it. A small window opens: **"Write: name"**. It has one large text field.
4. Type your message. If it's already long, a counter like `950/1024` appears below the field: that's the character count and the limit.
5. Click the blue **Send** button or press Ctrl+Enter (Cmd+Enter on Mac). The **Cancel** button closes the window without sending.
6. If everything worked, you'll hear a short send sound, and a **"Message sent"** notification with an **Open chat** link appears in the bottom right. Click it to go to the conversation. If you don't click, the message is still delivered, and the window disappears after a few seconds.

**Important:** the conversation (a chat with its own ID) is created at the moment the first message is sent. Until you send something, nothing appears for the other person, and you can safely close the window.

If you've already corresponded with this person, the **Send a message** button adds your message to the same conversation: a second separate chat with the same person is not created.

#### If the Button Isn't on the Profile

- You're viewing **your own** profile: you can't write to yourself.
- You're not logged in to the site.
- The person is blocked or hasn't confirmed registration: you can't write to them.
- Your group doesn't have write permission for the messenger (you can read but not write).

In such cases the site itself will explain the reason when you try to send a message, for example "User not found or unavailable."

### 1.5. The Messenger Main Screen

When you open the messenger (the "Messages" link in the header or the round button), you land on the main screen. On a computer it's divided into two parts; on a phone only one of them is shown.

**Left column — the list of your conversations.** At the top are:

- the heading and the **New chat** button (pencil or plus icon): use it to find a person by name and start a conversation;
- the sound toggle (speaker icon): turns messenger sounds on and off;
- a search field for your correspondents' names (searches among existing conversations, not message text).

Below are the conversations themselves, most recent on top. Each row contains:

- a round icon with the first letter of the name (for a group — a group icon);
- the correspondent's name or the group name;
- the time of the last message (today — hours and minutes, earlier — date);
- the beginning of the last message in gray;
- a green circle with a number if there are unread messages in the conversation;
- a crossed-out bell icon if you've muted notifications for this conversation;
- a green dot next to the icon if the correspondent is currently on the site.

**Right part — the open conversation.** Until you select something, it says you need to select a chat on the left or create a new one.

**Invitations** (when you've been invited to a group conversation) are shown in a separate block above the list: the name, who invited you, and two buttons — **Join** and **Decline**. Details in section 1.9.

The list updates itself approximately every few seconds, so there's no need to reload the page.

#### Phone and Narrow Window

On a narrow screen, either the list or one conversation is shown. You select a conversation — the list hides, and a **back** arrow appears in the top left of the conversation. Press it, and you see the list again. The browser's own "Back" button also works: it returns through the same steps.

#### Light and Dark Theme

The messenger follows the site's design. If the site is switched to a dark theme, the messenger is dark too. If the site doesn't have such a toggle, your device's setting is used (light or dark system).

### 1.6. The Conversation Window

Open any conversation. At the top is a panel with the correspondent's name, and below it in a gray line is the status: **online**, **last seen recently**, or "typing…". On the right of the panel are action buttons (bell, add person, for groups — rename and leave). Below the panel is the message feed, and at the bottom is the input field.

#### How to Read the Feed

- Your messages are on the right, colored. Others' messages are on the left, light.
- Above each new day's messages is a gray "pill" with the date ("Today," "Yesterday," or a number).
- Each message has its send time in small font at the bottom right.
- If you opened a conversation with unread messages, the feed automatically scrolls to the first unread one, and a **"Unread messages"** line appears above it. Everything below it you haven't seen yet.
- Links like `https://...` in the text are clickable and open in a new tab. Links without `http://` or `https://` remain plain text.
- Letter-based smileys are automatically converted to emoji images (see 1.7).
- Gray centered lines in the feed are service entries, for example "Anna invited Ivan," "Ivan joined," "Maria left," "Chat renamed." They're written by the messenger itself, not by people.

#### Checkmarks on Your Messages

| Icon | Meaning |
| --- | --- |
| One checkmark | The message is sent and saved on the site. The correspondent hasn't opened it yet. |
| Two checkmarks | The correspondent opened the conversation and read the message. |

In groups, checkmarks reflect reading by at least one participant (other than you).

#### If the Correspondence Is Long

The window initially shows the latest messages. To see earlier correspondence, click the gray **"Show earlier"** button at the very top of the feed: messages load in portions, and the feed won't "jump" — you'll stay where you were reading. If a new message arrives while you're reading history, the feed won't scroll automatically.

#### Correspondent Statuses

- **online** — the person was active on the site less than 5 minutes ago. Important: "online" means "their site page is open and working," not "they're looking at your message." A background tab also counts.
- **last seen today at 14:35** or **last seen yesterday at 21:10** — the time of last activity on the site.
- **last seen recently** — visited within the last 30 days, but not yesterday or today. **last seen a long time ago** — hasn't visited for over a month (or no data).
- **typing…** — the correspondent is typing right now. The hint fades about 5 seconds after the last key press.
- In a group chat, instead of this, the number of participants and how many of them are online is shown; in the participant list, invitees who haven't joined yet are marked "awaiting response."

### 1.7. How to Write Messages

The input field is at the bottom of the conversation window. To its left is the **Smileys** button, to its right is the blue round **Send** button with a paper plane icon.

#### Keys

| What to press | What happens |
| --- | --- |
| Enter | The message is sent |
| Shift + Enter | Line break, message not sent |
| Ctrl + Enter (Cmd + Enter on Mac) | Sending in the small "Write: name" window that opens from the profile |

The field grows in height as you type several lines, but not above about six lines; beyond that, scrolling appears inside the field. If you're typing in Chinese, Japanese, or another input-method way, the Enter you use to select a suggestion won't send the message.

#### What You Can Write

- Only plain text. Images, files, photos, and voice messages cannot be sent.
- Maximum **1024 characters** per message. The administrator may have set a lower limit (256 or 512). When fewer than 150 characters remain, a counter like `900/1024` appears below the field. If you exceed the limit, the message won't send, and you'll see an explanation: "Message too long."
- Spaces and line breaks at the beginning and end of the text are trimmed. An empty message (only spaces) cannot be sent.
- Text limits are also checked on the server, so they can't be bypassed.

#### How Fast You Can Write

At least one second must pass between two of your messages (the administrator may increase the pause). If you click "Send" too quickly, a hint appears: "Not so fast! Wait a second" — just click again in a moment. The text is not lost.

#### Smileys

Smileys can be added in two ways.

1. **The "Smileys" button** opens a panel of 32 images. Click the one you want — it's inserted where the cursor is. The panel closes when you click anywhere else.
2. **Typing letters.** Some combinations turn into an image when sent:

| You type | You get |
| --- | --- |
| `:)` or `:-)` | smile |
| `:(` or `:-(` | sadness |
| `:D` | wide smile |
| `;)` | wink |
| `:P` or `:p` | tongue |
| `<3` | heart |
| `:O` | surprise |
| `:*` | kiss |
| `XD` | laughing to tears |

The conversion happens once, at the moment of sending. If you don't want the combination replaced, put a space or some letter between the characters.

#### Links

Addresses starting with `http://` or `https://` automatically become clickable and open in a new tab. Addresses like `example.com` without the `http` prefix remain plain text: you'll have to copy them. Other types of links (for example, starting with `javascript:` or `ftp:`) never become clickable: this is for your safety.

**Security tip.** Before following a link from someone you don't know, think. The messenger doesn't check where the address leads. Don't enter your site password anywhere a chat link takes you.

#### Drafts

If you started writing a message but switched to another conversation, the started text is remembered. When you return, you'll find it in the field. But if you **reload the page or close the tab**, the draft is lost: it's stored only in the open window.

#### If the Message Didn't Send

| What you see | What to do |
| --- | --- |
| "No connection to the server" | Check your internet and click "Send" again. The text remains in the field. |
| "User not found or unavailable" | The person is deleted, blocked, or hasn't confirmed registration. You can't write to them. |
| "You can't write to yourself" | Choose another person. |
| "You need to log in to the site" | Your session has ended. Log in to the site again, open the messenger, and retry. |
| "You don't have access to this chat" | You were removed from the chat, it was deleted by the administrator, or you tried to open someone else's conversation. |

### 1.8. Notifications, Counters, and Sounds

The messenger notifies you of new messages in three ways, and you don't need to keep it open.

#### Header Counter on the Site

The green circle next to the "Messages" link (or on the round button) shows the sum of two things: how many unread messages you have and how many group chat invitations await a response. The number updates itself approximately every 5 seconds (the administrator may set a different interval). If you have the page open but the tab is minimized or hidden, the check runs less often: no more than once every 10 seconds.

The number is also added to the browser tab title, for example "(3) Site Name." Thanks to this, you'll notice a new message even if you're looking at another tab. If the number is greater than 99, "99+" is shown.

#### Popup Cards

When someone writes to you, a card slides out in the bottom right corner with the sender's name and the beginning of the message. Rules:

- **One** card per conversation. If several more messages arrive in it, the counter on the card grows rather than new cards appearing.
- The card **does not disappear by itself**. It stays until you click the cross in its top right corner or open the conversation itself. If you close the page and come back later, the card will wait for you.
- Click the card text to open that conversation.
- At most three cards are shown at once. If there are more conversations with new messages, a line "and N more" appears below them.
- The card disappears by itself as soon as you open the conversation, reply in it, or read the messages on another device or in another tab.
- For a group chat invitation, the card is special: it says "You've been invited to a chat," the name, who invited you, and two buttons — **Join** and **Decline**.

If you yourself send a message from someone else's profile, a short confirmation "Message sent" with an **Open chat** link appears in the bottom right for a few seconds.

Popup cards are shown on any page of the site, including the messenger itself. In the messenger, a card doesn't appear only for the conversation you currently have open (you're already seeing its messages). If someone from another conversation writes, the card appears, and clicking it switches you to that conversation without reloading the page. If the administrator has disabled popup notifications for the whole site, only the header counter and sound remain.

#### Sounds

The messenger uses several short sounds:

| Event | Sound |
| --- | --- |
| A message arrived when you're on another page or in another conversation | Double "ding-ding" |
| A message arrived in the conversation you currently have open | Quiet short "pop" |
| You sent a message | Light "whoosh" |
| You were invited to a group chat | Separate melodic signal |
| Error (e.g., message too long) | Short quiet signal |

Sounds don't appear immediately after page load: browsers prohibit sites from making sounds until the person has clicked or touched the screen at least once. So if you just opened the site and haven't clicked anywhere, the first sound may not play. This is normal, not a malfunction.

**How to turn off sound.** Open the messenger page and click the speaker icon at the top of the conversation list. The icon changes to show whether sound is on or off. The setting is saved in your browser: on another browser or device you'll have to repeat it, and if you clear the site data, it resets to "on." If the administrator has disabled sounds entirely, the icon won't be there.

#### Per-Conversation Notifications (Without Sound)

If a conversation bothers you, open it and click the bell button in the top panel: the button's tooltip says "Mute notifications." Then:

- no popup cards appear from this conversation;
- in the list on the left it has a crossed-out bell icon;
- unread messages still accumulate and are shown on the conversation and in the overall counter: you don't lose anything, you're just not distracted.

To restore notifications, click the same button again (tooltip "Unmute notifications").

### 1.9. Group Chats: Invitations, Joining, Leaving

There's no separate "Create group" button in the messenger. A group chat appears when you **invite a third person into an existing conversation**. This is by design: first you have a conversation, then you invite someone into it.

#### How to Invite a Person

1. Open a conversation (personal or group).
2. In the top panel, click the **Invite** button (person with a plus icon).
3. In the "Invite to chat" window, start typing a nickname or login. You need to enter **at least 2 characters**. A list of matching people appears below, no more than 8.
4. Click the person you want. They receive an invitation, and a gray line "You invited …" appears in the feed.
5. The window closes itself, and a confirmation "… invited to the chat" appears below.

The search list **will not include**: yourself; people who are already in this chat or already invited; blocked people and those who haven't confirmed registration. If a person isn't in the list, they don't meet these conditions or the name was typed with a mistake. The search is by nickname and login, not by message text.

#### What Happens When You Invite into a Personal Dialog

A personal dialog between two people **turns into a group chat**. A gray line "… turned the dialog into a group chat" appears in the feed. Note two important consequences:

- The invitee, **upon joining the chat, will see all previous correspondence**, including what was written before their invitation. The invitation window states this explicitly. If there's something personal in the dialog, think before inviting a third person.
- The dialog is no longer considered "personal": you can leave it, it has a name, you can invite more people to it. If later you or the correspondent click "Send a message" on each other's profiles again, a **new** personal dialog starts, and the group remains a separate chat.

#### What the Invitee Sees

The invitee receives:

- a green circle with a counter in the header (invitations are included in the total);
- a popup card "You've been invited to a chat" with the name, the inviter's name, and **Join** / **Decline** buttons;
- an "Invitations" block above the conversation list on the messenger page.

Until the person **clicks "Join," they don't see the correspondence** and can't write. The invitation doesn't disappear by itself: it waits until the person accepts or declines it.

- **Join.** The chat opens, a line "… joined the chat" appears in the feed, and you see the entire history.
- **Decline.** The invitation disappears. In the chat, participants see the line "… declined the invitation." The invitation can be sent again.

If the chat is already full (by default up to 20 people, counting invitees who haven't joined yet), when trying to invite you'll see "The chat already has the maximum number of participants." If you try to invite someone who's already in the chat or already invited, you'll see "This user is already in the chat" or "This user has already been sent an invitation."

#### Group Name and Participant List

- Click the chat name in the top panel (or the "Chat info" icon) to open the participant list. It shows who's online, and below is a section "Invited but haven't joined yet" marked "awaiting response."
- **Any participant** can rename the chat, not just the creator. The new name is visible to everyone, and a line "… renamed the chat: 'New name'" appears in the feed. Rename carefully: only the next rename can undo it.

#### How to Leave a Group

1. In the chat window, open the chat info and click **Leave chat**.
2. The button changes to the question **Really leave?**. Click again if you're sure. This protects against accidental clicks.
3. You land on the main screen, and the others see the line "… left the chat."

After leaving, you **no longer see the correspondence of this group**, and you can't return without a new invitation. If the last person leaves the chat, the entire chat with its correspondence is deleted without the possibility of recovery. You **cannot leave a personal dialog** (you'll get the message "You can't leave a personal dialog"): if you don't want to continue the conversation, mute its notifications.

#### Muting Notifications

In the top panel of a conversation there's a bell button. Click it: a short confirmation "Notifications for this chat are off" appears. The chat continues to work, messages accumulate, but there will be no popup cards. Clicking again turns notifications back on. This is the only thing you can do if a persistent correspondent from a personal dialog writes to you: complaints and blocking are not provided in version 1.0.0 (see 1.12).

### 1.10. Capability Boundaries: Summary Table

| Parameter | Default value | Who can change |
| --- | --- | --- |
| Length of one message | 1024 characters | Administrator: 256, 512, or 1024 |
| Pause between your messages | 1 second | Administrator |
| Participants in one chat | 20 (including invitees) | Administrator |
| Message content | Text only, smileys, and links | Cannot be changed |
| Person search | From 2 characters, up to 8 results | Cannot be changed |
| Speed of new message appearance | About 1–2 seconds in an open chat; about 5 seconds on other pages | Administrator (for other pages) |
| Popup cards | No more than 3 at once | Cannot be changed |
| Number of chats | Not limited by the program | Administrator (overall disk limit) |
| Correspondence retention period | Not limited by the program | Administrator can delete old correspondence |

### 1.11. Privacy and Security Rules

#### Who Sees Your Correspondence

- **Chat participants** see it — and only them on the site.
- Correspondence is stored on the site's server **in plain text**: this is not an end-to-end encrypted messenger. The server's technical administrator theoretically has access to the database. Don't send passwords, SMS codes, bank card data, or documents through the chat.
- The plugin's admin panel shows **only statistics and chat names** (who the creator is, how many participants and messages), but not the message text. The administrator can delete a chat entirely or old correspondence.

#### What Others See About You

- Your name (nickname) and site avatar.
- Your "online"/"last seen…" status in a conversation with you. It cannot be disabled in version 1.0.0.
- That you're typing right now, and whether you've read a message (two checkmarks).

#### Who Can Write to You

Any registered site member who **can find you** (you're not blocked and have confirmed registration). You cannot forbid incoming messages or invitations. If someone writes unwanted things to you, mute the chat notifications and report it to the site administration: they can delete the chat.

#### Practical Tips

- Don't follow unfamiliar links; the messenger doesn't check them.
- Never tell anyone your password. Site administrators don't ask for it.
- Don't leave the messenger open on someone else's computer. After working, click "Log out" in the site header.
- Remember that the correspondent can copy your correspondence.

### 1.12. What Version 1.0.0 Doesn't Have (So You Don't Look for It)

- Sending images, files, voice and video messages.
- Editing and deleting **sent** messages (including "delete for me"). If you made a mistake, send a correction.
- Replies to a specific message, forwarding, pinning, reactions.
- Text formatting (bold, italic, etc.): everything remains plain text.
- Searching message text (there's only search by names).
- Blocking a person, reporting a message, "who can write to me" settings.
- Email or phone notifications when you're not on the site. The messenger only notifies you of a new message when you open the site.
- A separate "Create group" button and a group chat without an initial conversation.
- Deleting a chat by the user themselves (it's deleted by the administrator or disappears when the last participant leaves).
- Calls.

Messages arrive with a delay of a few seconds — this is not instant delivery like in Telegram. The messenger checks for new messages at regular intervals, so on a site page where you're just reading an article, a new message may not appear immediately but after a few seconds.

### 1.13. What to Do If Something Doesn't Work

| Problem | What to check |
| --- | --- |
| I don't see the "Messages" link | Are you logged in to the site? Check if there's a round blue button in the bottom left corner. If neither is there, contact the administrator: your group may not have permissions. |
| There's no "Send a message" button on the profile | Are you on your own profile? Are you logged in? Is the person not blocked? If everything is fine, try refreshing the page and clearing your browser cache. |
| Messages don't arrive, the counter doesn't change | Check your internet. Wait 10–15 seconds (the check isn't instant). Refresh the page. If that doesn't help, log out and log back in to the site. |
| No sound | Click anywhere on the page (the browser prohibits sound until the first click); check the speaker icon in the chat list and the device volume. |
| The popup card doesn't disappear | This is by design: it stays until you click the cross or open the chat. |
| I didn't find the person in the search | Enter at least 2 characters of the nickname or login. The person may already be in this chat, blocked, or haven't confirmed registration. |
| There are characters in the field but it won't send | Check the character counter: you may have exceeded the limit. |
| "No connection to the server" | Temporary network or server problem. Wait a minute and retry. The message text remains in the field. |
| Dark text on a dark background | Report it to the site administrator: their theme doesn't combine well with the messenger. |

### 1.14. Quick User Memo

1. To write to a person, open their profile and click **Send a message**.
2. To continue a conversation, click **Messages** in the site header or the round blue button.
3. Enter sends, Shift+Enter makes a line break.
4. One checkmark — sent, two — read.
5. The card in the bottom right stays until you close it with the cross.
6. You can invite a third person with the **Invite** button in a conversation; the invitee sees the entire history after joining.
7. The bell turns off conversation notifications, the speaker in the list turns off sounds.
8. You can leave a group, but not a personal dialog.
9. Don't send passwords or card data in the chat.

## Part 2. Owner and Chief Administrator Guide

This part is for the person responsible for the site: deciding whether to install the plugin, on which server it will run, who gets access, and what to do if something goes wrong. Almost no code is needed here, but technical terms are inevitable; each is explained at first mention.

### 2.1. What You Get and What You Don't Get

The `messenger` plugin adds Telegram-style private correspondence between registered users to a Cotonti site:

- personal dialogs and group chats (a group arises when a third person is invited into a dialog);
- an unread counter in the site header and in the browser tab title;
- popup cards in the bottom right on any page of the site;
- sounds, "online" and "typing" statuses, light and dark theme, phone adaptation;
- a management page in the admin panel: statistics, chat list, deletion, old cleanup.

What it doesn't have (honestly, so there are no unpleasant surprises): images and files, voice, calls, editing and deleting sent messages, blocking a correspondent and complaints, email and phone notifications, correspondence encryption, message text search, "who can write to me" settings. All of this is discussed in sections 2.9 and 3.9: some items can be added by modification.

The plugin **does not require a separate server**, daemons, Node.js, Redis, or WebSocket.
---
> **It runs on the same PHP and the same database as the site. The price of this simplicity is "polling": each user's browser regularly asks the server whether there's anything new (see section 2.6). This determines the load.**

### 2.2. Software Requirements

| What | Requirement | Comment |
| --- | --- | --- |
| Cotonti | 0.9.26 or newer (stated in README, verified on a new core version where plugin output goes through `$pluginBody`) | Older 0.9.x versions were not tested |
| PHP | 8.0 and above (development and tests on 8.5) | Requires `mbstring`, `json`, `pdo_mysql` extensions — they're available on almost all hostings |
| Database | MySQL 5.7+ or MariaDB 10.3+ with InnoDB tables | `utf8mb4` encoding is required, otherwise emoji in messages won't be saved (see FAQ in Part 3) |
| Table prefix | `cot_` | The plugin's installation file hard-codes the `cot_` prefix. If the site has a different prefix, you'll need to edit manually (see 2.4) |
| JavaScript in the browser | Enabled | The messenger is a JavaScript page; without it the user will only see a request to enable it |
| Modern browser | Chrome, Firefox, Safari, Edge of recent years | Requires `fetch` and Web Audio capabilities. Old browsers (e.g., Internet Explorer) are not supported |
| Bootstrap | Not needed | The plugin brings its own styles; Bootstrap 5.3 is used only to detect the dark theme via `data-bs-theme` |
| Site template | Output of `{FOOTER_RC}` in the footer is mandatory | That's where the plugin adds its script |
| HTTPS | Recommended | Not a plugin requirement, but correspondence goes as plain text over HTTP if there's no HTTPS |

**Important about verification.** Scenario tests (49 logic checks on a test database) and interface testing in an automated browser (no errors) were performed in a test environment. On your specific theme, hosting, and PHP version, you need to verify behavior yourself using the checklist in section 2.10.

### 2.3. Hardware and Hosting Requirements

The plugin doesn't need "special" hardware. The key parameter is not disk space but **the number of requests per second your PHP can handle**, because every open user site regularly contacts the server. Detailed calculation in section 2.6. Here are guidelines (these are estimates, not measurements on your site):

| Site | Simultaneously online | What will work |
| --- | --- | --- |
| Small | up to 50 | Regular shared hosting or a VPS with 1 core and 1 GB RAM |
| Medium | 50–300 | VPS 2 cores, 2–4 GB, PHP-FPM, OPcache, polling interval 8–15 seconds |
| Large | 300 and more | VPS 4+ cores or a dedicated server, PHP-FPM with enough processes, 15-second interval, and as it grows — modification (see 3.9) |

Mandatory condition for any size: **OPcache** enabled (compiled PHP code cache), otherwise each polling request will re-read and parse dozens of Cotonti core files.

### 2.4. Installation Step by Step

**Before starting, back up the database and the `plugins` directory.** Installation adds four tables; it doesn't change existing site data.

1. **Uploading files.** Unpack the `messenger-1.0.0.zip` archive. Inside is the `plugins/messenger` directory. Upload it entirely into your site's `plugins` directory (via FTP, SFTP, or the hosting panel). The result should be the path `plugins/messenger/messenger.setup.php`. A common mistake is nesting an extra directory (`plugins/messenger/messenger/...`): then the plugin won't appear in the list.
2. **File permissions.** Files need read permission for the web server. The plugin doesn't need write permission: it writes nothing to disk, everything is stored in the database.
3. **Installation in the admin panel.** Open "Administration → Extensions," find "Messenger" (code `messenger`, version 1.0.0), click "Install." If installation succeeded, the tables `cot_msgr_chats`, `cot_msgr_members`, `cot_msgr_messages`, `cot_msgr_notifs` will appear.
4. **Access permissions.** The answer to "who is allowed" is in section 2.5 below. By default members are given read and write, guests nothing.
5. **Settings.** Open the plugin settings and check the six parameters (table below).
6. **Connecting to the template.** This is an optional but desirable step: section 2.7.
7. **Verification.** Go through checklist 2.10 under two different accounts in two different browsers.

#### If the Table Prefix Is Not `cot_`

The installation file `setup/messenger.install.sql` and the removal file contain table names with the `cot_` prefix. The plugin code takes the prefix from core settings at runtime, so the problem only arises during installation. Solution: before installing, open both files in `plugins/messenger/setup/` with a text editor and replace `cot_msgr_` with your prefix plus `msgr_` (for example, `site_msgr_`). If you're not sure how to do this, ask a developer: an error here results in the installation "succeeding" but tables with the right names not existing.

#### If Installation Failed with an Error

- Open the error message: most often it's insufficient database user permissions to create tables (`CREATE`) or an incompatible prefix.
- Make sure the tables were actually created (e.g., via phpMyAdmin).
- After fixing, click "Uninstall" and then "Install" again: reinstallation is safe, tables are created with an "if not exists" condition.

### 2.5. Access Permissions

The plugin uses Cotonti's standard permission system: "Administration → Users → Permissions → Plugins → Messenger."

| Permission | What it means | Default value |
| --- | --- | --- |
| Read (R) | See the messenger, chat list, read, receive notifications and the counter | Members — yes, guests — no |
| Write (W) | Send messages, start dialogs, invite, join, leave, rename, mute notifications | Members — yes, guests — no |
| Administration (A) | Access to the management page "Administration → Other → Messenger" | Administrators only |

Practical consequences:

- **Guests see nothing.** Neither the header link, nor the profile button, nor popup cards.
- **"Read-only" mode for a group.** Give the group R but not W: such users can read their chats and receive counters, but cannot write, respond to invitations, or mute notifications. The "Send a message" button on profiles is not shown to them.
- **How to forbid the messenger to newcomers or a specific group.** Remove both R and W from that group. Existing correspondence remains in the database, but the user won't see it.
- **If a user doesn't have permission, nothing breaks**: all checks run on the server, and the browser script simply doesn't load.

### 2.6. Settings Parameters

Settings are opened from "Administration → Extensions → Messenger → Settings."

| Parameter | Allowed values | Default | What it affects |
| --- | --- | --- | --- |
| Maximum message length | 256, 512, 1024 | 1024 | Character limit per message. Lower — less database load and fewer "walls of text" |
| Minimum interval between messages | 0, 1, 2, 3, 5 seconds | 1 | Flood protection. 0 disables protection; not recommended |
| Maximum participants in a group | 5, 10, 20, 50, 100 | 20 | Both those who accepted the invitation and those who haven't responded yet are counted. The larger the group, the heavier each update |
| Background check interval on regular pages | 3, 5, 8, 15 seconds | 5 | **The main load lever** (see 2.6.1). The rarer — the fewer requests, but the longer to wait for the counter |
| Sounds | Yes/No | Yes | Enables sounds for everyone. The user can still turn them off on their side |
| Popup notifications | Yes/No | Yes | Enables cards in the bottom right. The header counter and sound remain regardless of this |

Note: the settings don't allow, for example, setting message length to 2000 or interval to 30 seconds. Allowed values are hard-coded in the selection list; to expand the range, you need to edit the `messenger.setup.php` file (see Part 3).

### 2.7. Integration into Site Templates

This is the main practical section. The plugin works "out of the box," but looks and behaves better if you manually place three tags in the theme template. A tag is a word in curly braces, for example `{HEADER_MESSENGER}`: Cotonti replaces it with a ready-made page fragment.

All edits are made in your **current theme's** files, in the `themes/THEME_NAME/` directory. Before editing, copy the original file (e.g., `header.tpl` → `header.tpl.bak`).

#### What Works Without Template Edits

| Element | Without edits | After edits |
| --- | --- | --- |
| "Messages" link with counter | A round blue button with a paper plane appears in the bottom left corner of the screen | The link is embedded in the site header next to the username |
| "Send a message" button on the profile | Inserted by the script right after the page heading (the first `h1`); if there's no heading — floats at the bottom left | The button stands where you place it in the person's information block |
| Plugin script and styles | Connected automatically if the theme outputs `{FOOTER_RC}` (styles are added to `<head>`) | No changes |

That is, template edits are a matter of visual neatness, not functionality. The only **mandatory** condition is described in step 3.

#### Step 1. "Messages" Link in the Header: `header.tpl`

Open `themes/THEME_NAME/header.tpl` and place the tag where the user links are output in the header (profile, logout). The tag is called `{HEADER_MESSENGER}`. Next to it is a second tag, `{HEADER_MESSENGER_URL}` — just the page address without formatting, if you want to style the link yourself.

Example for a Bootstrap 5 theme. The names of neighboring elements differ in each theme, so only the line with the tag matters: it needs to be placed inside the header block that is visible to a logged-in user (where your username and "Logout" link are output):

```
<!-- Inside the header block shown to a logged-in user -->
	<!-- IF {PHP|cot_plugin_active('messenger')} -->
	{HEADER_MESSENGER}
	<!-- ENDIF -->
<!-- ...your theme's own links remain here: profile, logout, etc. -->
```

Example with your own styling, via the address:

```
<!-- IF {PHP|cot_plugin_active('messenger')} -->
<a class="oc-menu-item" href="{HEADER_MESSENGER_URL}">
	<span class="oc-menu-icon"><i class="fa-brands fa-telegram"></i></span>
	<span class="oc-menu-text">
		<strong>{PHP.L.msgr_title}</strong>
	</span>
</a>
<!-- ENDIF -->
```

Remember: if you style the link yourself via `{HEADER_MESSENGER_URL}`, **the counter cannot appear in it**. It's tied to the link that the `{HEADER_MESSENGER}` tag outputs. If you need the counter, use that tag and adjust the appearance via CSS (see below).

What the tag outputs: a link with the class `msgr-hdr`, a paper plane icon, the word "Messages," and a green counter circle. For guests and people without read permission, the tag is empty, so it can be placed unconditionally without wrapping it in checks.

On a narrow screen (less than 760 pixels), the word "Messages" is hidden, leaving the icon and counter.

**How to check.** Log in to the site. The link should appear in the header, and the round button in the bottom left should not. If both appeared, the tag was placed in the wrong template block (one not output for logged-in users) or the file wasn't uploaded to the server.

#### Step 2. Button on the Profile: `users.details.tpl`

Open `themes/THEME_NAME/users.details.tpl`. Place `{USERS_DETAILS_MESSENGER}` where actions with the person or their data are shown, for example under the avatar. The `{USERS_DETAILS_MESSENGER_URL}` tag gives the address without formatting.

```
<!-- IF {PHP|cot_plugin_active('messenger')} AND {PHP.usr.id} > 0 -->
<div class="mb-3">{USERS_DETAILS_MESSENGER}</div>
<!-- ENDIF -->
```
or
```
<!-- IF {PHP|cot_plugin_active('messenger')} AND {PHP.usr.id} > 0 -->
<a href="{USERS_DETAILS_MESSENGER_URL}" 
class="oc-menu-item" 
title="{PHP.L.messenger_subtitle_desc}">
	<span class="btn-as-mega-menu-icon"><i class="fa-brands fa-telegram"></i></span>
	<span class="btn-as-mega-menu-text">
		<strong>{PHP.L.messenger_btn_write_title}</strong>
		<small>{PHP.L.messenger_subtitle}</small>
	</span>
</a>
<!-- ENDIF -->
```

The tag is empty in three cases: you're viewing **your own** profile; you don't have write permission for the plugin; the person is blocked or hasn't confirmed registration. In these cases the button shouldn't be shown, and that's normal.

Clicking the button opens a small window for the first message (not a new page). If JavaScript is disabled, the button works as a regular link to the page `?e=messenger&m=new&u=NUMBER`.

#### Step 3. Footer: `{FOOTER_RC}` — This Is Mandatory

The messenger script (responsible for the counter, popup cards, sounds, the entire chat interface) is added to the common `{FOOTER_RC}` variable. Open `themes/THEME_NAME/footer.tpl` and make sure the tag is there:

```
{FOOTER_RC}
```

It must be placed **before** the closing `</body>`. In standard Cotonti themes it's already there. In custom and heavily modified themes it's sometimes removed. A sign that the tag is missing: the header link is visible, but the number on it doesn't change, chat windows don't open, and the messenger page says "Loading…".

Check: open a site page, click "View page source" and find `MSGR_BOOT` and `messenger.js` there. If they're not there — the tag is missing in the footer, or you're viewing the page as a guest.

#### The Messenger Page Itself

The page `index.php?e=messenger` is styled by the template `plugins/messenger/tpl/messenger.app.tpl` and is output inside your site's regular layout (header, footer, menu — yours). To change the template safely without editing the plugin file, copy it to the theme:

```
themes/THEME_NAME/plugins/messenger/messenger.app.tpl
```

Cotonti first looks for the template in the theme, then in the plugin itself. This is the standard mechanism for overriding plugin templates; after updating the plugin, your copy won't be overwritten, but it also won't receive new changes — compare it manually during updates.

#### Appearance Tuning (CSS)

All styles are in `plugins/messenger/css/messenger.css`. You don't need to edit the plugin file: add your rules to the theme's CSS, they'll win if connected later. Colors are set by variables, for example `--tg-badge` (counter color), `--tg-accent` (accent color), `--tg-online` (online dot), `--tg-side` (chat list background), `--tg-sub` (gray text). The full list is at the beginning of the stylesheet.

```
:root {
  --tg-accent: #0d6efd;   /* messenger buttons and links color */
  --tg-badge: #dc3545;    /* red counter instead of green */
}
```

The header link's property: it inherits the text color of neighboring links (`color: inherit`), so in most themes it adapts itself.

#### Dark Theme

The messenger switches to dark styling in two cases: the page has `data-bs-theme="dark"` (this is what the Bootstrap 5.3 theme switcher does), or the device system has chosen a dark theme. If your site switches to dark theme in another way (with its own class on `body`), the messenger won't know about it; tell the developer (Part 3, FAQ).

#### If the Theme Is Non-Standard

- **A theme without a `users.details.tpl` file.** Tags aren't needed: the button places itself, but you don't choose its position.
- **A theme where the header is assembled not via `header.tpl`.** Not a problem: the round button will work. If you don't like it, the header needs modification.
- **A theme where `{FOOTER_RC}` is output before the main scripts are connected.** This doesn't break the chat: the plugin doesn't use jQuery or Bootstrap scripts.
- **Two site layouts (regular and mobile).** Edit both `header.tpl` files.

#### Tag Cheat Sheet

| Tag | File | What it outputs | Mandatory |
| --- | --- | --- | --- |
| `{HEADER_MESSENGER}` | `header.tpl` | "Messages" link with a live counter | No (there's a fallback round button) |
| `{HEADER_MESSENGER_URL}` | `header.tpl` | Just the messenger page address | No |
| `{USERS_DETAILS_MESSENGER}` | `users.details.tpl` | "Send a message" button | No (there's automatic insertion) |
| `{USERS_DETAILS_MESSENGER_URL}` | `users.details.tpl` | "Write to this person" address | No |
| `{FOOTER_RC}` | `footer.tpl` | Messenger script and data | **Yes** |


ReadMeMore:       https://abuyfile.com/market/cotonti/plugs/messenger
Support:          https://abuyfile.com/forums/cotonti/custom/
Source:           https://github.com/webitproff/messenger-cotonti
 
Date: 2026-11-18 
package messenger
version 1.1.1
author webitproff
copyright Copyright (c) 2026 webitproff | https://github.com/webitproff/messenger-cotonti
license BSD-3

___

# Русский


# Руководство по плагину Messenger 1.0.0 для Cotonti

Плагин Messenger 1.0.0 — это мессенджер в стиле Telegram для сайта на Cotonti: личные диалоги, групповые чаты по приглашению, счётчик непрочитанного в шапке и всплывающие уведомления на любой странице. Руководство разделено на три независимые части, и каждую можно читать отдельно.

| Часть | Для кого | Что внутри |
| --- | --- | --- |
| 1 | Обычный зарегистрированный пользователь сайта | Как найти чат, написать, пригласить человека, что значат значки и счётчики, какие есть ограничения. Без кода. |
| 2 | Владелец и главный администратор | Требования к ПО и железу, установка, интеграция в шаблоны сайта, настройки, нагрузка, хранение данных, безопасность, обновление, чек-листы. |
| 3 | Разработчик расширений Cotonti | Архитектура, хуки, схема БД, глоссарий всех функций, справочник API, разбор безопасности с найденными слабыми местами, примеры кода, FAQ. |



### Условные обозначения

- Название кнопки на экране написано **жирным**.
- Пути к файлам и имена функций написаны `моноширинным шрифтом`.
- Строки «Рекомендация» — это совет, а не требование. Строки «Ограничение» описывают то, чего плагин в версии 1.0.0 делать не умеет.

## Часть 1. Руководство пользователя

Эта часть написана для обычного зарегистрированного посетителя сайта. Никакого кода и технических терминов: только то, что вы видите на экране и на какие кнопки нужно нажимать.

### 1.1. Что такое мессенджер и зачем он нужен

Мессенджер — это личная переписка на сайте. Вы можете написать любому другому участнику сайта, не выходя с сайта и не зная его почты или номера телефона. Всё работает так же, как в привычных мессенджерах: слева список ваших разговоров, справа сам разговор, внизу строка, где вы набираете текст.

Что вы можете делать:

- писать личные сообщения любому зарегистрированному участнику сайта;
- видеть, что вам ответили, даже когда вы читаете совсем другую страницу сайта;
- вести разговор втроём и больше: пригласить в уже начатый чат ещё одного человека;
- отключать звук и всплывающие окна для разговоров, которые вас не интересуют;
- видеть, прочитал ли собеседник ваше сообщение, и что он сейчас печатает.

Чего в версии 1.0.0 нет (подробнее в разделе 1.12): отправки картинок и файлов, голосовых сообщений, редактирования и удаления отправленного, поиска по тексту переписки, блокировки собеседника.

### 1.2. Кто может пользоваться

Только вошедшие на сайт участники. Если вы не вошли, при открытии мессенджера вы увидите сообщение «Чтобы пользоваться мессенджером, войдите на сайт» и кнопку **Войти**. Гости (не вошедшие посетители) не видят ни ссылки в шапке, ни кнопки «Написать сообщение». Если вы вошли, но не видите мессенджера, возможно, администратор не выдал вашей группе права на плагин: обратитесь к администрации сайта.

### 1.3. Где находится мессенджер

К мессенджеру ведут три пути. Любой из них приводит в одно и то же окно.

1. **Ссылка «Сообщения» в шапке сайта.** Она стоит рядом с вашим именем и кнопкой выхода (точное место зависит от оформления сайта). Рядом с ней — зелёный кружок с числом: столько у вас непрочитанных сообщений и приглашений. Если непрочитанного нет, кружка не видно.
2. **Круглая синяя кнопка с бумажным самолётиком в левом нижнем углу экрана.** Она появляется, если администратор не поставил ссылку в шапку. Назначение у неё такое же: открыть мессенджер. На ней тоже горит число непрочитанного.
3. **Всплывающее уведомление.** Когда вам пишут, в правом нижнем углу выезжает карточка; щелчок по ней открывает нужный разговор.

На странице чужого профиля есть ещё кнопка **Написать сообщение** (см. следующий раздел).

### 1.4. Как написать первое сообщение человеку

Первое сообщение удобнее всего писать с профиля.

1. Откройте профиль участника: щёлкните по его имени в комментарии, на форуме, в списке пользователей или в любом другом месте сайта, где имя является ссылкой.
2. На странице профиля найдите синюю кнопку со значком самолётика **Написать сообщение**. Обычно она стоит под заголовком страницы. Если администратор встроил её в шаблон, она может находиться в другом месте блока с информацией о человеке.
3. Нажмите её. Откроется небольшое окно **«Написать: имя»**. В нём одно большое поле для текста.
4. Наберите сообщение. Если оно уже длинное, под полем появится счётчик вида `950/1024`: это число символов и предел.
5. Нажмите синюю кнопку **Отправить** или сочетание клавиш Ctrl+Enter (на Mac — Cmd+Enter). Кнопка **Отмена** закрывает окно без отправки.
6. Если всё получилось, вы услышите короткий звук отправки, а внизу справа появится сообщение **«Сообщение отправлено»** со ссылкой **Открыть чат**. Нажмите её, чтобы перейти в разговор. Если не нажимать, сообщение всё равно доставлено, а окно исчезнет через несколько секунд.

**Важно:** разговор (чат со своим номером) создаётся именно в момент отправки первого сообщения. Пока вы ничего не отправили, у собеседника ничего не появляется, и вы можете спокойно закрыть окно.

Если вы уже переписывались с этим человеком, кнопка **Написать сообщение** добавит ваше сообщение в тот же разговор: второго отдельного чата с тем же человеком не создаётся.

#### Если кнопки на профиле нет

- Вы смотрите **собственный** профиль: сам себе писать нельзя.
- Вы не вошли на сайт.
- Человек заблокирован или ещё не подтвердил регистрацию: ему написать нельзя.
- У вашей группы нет прав записи на мессенджер (вы можете читать, но не писать).

В таких случаях сайт сам подскажет причину при попытке отправить сообщение, например «Пользователь не найден или недоступен».

### 1.5. Главный экран мессенджера

Открыв мессенджер (ссылка «Сообщения» в шапке или круглая кнопка), вы попадаете на главный экран. На компьютере он разделён на две части, на телефоне показывается только одна из них.

**Левая колонка — список ваших разговоров.** Сверху находятся:

- заголовок и кнопка **Новый чат** (значок карандаша или плюса): через неё можно найти человека по имени и начать разговор;
- переключатель звука (значок динамика): включает и выключает звуки мессенджера;
- поле поиска по именам ваших собеседников (ищет среди уже имеющихся разговоров, а не по тексту сообщений).

Ниже идут сами разговоры, самые свежие сверху. Каждая строка содержит:

- круглый значок с первой буквой имени (для группы — значок группы);
- имя собеседника или название группы;
- время последнего сообщения (сегодня — часы и минуты, раньше — дата);
- начало последнего сообщения серым цветом;
- зелёный кружок с числом, если в разговоре есть непрочитанные сообщения;
- значок перечёркнутого колокольчика, если вы отключили уведомления этого разговора;
- зелёную точку у значка, если собеседник сейчас на сайте.

**Правая часть — открытый разговор.** Пока вы ничего не выбрали, там написано, что нужно выбрать чат слева или создать новый.

**Приглашения** (когда вас позвали в групповой разговор) показываются отдельным блоком над списком: название, кто пригласил и две кнопки — **Присоединиться** и **Отклонить**. Подробно — в разделе 1.9.

Список обновляется сам примерно раз в несколько секунд, поэтому страницу перезагружать не нужно.

#### Телефон и узкое окно

На узком экране показывается либо список, либо один разговор. Выбрали разговор — список скрылся, а в левом верхнем углу разговора появилась стрелка **назад**. Нажмите её, и вы снова увидите список. Кнопка «Назад» самого браузера тоже работает: она возвращает по тем же шагам.

#### Светлая и тёмная тема

Мессенджер повторяет оформление сайта. Если сайт переключён на тёмную тему, мессенджер тоже тёмный. Если сайт такого переключателя не имеет, берётся настройка вашего устройства (светлая или тёмная система).

### 1.6. Окно разговора

Откройте любой разговор. Сверху — панель с именем собеседника, под ним серой строкой статус: **в сети**, **был(а) недавно** или «печатает…». Справа в панели находятся кнопки действий (колокольчик, добавление человека, для групп — переименование и выход). Под панелью идёт лента сообщений, внизу — поле ввода.

#### Как читать ленту

- Ваши сообщения справа, цветные. Сообщения других — слева, светлые.
- Над сообщениями каждого нового дня стоит серая «таблетка» с датой («Сегодня», «Вчера» или число).
- У каждого сообщения справа внизу мелким шрифтом время отправки.
- Если вы открыли разговор с непрочитанными сообщениями, лента сама промотается к первому непрочитанному, а над ним появится строка **«Непрочитанные сообщения»**. Всё, что ниже неё, вы ещё не видели.
- Ссылки вида `https://...` в тексте нажимаются и открываются в новой вкладке. Ссылки без `http://` или `https://` остаются обычным текстом.
- Смайлики из букв автоматически превращаются в картинки-эмодзи (см. 1.7).
- Серые центрированные строки в ленте — служебные записи, например «Анна пригласила Ивана», «Иван присоединился», «Мария вышла», «Чат переименован». Их пишет сам мессенджер, а не люди.

#### Галочки у ваших сообщений

| Значок | Что означает |
| --- | --- |
| Одна галочка | Сообщение отправлено и сохранено на сайте. Собеседник его ещё не открывал. |
| Две галочки | Собеседник открыл разговор и прочитал сообщение. |

В группах галочки отражают прочтение хотя бы одним из участников (кроме вас).

#### Если переписка длинная

В окне сразу показываются последние сообщения. Чтобы увидеть более раннюю переписку, нажмите серую кнопку **«Показать более ранние»** в самом верху ленты: сообщения подгрузятся порциями, а лента не будет «прыгать» — вы останетесь на том месте, где читали. Если новое сообщение придёт, пока вы читаете историю, лента сама не перемотается.

#### Статусы собеседника

- **в сети** — человек был активен на сайте менее 5 минут назад. Важно: «в сети» значит «страница сайта у него открыта и работает», а не «он смотрит на ваше сообщение». Вкладка в фоне тоже считается.
- **был(а) сегодня в 14:35** или **был(а) вчера в 21:10** — время последней активности на сайте.
- **был(а) недавно** — заходил в последние 30 дней, но не вчера и не сегодня. **был(а) давно** — не заходил больше месяца (или данных нет).
- **печатает…** — собеседник набирает текст прямо сейчас. Подсказка гаснет примерно через 5 секунд после последней нажатой клавиши.
- В групповом чате вместо этого показывается число участников и сколько из них в сети; в списке участников у приглашённых, которые ещё не вступили, написано «ждёт ответа».

### 1.7. Как писать сообщения

Поле ввода находится внизу окна разговора. Слева от него кнопка **Смайлики**, справа синяя круглая кнопка **Отправить** со значком бумажного самолётика.

#### Клавиши

| Что нажать | Что произойдёт |
| --- | --- |
| Enter | Сообщение отправляется |
| Shift + Enter | Перенос строки, сообщение не отправляется |
| Ctrl + Enter (на Mac — Cmd + Enter) | Отправка в маленьком окне «Написать: имя», которое открывается с профиля |

Поле само растёт по высоте, пока вы набираете несколько строк, но не выше примерно шести строк; дальше появляется прокрутка внутри поля. Если вы печатаете по-китайски, по-японски или другим способом через «подсказки» ввода, Enter, которым вы выбираете вариант из подсказки, сообщение не отправляет.

#### Что можно писать

- Только обычный текст. Картинки, файлы, фото и голосовые сообщения отправить нельзя.
- Максимум **1024 символа** в одном сообщении. Администратор мог поставить меньший предел (256 или 512). Когда до предела остаётся меньше 150 символов, под полем появляется счётчик вида `900/1024`. Если превысить предел, сообщение не уйдёт, а вы увидите пояснение «Сообщение слишком длинное».
- Пробелы и переводы строк в начале и в конце текста обрезаются. Пустое сообщение (одни пробелы) отправить нельзя.
- Ограничения по тексту проверяются ещё и на сервере, так что обойти их нельзя.

#### Как быстро можно писать

Между двумя вашими сообщениями должна пройти минимум одна секунда (администратор может увеличить паузу). Если вы нажали «Отправить» слишком быстро, появится подсказка «Не так быстро! Подождите секунду» — просто нажмите ещё раз через мгновение. Текст при этом не пропадает.

#### Смайлики

Смайлики можно добавить двумя способами.

1. **Кнопка «Смайлики»** открывает панель из 32 картинок. Нажмите на нужную — она вставится туда, где стоит курсор. Панель закрывается, когда вы щёлкаете в любое другое место.
2. **Набор буквами.** Некоторые сочетания превращаются в картинку при отправке:

| Вы пишете | Получается |
| --- | --- |
| `:)` или `:-)` | улыбка |
| `:(` или `:-(` | грусть |
| `:D` | широкая улыбка |
| `;)` | подмигивание |
| `:P` или `:p` | язык |
| `<3` | сердечко |
| `:O` | удивление |
| `:*` | поцелуй |
| `XD` | смех до слёз |

Превращение происходит один раз, в момент отправки. Если вы не хотите, чтобы сочетание заменилось, поставьте между символами пробел или какую-нибудь букву.

#### Ссылки

Адреса, начинающиеся с `http://` или `https://`, автоматически становятся нажимаемыми и открываются в новой вкладке. Адреса вида `example.com` без начала `http` остаются обычным текстом: их придётся скопировать. Другие виды ссылок (например, начинающиеся с `javascript:` или `ftp:`) никогда не становятся нажимаемыми: это сделано для вашей безопасности.

**Совет по безопасности.** Прежде чем переходить по ссылке от человека, которого вы не знаете, подумайте. Мессенджер не проверяет, куда ведёт адрес. Не вводите пароль сайта нигде, куда вас привела ссылка из чата.

#### Черновики

Если вы начали писать сообщение, но переключились на другой разговор, начатый текст запоминается. Вернувшись, вы найдёте его в поле. Но если вы **перезагрузили страницу или закрыли вкладку**, черновик пропадёт: он хранится только в открытом окне.

#### Если сообщение не ушло

| Что вы видите | Что делать |
| --- | --- |
| «Нет связи с сервером» | Проверьте интернет и нажмите «Отправить» ещё раз. Текст остался в поле. |
| «Пользователь не найден или недоступен» | Человек удалён, заблокирован или не подтвердил регистрацию. Написать ему нельзя. |
| «Нельзя писать самому себе» | Выберите другого человека. |
| «Нужно войти на сайт» | Ваш сеанс закончился. Войдите на сайт заново, откройте мессенджер и повторите. |
| «У вас нет доступа к этому чату» | Вас убрали из чата, он удалён администратором, либо вы пытались открыть чужой разговор. |

### 1.8. Уведомления, счётчики и звуки

Мессенджер сообщает о новых сообщениях тремя способами, и вам не нужно держать его открытым.

#### Счётчик в шапке сайта

Зелёный кружок рядом со ссылкой «Сообщения» (или на круглой кнопке) показывает сумму двух вещей: сколько у вас непрочитанных сообщений и сколько приглашений в групповые чаты ждут ответа. Число обновляется само примерно раз в 5 секунд (администратор может поставить другой интервал). Если у вас открыта страница, но вкладка свёрнута или скрыта, проверка идёт реже: не чаще раза в 10 секунд.

Число также добавляется к названию вкладки браузера, например «(3) Название сайта». Благодаря этому вы заметите новое сообщение, даже если смотрите на другую вкладку. Если число больше 99, показывается «99+».

#### Всплывающие карточки

Когда вам пишут, в правом нижнем углу выезжает карточка с именем отправителя и началом сообщения. Правила:

- На каждый разговор — **одна** карточка. Если в нём пришло ещё несколько сообщений, на карточке растёт счётчик, а не появляются новые карточки.
- Карточка **не пропадает сама**. Она остаётся, пока вы не нажмёте на крестик в её правом верхнем углу или не откроете сам разговор. Если вы закроете страницу и придёте позже, карточка вас дождётся.
- Нажмите на текст карточки, чтобы открыть этот разговор.
- Единовременно показываются не более трёх карточек. Если разговоров с новыми сообщениями больше, под ними появится строка «и ещё N».
- Карточка исчезает сама, как только вы открыли разговор, ответили в нём или прочитали сообщения на другом устройстве или в другой вкладке.
- Для приглашения в групповой чат карточка особая: на ней написано «Вас пригласили в чат», название, кто пригласил, и две кнопки — **Присоединиться** и **Отклонить**.

Если вы сами отправляете сообщение с чужого профиля, внизу справа на несколько секунд появляется короткое подтверждение «Сообщение отправлено» с ссылкой **Открыть чат**.

Всплывающие карточки показываются на любой странице сайта, включая сам мессенджер. В мессенджере карточка не появляется только для того разговора, который у вас сейчас открыт (вы и так видите его сообщения). Если пишет кто-то из других разговоров, карточка появится, а щелчок по ней переключит вас на этот разговор без перезагрузки страницы. Если администратор отключил всплывающие уведомления для всего сайта, остаются только счётчик в шапке и звук.

#### Звуки

Мессенджер использует несколько коротких звуков:

| Событие | Звук |
| --- | --- |
| Сообщение пришло, когда вы на другой странице или в другом разговоре | Двойной «динь-динь» |
| Сообщение пришло в тот разговор, который у вас сейчас открыт | Тихий короткий «поп» |
| Вы отправили сообщение | Лёгкий «свист» |
| Вас пригласили в групповой чат | Отдельный мелодичный сигнал |
| Ошибка (например, слишком длинное сообщение) | Короткий негромкий сигнал |

Звуки появляются не сразу после загрузки страницы: браузеры запрещают сайтам издавать звуки, пока человек хотя бы раз не нажал или не коснулся экрана. Поэтому, если вы только что открыли сайт и никуда не нажимали, первый звук может не прозвучать. Это нормально, а не поломка.

**Как выключить звук.** Откройте страницу мессенджера и нажмите на значок динамика в верхней части списка разговоров. Значок меняется, показывая, включён звук или нет. Настройка запоминается в вашем браузере: на другом браузере или устройстве её придётся повторить, а если вы очистите данные сайта — она сбросится на «включено». Если администратор вообще отключил звуки, значка не будет.

#### Уведомления отдельного разговора (без звука)

Если какой-то разговор вам мешает, откройте его и нажмите на кнопку с колокольчиком в верхней панели: подсказка у кнопки «Выключить уведомления». Дальше:

- от этого разговора не появляются всплывающие карточки;
- в списке слева у него значок перечёркнутого колокольчика;
- непрочитанные сообщения по-прежнему копятся и показываются у разговора и в общем счётчике: вы ничего не теряете, просто вас не отвлекают.

Чтобы вернуть уведомления, нажмите ту же кнопку ещё раз (подсказка «Включить уведомления»).

### 1.9. Групповые чаты: приглашения, вход, выход

Отдельной кнопки «Создать группу» в мессенджере нет. Групповой чат появляется, когда вы **приглашаете в уже существующий разговор третьего человека**. Так задумано: сначала у вас есть разговор, потом в него кого-то зовут.

#### Как пригласить человека

1. Откройте разговор (личный или групповой).
2. В верхней панели нажмите кнопку **Пригласить** (значок человека с плюсом).
3. В окне «Пригласить в чат» начните вводить ник или логин. Нужно ввести **не меньше 2 символов**. Ниже появится список подходящих людей, не больше 8.
4. Нажмите на нужного человека. Он получит приглашение, а в ленте появится серая строка «Вы пригласили …».
5. Окно закроется само, внизу появится подтверждение «… приглашён(а) в чат».

В списке поиска **не будет**: вас самих; людей, которые уже в этом чате или уже приглашены; заблокированных и ещё не подтвердивших регистрацию. Если человека нет в списке, значит, он не подходит под эти условия или имя введено с опечаткой. Поиск идёт по нику и логину, а не по тексту переписки.

#### Что происходит, когда вы приглашаете в личный диалог

Личный диалог двоих **превращается в групповой чат**. В ленте появится серая строка «… превратил(а) диалог в групповой чат». Обратите внимание на два важных следствия:

- Приглашённый, **вступив в чат, увидит всю прежнюю переписку**, в том числе то, что было написано до его приглашения. В окне приглашения об этом написано явно. Если в диалоге есть что-то личное, подумайте, прежде чем звать третьего.
- Диалог больше не считается «личным»: из него можно выйти, у него есть название, к нему можно приглашать ещё людей. Если позже вы или собеседник снова нажмёте «Написать сообщение» в профиле друг друга, начнётся **новый** личный диалог, а группа останется отдельным чатом.

#### Что видит приглашённый

Приглашённый получает:

- зелёный кружок со счётчиком в шапке (приглашения входят в общую сумму);
- всплывающую карточку «Вас пригласили в чат» с названием, именем пригласившего и кнопками **Присоединиться** и **Отклонить**;
- блок «Приглашения» над списком разговоров на странице мессенджера.

Пока человек **не нажал «Присоединиться», он не видит переписки** и не может писать. Приглашение не пропадает само: оно ждёт, пока человек не примет или не отклонит его.

- **Присоединиться.** Чат откроется, в ленте появится строка «… присоединился(-ась) к чату», а вы увидите всю историю.
- **Отклонить.** Приглашение исчезнет. В чате участники увидят строку «… отклонил(а) приглашение». Приглашение можно прислать повторно.

Если чат уже заполнен (по умолчанию в нём до 20 человек, считая приглашённых, но ещё не вступивших), при попытке пригласить появится сообщение «В чате уже максимальное число участников». Если вы пытаетесь пригласить того, кто уже в чате или уже приглашён, вы увидите «Этот пользователь уже в чате» или «Этому пользователю уже отправлено приглашение».

#### Название группы и список участников

- Нажмите на название чата в верхней панели (или на значок «Информация о чате»), чтобы открыть список участников. В нём видно, кто в сети, а ниже — раздел «Приглашены, но ещё не присоединились» с пометкой «ждёт ответа».
- Переименовать чат может **любой участник**, а не только создатель. Новое название видно всем, в ленте появляется строка «… переименовал(а) чат: «Новое название»». Переименовывайте аккуратно: отменить это может только следующее переименование.

#### Как выйти из группы

1. В окне чата откройте информацию о чате и нажмите **Выйти из чата**.
2. Кнопка изменится на вопрос **Точно выйти?**. Нажмите ещё раз, если уверены. Это защита от случайного нажатия.
3. Вы окажетесь на главном экране, а остальные увидят строку «… покинул(а) чат».

После выхода вы **больше не видите переписки этой группы**, и вернуться без нового приглашения нельзя. Если из чата вышел последний человек, весь чат с перепиской удаляется без возможности восстановления. Из **личного диалога выйти нельзя** (будет сообщение «Из личного диалога выйти нельзя»): если вы не хотите продолжать разговор, отключите его уведомления.

#### Отключение уведомлений

В верхней панели разговора есть кнопка с колокольчиком. Нажмите её: появится короткое подтверждение «Уведомления этого чата выключены». Чат продолжает работать, сообщения копятся, но всплывающих карточек не будет. Повторное нажатие включает уведомления снова. Это единственное, что вы можете сделать, если вам пишет назойливый собеседник из личного диалога: жалобы и блокировки в версии 1.0.0 не предусмотрены (см. 1.12).

### 1.10. Границы возможностей: сводная таблица

| Параметр | Значение по умолчанию | Кто может изменить |
| --- | --- | --- |
| Длина одного сообщения | 1024 символа | Администратор: 256, 512 или 1024 |
| Пауза между вашими сообщениями | 1 секунда | Администратор |
| Участников в одном чате | 20 (вместе с приглашёнными) | Администратор |
| Содержимое сообщения | Только текст, смайлики и ссылки | Нельзя изменить |
| Поиск человека | От 2 символов, до 8 результатов | Нельзя изменить |
| Скорость появления новых сообщений | Около 1–2 секунд в открытом чате; около 5 секунд на остальных страницах | Администратор (для остальных страниц) |
| Вспомогательные карточки | Не более 3 одновременно | Нельзя изменить |
| Количество чатов | Не ограничено программой | Администратор (общий дисковый лимит) |
| Срок хранения переписки | Не ограничен программой | Администратор может удалять старую переписку |

### 1.11. Приватность и правила безопасности

#### Кто видит вашу переписку

- Её видят **участники чата** — и только они на сайте.
- Переписка хранится на сервере сайта **в открытом виде**: это не защищённый «сквозным шифрованием» мессенджер. Технический администратор сервера теоретически имеет доступ к базе данных. Не отправляйте через чат пароли, коды из СМС, данные банковских карт и документы.
- Панель администратора плагина показывает **только статистику и названия чатов** (кто создатель, сколько участников и сообщений), но не текст сообщений. Администратор может удалить чат целиком или старую переписку.

#### Что видят о вас другие

- Ваше имя (ник) и аватар сайта.
- Ваш статус «в сети»/«был(а)…» в разговоре с вами. Отключить его в версии 1.0.0 нельзя.
- Что вы печатаете сейчас, и прочитали ли вы сообщение (две галочки).

#### Кто может вам написать

Любой зарегистрированный участник сайта, который **может вас найти** (вы не заблокированы и подтвердили регистрацию). Запретить входящие сообщения или приглашения нельзя. Если вам пишут нежелательные вещи, отключите уведомления чата и сообщите администрации сайта: она может удалить чат.

#### Практические советы

- Не переходите по незнакомым ссылкам; мессенджер не проверяет их.
- Никому не называйте свой пароль. Администраторы сайта его не спрашивают.
- Не оставляйте мессенджер открытым на чужом компьютере. После работы нажмите «Выйти» в шапке сайта.
- Помните, что собеседник может скопировать вашу переписку.

### 1.12. Чего в версии 1.0.0 нет (чтобы не искать)

- Отправки картинок, файлов, голосовых и видеосообщений.
- Редактирования и удаления **отправленных** сообщений (в том числе «удалить у себя»). Если вы ошиблись, отправьте уточнение.
- Ответов на конкретное сообщение, пересылки, закрепления, реакций.
- Форматирования текста (жирный, курсив и т. п.): всё остаётся простым текстом.
- Поиска по тексту переписки (есть только поиск по именам).
- Блокировки человека, жалобы на сообщение, настройки «кто может мне писать».
- Уведомлений на почту или в телефон, когда вы не на сайте. Мессенджер сообщит о новом сообщении, только когда вы откроете сайт.
- Отдельной кнопки «Создать группу» и группового чата без начального разговора.
- Удаления чата самим пользователем (его удаляет администратор или он исчезает, когда выходит последний участник).
- Звонков.

Сообщения приходят с задержкой в несколько секунд — это не мгновенная доставка, как в Telegram. Мессенджер проверяет новые сообщения через равные промежутки времени, поэтому на странице сайта, где вы просто читаете статью, новое сообщение может появиться не сразу, а через несколько секунд.

### 1.13. Что делать, если что-то не работает

| Проблема | Что проверить |
| --- | --- |
| Не вижу ссылку «Сообщения» | Вы вошли на сайт? Посмотрите, нет ли круглой синей кнопки в левом нижнем углу. Если нет ни того, ни другого, обратитесь к администратору: возможно, вашей группе не выданы права. |
| Нет кнопки «Написать сообщение» в профиле | Вы на своём профиле? Вы вошли? Человек не заблокирован? Если всё в порядке, попробуйте обновить страницу и очистить кэш браузера. |
| Сообщения не приходят, счётчик не меняется | Проверьте интернет. Подождите 10–15 секунд (проверка идёт не мгновенно). Обновите страницу. Если не помогло, выйдите и снова войдите на сайт. |
| Нет звука | Нажмите на странице в любое место (браузер запрещает звук до первого нажатия); проверьте значок динамика в списке чатов и громкость устройства. |
| Всплывающая карточка не исчезает | Так и задумано: она висит, пока вы не нажмёте крестик или не откроете чат. |
| Не нашёл человека в поиске | Введите минимум 2 символа ника или логина. Человек может быть уже в этом чате, заблокирован или не подтвердил регистрацию. |
| В поле остались символы, а отправить не даёт | Проверьте счётчик символов: возможно, вы превысили лимит. |
| «Нет связи с сервером» | Временная проблема сети или сервера. Подождите минуту и повторите. Текст сообщения остаётся в поле. |
| Тёмный текст на тёмном фоне | Сообщите администратору сайта: его тема оформления плохо сочетается с мессенджером. |

### 1.14. Краткая памятка пользователя

1. Чтобы написать человеку, откройте его профиль и нажмите **Написать сообщение**.
2. Чтобы продолжить разговор, нажмите **Сообщения** в шапке сайта или круглую синюю кнопку.
3. Enter отправляет, Shift+Enter переносит строку.
4. Одна галочка — отправлено, две — прочитано.
5. Карточка в правом нижнем углу висит, пока вы её не закроете крестиком.
6. Позвать третьего можно кнопкой **Пригласить** в разговоре; приглашённый увидит всю историю после вступления.
7. Колокольчик отключает уведомления разговора, динамик в списке — звуки.
8. Из группы можно выйти, из личного диалога — нельзя.
9. Пароли и данные карт в чате не отправляйте.

## Часть 2. Руководство владельца и главного администратора

Эта часть для человека, который отвечает за сайт: решает, ставить ли плагин, на каком сервере он будет работать, кому давать доступ и что делать, если что-то пошло не так. Код здесь почти не нужен, но технические термины неизбежны; каждый из них объяснён при первом упоминании.

### 2.1. Что вы получаете и чего не получаете

Плагин `messenger` добавляет на сайт Cotonti личную переписку между зарегистрированными пользователями в стиле Telegram:

- личные диалоги и групповые чаты (группа возникает, когда в диалог приглашают третьего);
- счётчик непрочитанного в шапке сайта и в заголовке вкладки браузера;
- всплывающие карточки в правом нижнем углу на любой странице сайта;
- звуки, статусы «в сети» и «печатает», светлая и тёмная тема, адаптация под телефон;
- страницу управления в админке: статистика, список чатов, удаление, очистка старого.

Чего нет (честно, чтобы не было неприятных сюрпризов): картинок и файлов, голосовых, звонков, редактирования и удаления отправленных сообщений, блокировки собеседника и жалоб, уведомлений на почту и в телефон, шифрования переписки, поиска по тексту сообщений, настройки «кто может мне писать». Всё это обсуждается в разделах 2.9 и 3.9: часть пунктов можно добавить доработкой.

Плагин **не требует отдельного сервера**, демонов, Node.js, Redis или WebSocket. 
--- 
> **Он работает на том же PHP и той же базе данных, что и сайт. Цена этой простоты — «опрос»: браузер каждого пользователя сам регулярно спрашивает сервер, нет ли нового (см. раздел 2.6). Это определяет нагрузку.**  

### 2.2. Требования к программному обеспечению

| Что | Требование | Комментарий |
| --- | --- | --- |
| Cotonti | 0.9.26 или новее (заявлено в README, проверено на новой версии ядра, где вывод плагина идёт через `$pluginBody`) | Старые версии 0.9.x не проверялись |
| PHP | 8.0 и выше (разработка и тесты — на 8.5) | Нужны расширения `mbstring`, `json`, `pdo_mysql` — они есть почти у всех хостингов |
| База данных | MySQL 5.7+ или MariaDB 10.3+ с таблицами InnoDB | Нужна кодировка `utf8mb4`, иначе эмодзи в сообщениях не сохранятся (см. FAQ в части 3) |
| Префикс таблиц | `cot_` | Установочный файл плагина жёстко использует префикс `cot_`. Если на сайте другой префикс, правку нужно сделать вручную (см. 2.4) |
| JavaScript в браузере | Включён | Мессенджер — это страница на JavaScript; без него пользователь увидит только просьбу его включить |
| Современный браузер | Chrome, Firefox, Safari, Edge последних лет | Нужны возможности `fetch` и Web Audio. Старые браузеры (например, Internet Explorer) не поддерживаются |
| Bootstrap | Не нужен | Плагин приносит собственные стили; Bootstrap 5.3 используется только для определения тёмной темы через `data-bs-theme` |
| Шаблон сайта | Обязателен вывод `{FOOTER_RC}` в подвале | Именно туда плагин добавляет свой скрипт |
| HTTPS | Рекомендуется | Не требование плагина, но переписка идёт открытым текстом по HTTP, если нет HTTPS |

**Важное о проверках.** Тесты по сценариям (49 проверок логики на тестовой базе) и проверка интерфейса в автоматизированном браузере (без ошибок) выполнены в тестовой среде. На вашей конкретной теме оформления, хостинге и версии PHP поведение нужно проверить самим по чек-листу из раздела 2.10.

### 2.3. Требования к оборудованию и хостингу

Плагину не нужно «особенное» оборудование. Ключевой параметр — не объём диска, а **количество запросов в секунду, которое выдержит ваш PHP**, потому что каждый открытый у пользователя сайт регулярно обращается к серверу. Подробный расчёт в разделе 2.6. Здесь — ориентиры (это оценки, а не измерения на вашем сайте):

| Сайт | Одновременно онлайн | Что подойдёт |
| --- | --- | --- |
| Небольшой | до 50 | Обычный виртуальный хостинг или VPS с 1 ядром и 1 ГБ памяти |
| Средний | 50–300 | VPS 2 ядра, 2–4 ГБ, PHP-FPM, OPcache, интервал опроса 8–15 секунд |
| Крупный | 300 и больше | VPS 4+ ядер или отдельный сервер, PHP-FPM с достаточным числом процессов, интервал 15 секунд, а при росте — доработка (см. 3.9) |

Обязательное условие для любого размера: включён **OPcache** (кэш скомпилированного PHP-кода), иначе каждый запрос опроса будет заново читать и разбирать десятки файлов ядра Cotonti.

### 2.4. Установка по шагам

**Перед началом сделайте резервную копию базы данных и каталога `plugins`.** Установка добавляет четыре таблицы; существующие данные сайта она не меняет.

1. **Загрузка файлов.** Распакуйте архив `messenger-1.0.0.zip`. Внутри — каталог `plugins/messenger`. Загрузите его целиком в каталог `plugins` вашего сайта (по FTP, SFTP или через панель хостинга). В итоге должен получиться путь `plugins/messenger/messenger.setup.php`. Частая ошибка — вложить лишний каталог (`plugins/messenger/messenger/...`): тогда плагин в списке не появится.
2. **Права на файлы.** Файлам нужно право чтения для веб-сервера. Права записи плагину не нужны: он ничего не пишет на диск, всё хранится в базе.
3. **Установка в админке.** Откройте «Администрирование → Расширения», найдите «Мессенджер» (код `messenger`, версия 1.0.0), нажмите «Установить». Если установка прошла успешно, появятся таблицы `cot_msgr_chats`, `cot_msgr_members`, `cot_msgr_messages`, `cot_msgr_notifs`.
4. **Права доступа.** Ответ на вопрос «кому разрешено» — в разделе 2.5 ниже. По умолчанию участникам дано чтение и запись, гостям — ничего.
5. **Настройки.** Откройте настройки плагина и проверьте шесть параметров (таблица ниже).
6. **Подключение к шаблону.** Это необязательный, но желательный шаг: раздел 2.7.
7. **Проверка.** Пройдите чек-лист 2.10 под двумя разными учётными записями в двух разных браузерах.

#### Если префикс таблиц не `cot_`

Установочный файл `setup/messenger.install.sql` и удаляющий файл содержат имена таблиц с префиксом `cot_`. Код плагина при работе берёт префикс из настроек ядра, поэтому проблема возникает только при установке. Решение: до установки откройте оба файла в `plugins/messenger/setup/` текстовым редактором и замените `cot_msgr_` на ваш префикс с `msgr_` (например, `site_msgr_`). Если вы не уверены, как это сделать, попросите разработчика: ошибка здесь приводит к тому, что установка «проходит», а таблиц с нужными именами нет.

#### Если установка завершилась с ошибкой

- Откройте сообщение об ошибке: чаще всего это нехватка прав у пользователя базы данных на создание таблиц (`CREATE`) или несовместимый префикс.
- Убедитесь, что таблицы действительно созданы (например, через phpMyAdmin).
- После исправления нажмите «Удалить» и затем «Установить» ещё раз: повторная установка безопасна, таблицы создаются с условием «если не существуют».

### 2.5. Права доступа

Плагин использует стандартную систему прав Cotonti: «Администрирование → Пользователи → Права → Плагины → Мессенджер».

| Право | Что значит | Значение по умолчанию |
| --- | --- | --- |
| Чтение (R) | Видеть мессенджер, список чатов, читать, получать уведомления и счётчик | Участники — да, гости — нет |
| Запись (W) | Отправлять сообщения, начинать диалоги, приглашать, присоединяться, выходить, переименовывать, отключать уведомления | Участники — да, гости — нет |
| Администрирование (A) | Доступ к странице управления «Администрирование → Другое → Мессенджер» | Только администраторы |

Практические следствия:

- **Гости не видят ничего.** Ни ссылки в шапке, ни кнопки на профиле, ни всплывающих карточек.
- **Режим «только чтение» для группы.** Дайте группе право R, но не W: такие пользователи смогут читать свои чаты и получат счётчики, но писать, отвечать на приглашения и отключать уведомления не смогут. Кнопка «Написать сообщение» на профилях им не показывается.
- **Как запретить мессенджер новичкам или определённой группе.** Уберите у этой группы оба права R и W. Существующая переписка останется в базе, но пользователь её не увидит.
- **Если у пользователя нет права, ничего не сломается**: все проверки идут на сервере, а скрипт в браузере просто не загружается.

### 2.6. Параметры настроек

Настройки открываются из «Администрирование → Расширения → Мессенджер → Настройки».

| Параметр | Допустимые значения | По умолчанию | На что влияет |
| --- | --- | --- | --- |
| Максимальная длина сообщения | 256, 512, 1024 | 1024 | Лимит символов в одном сообщении. Меньше — меньше нагрузка на базу и меньше возможностей для «простыней» |
| Минимальный интервал между сообщениями | 0, 1, 2, 3, 5 секунд | 1 | Защита от флуда. 0 отключает защиту; не рекомендуется |
| Максимум участников в группе | 5, 10, 20, 50, 100 | 20 | Считаются и принявшие приглашение, и ещё не ответившие. Чем больше группа, тем тяжелее каждое обновление |
| Интервал фоновой проверки на обычных страницах | 3, 5, 8, 15 секунд | 5 | **Главный рычаг нагрузки** (см. 2.6.1). Чем реже — тем меньше запросов, но дольше ждать счётчик |
| Звуки | Да/Нет | Да | Включает звуки для всех. Пользователь всё равно может выключить их у себя |
| Всплывающие уведомления | Да/Нет | Да | Включает карточки в правом нижнем углу. Счётчик в шапке и звук остаются независимо от этого |

Обратите внимание: настройки не позволяют, например, задать длину сообщения 2000 или интервал 30 секунд. Допустимые значения зашиты в список выбора; чтобы расширить диапазон, нужна правка файла `messenger.setup.php` (см. часть 3).

### 2.7. Интеграция в шаблоны сайта

Это главный практический раздел. Плагин работает «из коробки», но выглядит и ведёт себя лучше, если вручную поставить в шаблон темы три метки. Метка (тег) — это слово в фигурных скобках, например `{HEADER_MESSENGER}`: Cotonti заменяет его на готовый кусок страницы.

Все правки делаются в файлах вашей **текущей темы**, в каталоге `themes/ИМЯ_ТЕМЫ/`. Перед правкой скопируйте файл-оригинал (например, `header.tpl` → `header.tpl.bak`).

#### Что работает без правок шаблона

| Элемент | Без правок | После правки |
| --- | --- | --- |
| Ссылка «Сообщения» со счётчиком | Появляется круглая синяя кнопка с самолётиком в левом нижнем углу экрана | Ссылка встроена в шапку сайта рядом с именем пользователя |
| Кнопка «Написать сообщение» на профиле | Вставляется скриптом сразу после заголовка страницы (первого `h1`); если заголовка нет — плавает слева внизу | Кнопка стоит там, где вы её разместите в блоке информации о человеке |
| Скрипт и стили плагина | Подключаются автоматически, если тема выводит `{FOOTER_RC}` (стили добавляются в `<head>`) | Без изменений |

То есть правки шаблона — вопрос аккуратности внешнего вида, а не работоспособности. Единственное **обязательное** условие описано в шаге 3.

#### Шаг 1. Ссылка «Сообщения» в шапке: `header.tpl`

Откройте `themes/ИМЯ_ТЕМЫ/header.tpl` и поставьте метку там, где в шапке выводятся ссылки пользователя (профиль, выход). Метка называется `{HEADER_MESSENGER}`. Рядом есть вторая метка `{HEADER_MESSENGER_URL}` — только адрес страницы без оформления, если вы хотите оформить ссылку сами.

Пример для темы на Bootstrap 5. Названия соседних элементов у каждой темы свои, поэтому важна только строка с меткой: её нужно положить внутрь блока шапки, который виден вошедшему пользователю (там, где у вас выводятся имя пользователя и ссылка «Выход»):

```
<!-- Внутри блока шапки, который показывается вошедшему пользователю -->
	<!-- IF {PHP|cot_plugin_active('messenger')} -->
	{HEADER_MESSENGER}
	<!-- ENDIF -->
<!-- ...здесь остаются ваши собственные ссылки темы: профиль, выход и т. д. -->
```

Пример со своим оформлением, через адрес:

```
<!-- IF {PHP|cot_plugin_active('messenger')} -->
<a class="oc-menu-item" href="{HEADER_MESSENGER_URL}">
	<span class="oc-menu-icon"><i class="fa-brands fa-telegram"></i></span>
	<span class="oc-menu-text">
		<strong>{PHP.L.msgr_title}</strong>
	</span>
</a>
<!-- ENDIF -->
```

Запомните: если вы оформляете ссылку сами через `{HEADER_MESSENGER_URL}`, **счётчик в ней появиться не сможет**. Он привязан к ссылке, которую выдаёт метка `{HEADER_MESSENGER}`. Если вам нужен счётчик, используйте именно её и настраивайте вид через CSS (см. ниже).

Что выводит метка: ссылку с классом `msgr-hdr`, значком самолётика, словом «Сообщения» и зелёным кружком-счётчиком. У гостей и у людей без права чтения метка пустая, поэтому её можно ставить безусловно, не заворачивая в проверки.

На узком экране (меньше 760 пикселей) слово «Сообщения» скрывается, остаются значок и счётчик.

**Как проверить.** Войдите на сайт. Ссылка должна появиться в шапке, а круглая кнопка в левом нижнем углу — нет. Если появились обе, значит метка поставлена не в тот блок шаблона (он не выводится для вошедших) или файл не загрузился на сервер.

#### Шаг 2. Кнопка на профиле: `users.details.tpl`

Откройте `themes/ИМЯ_ТЕМЫ/users.details.tpl`. Поставьте `{USERS_DETAILS_MESSENGER}` туда, где показываются действия с человеком или его данные, например под аватаром. Метка `{USERS_DETAILS_MESSENGER_URL}` даёт адрес без оформления.

```
<!-- IF {PHP|cot_plugin_active('messenger')} AND {PHP.usr.id} > 0 -->
<div class="mb-3">{USERS_DETAILS_MESSENGER}</div>
<!-- ENDIF -->
```
или
```
<!-- IF {PHP|cot_plugin_active('messenger')} AND {PHP.usr.id} > 0 -->
<a href="{USERS_DETAILS_MESSENGER_URL}" 
class="oc-menu-item" 
title="{PHP.L.messenger_subtitle_desc}">
	<span class="btn-as-mega-menu-icon"><i class="fa-brands fa-telegram"></i></span>
	<span class="btn-as-mega-menu-text">
		<strong>{PHP.L.messenger_btn_write_title}</strong>
		<small>{PHP.L.messenger_subtitle}</small>
	</span>
</a>
<!-- ENDIF -->
```


Метка пустая в трёх случаях: вы смотрите **свой** профиль; у вас нет права записи на плагин; человек заблокирован или не подтвердил регистрацию. В этих случаях кнопка не должна показываться, и это нормально.

При нажатии на кнопку открывается небольшое окно для первого сообщения (а не новая страница). Если JavaScript отключён, кнопка работает как обычная ссылка на страницу `?e=messenger&m=new&u=НОМЕР`.

#### Шаг 3. Подвал: `{FOOTER_RC}` — это обязательно

Скрипт мессенджера (отвечает за счётчик, всплывающие карточки, звуки, весь интерфейс чата) добавляется в общую переменную `{FOOTER_RC}`. Откройте `themes/ИМЯ_ТЕМЫ/footer.tpl` и убедитесь, что метка там есть:

```
{FOOTER_RC}
```

Она должна стоять **перед** закрывающим `</body>`. В стандартных темах Cotonti она уже есть. В самодельных и сильно переработанных темах её иногда удаляют. Признак того, что метки нет: ссылка в шапке видна, но число на ней не меняется, окна чата не открываются, на странице мессенджера написано «Загрузка…».

Проверка: откройте страницу сайта, нажмите «Просмотр исходного кода страницы» и найдите там `MSGR_BOOT` и `messenger.js`. Если их нет — метки в подвале нет, или вы смотрите страницу под гостем.

#### Страница самого мессенджера

Страница `index.php?e=messenger` оформляется шаблоном `plugins/messenger/tpl/messenger.app.tpl` и выводится внутри обычного макета вашего сайта (шапка, подвал, меню — ваши). Чтобы изменить шаблон безопасно, не правя файл плагина, скопируйте его в тему:

```
themes/ИМЯ_ТЕМЫ/plugins/messenger/messenger.app.tpl
```

Cotonti сначала ищет шаблон в теме, а потом в самом плагине. Это штатный механизм подмены шаблонов плагинов; после обновления плагина ваша копия не затрётся, но и не получит новых изменений — сверяйте её вручную при обновлениях.

#### Подгонка внешнего вида (CSS)

Все стили лежат в `plugins/messenger/css/messenger.css`. Править файл плагина не нужно: добавьте свои правила в CSS темы, они победят, если подключены позже. Цвета заданы переменными, например `--tg-badge` (цвет счётчика), `--tg-accent` (акцентный цвет), `--tg-online` (точка «в сети»), `--tg-side` (фон списка чатов), `--tg-sub` (серый текст). Полный список — в начале файла стилей.

```
:root {
  --tg-accent: #0d6efd;   /* цвет кнопок и ссылок мессенджера */
  --tg-badge: #dc3545;    /* красный счётчик вместо зелёного */
}
```

Свойство ссылки в шапке: она наследует цвет текста соседних ссылок (`color: inherit`), поэтому в большинстве тем подстраивается сама.

#### Тёмная тема

Мессенджер переключается на тёмное оформление в двух случаях: у страницы стоит `data-bs-theme="dark"` (так делает переключатель темы Bootstrap 5.3), либо система устройства выбрала тёмную тему. Если ваш сайт переключается на тёмную тему другим способом (своим классом на `body`), мессенджер этого не узнает; подскажите разработчику (часть 3, FAQ).

#### Если тема нестандартная

- **Тема без файла `users.details.tpl`.** Метки не нужны: кнопка поставится сама, но её положение ты не выбираешь.
- **Тема, где шапка собирается не через `header.tpl`.** Не страшно: сработает круглая кнопка. Если она вас не устраивает, нужна доработка шапки.
- **Тема, где `{FOOTER_RC}` выведен до подключения основных скриптов.** Чат это не ломает: плагин не использует jQuery и Bootstrap-скрипты.
- **Два макета сайта (обычный и мобильный).** Правьте оба `header.tpl`.

#### Шпаргалка по меткам

| Метка | Файл | Что выводит | Обязательна |
| --- | --- | --- | --- |
| `{HEADER_MESSENGER}` | `header.tpl` | Ссылка «Сообщения» с живым счётчиком | Нет (есть запасная круглая кнопка) |
| `{HEADER_MESSENGER_URL}` | `header.tpl` | Только адрес страницы мессенджера | Нет |
| `{USERS_DETAILS_MESSENGER}` | `users.details.tpl` | Кнопка «Написать сообщение» | Нет (есть автоматическая вставка) |
| `{USERS_DETAILS_MESSENGER_URL}` | `users.details.tpl` | Адрес «написать этому человеку» | Нет |
| `{FOOTER_RC}` | `footer.tpl` | Скрипт и данные мессенджера | **Да** |




ReadMeMore:       https://abuyfile.com/market/cotonti/plugs/messenger
Support:          https://abuyfile.com/forums/cotonti/custom/
Source:           https://github.com/webitproff/messenger-cotonti
 
Date: 2026-11-18 
package messenger
version 1.1.1
author webitproff
copyright Copyright (c) 2026 webitproff | https://github.com/webitproff/messenger-cotonti
license BSD-3

