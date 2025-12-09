# Ketupat-Labs
CompuPlay
# 🤖 Ketupats Chatbot Module (v2.0)

[![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)](https://github.com/ketupats-labs/chatbot-module)
[![Stack](https://img.shields.io/badge/stack-Laravel%2011%20%7C%20React%2018%20%7C%20Node.js-purple)](https://laravel.com)
[![AI](https://img.shields.io/badge/AI-Google%20Gemini%20Flash-orange)](https://deepmind.google/technologies/gemini/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

> **AI-powered educational assistant optimized for Computer Science.**
> Now featuring conversation history, CS-scope restrictions, and the latest Gemini Flash model.

---

## 🚀 **Quick Navigation**

| 🏁 **Start Here** | 📚 **Documentation** | 🛠 **Development** |
| :--- | :--- | :--- |
| [⚡ 5-Minute Setup](#-5-minute-quick-start) | [📡 API Reference](#-api-endpoints) | [📂 File Structure](#-project-architecture) |
| [🔧 Configuration](#-configuration) | [🐛 Troubleshooting](#-troubleshooting) | [🤝 Contributing](#-collaboration) |

---

## ✨ What's New in v2.0 (December 2025)

<details>
<summary><strong>🎓 Computer Science Scope Restriction</strong></summary>
<br>
The chatbot is now strictly scoped to answer only Computer Science related questions.
<ul>
    <li>✅ <strong>Answers:</strong> Python, Java, Data Structures, Algorithms, Web Dev, AI/ML.</li>
    <li>❌ <strong>Ignores:</strong> Biology, Cooking, Sports, General History.</li>
</ul>
</details>

<details>
<summary><strong>🕰️ Conversation History & Persistence</strong></summary>
<br>
Never lose context again.
<ul>
    <li><strong>Capacity:</strong> Loads the last <strong>100 messages</strong> automatically.</li>
    <li><strong>Controls:</strong> Includes a specific <strong>Refresh Button (↻)</strong> to reload history.</li>
</ul>
</details>

<details>
<summary><strong>⚡ AI Engine: Gemini Flash</strong></summary>
<br>
We have upgraded the core engine for speed and cost-efficiency.
<ul>
    <li><strong>Primary Model:</strong> <code>gemini-flash-latest</code> for rapid responses.</li>
    <li><strong>Fallback:</strong> Automatically switches to <code>gemini-pro-latest</code> on 404 errors.</li>
</ul>
</details>

---

## ⚡ 5-Minute Quick Start

### 1️⃣ Prerequisites
* Node.js >= 14.0.0
* PHP >= 8.1 & Laravel 11
* Google Gemini API Key

### 2️⃣ Installation

#### Option A: Automated Script (Windows)
```powershell
# 1. Clone and Enter
git clone [https://github.com/your-team/chatbot-module.git](https://github.com/your-team/chatbot-module.git)
cd chatbot-module

# 2. Run the automated setup script
.\setup.ps1
