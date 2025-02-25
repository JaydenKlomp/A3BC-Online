# 🚀 A3BC Online - A Reddit-Inspired Community Platform


![PHP](https://img.shields.io/badge/PHP-8.1-blue) ![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.3-red) ![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)

A3BC Online is a **Reddit-style discussion platform** built using **CodeIgniter 4** and **Bootstrap**. Users can create posts, comment, upvote/downvote, and interact with content just like on Reddit. 🔥

---

## 📌 Features
✅ **User-Generated Content** – Create, view, and comment on posts  
✅ **Upvote & Downvote System** – Vote on posts and comments dynamically  
✅ **Comment Nesting** – Reply to comments in a Reddit-style thread  
✅ **Sorting Options** – Sort posts by `Hot`, `New`, `Top`, or `Rising`  
✅ **Dashboard Analytics** – View total posts, comments, upvotes/downvotes with graphs  
✅ **Dark Mode UI** – Inspired by Reddit's modern dark theme  
✅ **Customizable Communities (WIP)** – Users can create & manage communities

---

## 🛠️ Tech Stack
| Technology  | Description |
|-------------|------------|
| 🐘 PHP 8.1 | Backend language |
| 🔥 CodeIgniter 4 | PHP MVC Framework |
| 🎨 Bootstrap 5.3 | Responsive UI Framework |
| ⚡ JavaScript (Vanilla) | Client-side interactions |
| 📊 Chart.js | Data visualization for analytics |
| 🛢️ MySQL | Database for storing posts & comments |
| 🎛️ PhpMyAdmin | Database management |

---

## 🚀 Installation Guide
### 1️⃣ Clone the Repository
```bash
git clone https://github.com/JaydenKlomp/a3bc-online.git
cd a3bc-online
```

### 2️⃣ Set Up CodeIgniter
```bash
composer install
cp env .env  # Copy env file
```
🔹 **Configure the `.env` file** to match your database settings:
```
database.default.hostname = localhost
database.default.database = a3bc
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

### 3️⃣ Import the Database
- Import `codeigniter.sql` into MySQL using PhpMyAdmin or CLI:
```bash
mysql -u root -p a3bc < codeigniter.sql
```

### 4️⃣ Start the Development Server
```bash
php spark serve
```
🚀 Visit: **[http://localhost:8080](http://localhost:8080)**

---

## 📊 Dashboard Analytics
The **Dashboard** provides real-time insights into the platform:
- 📈 **Total Posts, Comments, Upvotes, Downvotes**
- 📊 **Graph: Posts created per day/week/month**
- 🔍 **Filter by Date, Time, and Type**

---

## 🔧 Folder Structure
```
📂 a3bc-online/
│── 📁 app/                  # CodeIgniter core files
│   ├── 📁 Controllers/      # Handles requests (Posts.php, Dashboard.php)
│   ├── 📁 Models/           # Handles database queries (PostModel.php, CommentModel.php)
│   ├── 📁 Views/            # UI pages (index.php, create.php, dashboard.php)
│── 📁 public/               # Frontend assets
│   ├── 📁 css/              # Stylesheets
│   ├── 📁 js/               # JavaScript logic
│   ├── 📁 images/           # Static assets
│── 📁 writable/             # Cache, logs, uploads
│── 📄 .env                  # Environment variables
│── 📄 README.md             # This file
│── 📄 codeigniter.sql          # Database structure
```

---

## 🌟 Contributing
We welcome contributions! Follow these steps:
1. 🍴 Fork the repository
2. 🌿 Create a new branch (`git checkout -b feature-name`)
3. 🛠️ Make your changes
4. 🚀 Commit and push (`git commit -m "Added new feature" && git push origin feature-name`)
5. 🔁 Open a Pull Request

---

## 📝 To-Do List
✅ **Basic Reddit-style layout**  
✅ **Sorting & filtering options**  
✅ **Voting system for posts/comments**  
✅ **Dashboard with statistics**  
🔲 **User authentication system**  
🔲 **Community-based subforums**  
🔲 **Notifications & moderation tools**

---

## 📜 License
This project is **open-source** and available under the **MIT License**.

---

👨‍💻 **Developed by [Jayden Klomp](https://github.com/JaydenKlomp)**  
🔗 **GitHub Repository:** [A3BC Online](https://github.com/JaydenKlomp/a3bc-online)

