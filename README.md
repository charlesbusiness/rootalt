# Laravel MLM Backend

A **Laravel-powered Multi-Level Marketing (MLM) backend** designed to manage users, hierarchical levels, reward points, and inventory in a scalable and modular way.  

---

## 🎯 Why This Project Is Relevant

This project highlights key backend development skills applied to a real-world domain (MLM systems):

- **Backend Architecture** – Modular design with user levels, rewards, and inventory management.  
- **API Development** – RESTful APIs for frontend and third-party integrations.  
- **Database Design** – Structured hierarchical models to handle MLM trees and point allocation.  
- **Authentication & Security** – Secure user authentication and role-based access.  
- **Reward Engine** – Automated points and levels tracking across multiple user hierarchies.  
- **Deployment** – Containerized with Docker for reproducible environments.  

---

## 🚀 Features

### Core MLM Features
- User registration & authentication  
- Multi-level user hierarchy management  
- Points & rewards tracking  
- User levels & progression rules  
- Inventory management for products  
- Commission calculation & reports  

### General Features
- RESTful APIs for all modules  
- Role & permission management  
- Queue & background job support  
- Reporting & analytics  
- Dockerized setup for quick deployment  

---

## 🛠️ Tech Stack
- **Framework:** Laravel 10+  
- **Database:** MySQL 8.x  
- **Cache/Queues:** Redis  
- **Containerization:** Docker & Docker Compose  
- **Web Server:** Nginx (inside Docker)  
- **Testing:** PHPUnit  

---

## 📦 Prerequisites
Ensure you have installed:
- [Git](https://git-scm.com/)  
- [Docker](https://www.docker.com/)  
- [Docker Compose](https://docs.docker.com/compose/)  

---

## ⚙️ Installation & Setup

### 1. Clone the repository
```bash
git clone https://github.com/your-username/rootatl-backend.git
cd mlm-backend
