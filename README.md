> [!IMPORTANT]
> See detailed instructions for setup in [SETUP.md](SETUP.md)

# Digital Game Marketplace Project

> for the courses, CTIS256: Introduction to Backend Development & CTIS487: Mobile Application Development (Android, Kotlin)  
> Bilkent University — Fall 2025-2026

A full-stack digital game marketplace consisting of a PHP REST API backend and an Android (Kotlin) mobile application.

## 📋 Table of Contents

-   [Overview](#overview)
-   [Project Structure](#project-structure)
-   [Technologies Used](#technologies-used)
-   [Getting Started](#getting-started)

## 🎯 Overview

This project implements a digital game marketplace platform where users can browse, purchase, and manage digital games. The system consists of:

-   **Backend (CTIS256)**: PHP REST API with custom routing and controller architecture
-   **Mobile App (CTIS487)**: Native Android application built with Kotlin
-   **Admin Panel**: Web-based administration interface

## 📁 Project Structure

```
256-487-projects/
│
├── 256-php/                      # Backend (PHP)
│   ├── api/                      # REST API
│   │   ├── index.php             # API entry point & router
│   │   ├── restUtil.php          # REST utilities & base classes
│   │   ├── restUtil.local.php    # Local configuration
│   │   └── controllers/          # API controllers
│   │       ├── HealthController.php
│   │       └── UserController.php
│   │
│   └── frontend/                 # Web frontend
│       ├── login.php             # Login page
│       ├── login.css             # Login styles
│       └── admin.php             # Admin dashboard
│
├── 487-mobile/                   # Android app (Kotlin)
│   └── [Android project files]
│
├── README.md                     # This file
└── SETUP.md                      # Setup instructions
```

## 🛠️ Technologies Used

### Backend (256-php)

-   **PHP 8.x**: Server-side logic
-   **Custom REST Framework**: Lightweight routing and controller system
-   **WAMP Server**: Local development environment
-   **JSON**: Data interchange format

### Frontend

-   **HTML5/CSS3**: Web interface
-   **JavaScript**: Client-side interactivity

### Mobile (487-mobile)

-   **Kotlin**: Primary language
-   **Android SDK**: Mobile platform
-   **Material Design**: UI components

## 🚀 Getting Started

### Prerequisites

-   **WAMP Server** (Windows) or **LAMP/MAMP** (Linux/Mac)
-   **PHP 8.0+**
-   **Android Studio** (for mobile development)
-   **Git**

### Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/aliboron/256-487-projects.git
    cd 256-487-projects
    ```

2. **Setup PHP Backend**

    > [!IMPORTANT]
    > See detailed instructions in [SETUP.md](SETUP.md)

    Quick start:

    - Place project in WAMP's `www` directory
    - Configure the API wrapper at `www/api/`
    - Access API at: `http://localhost/api`

3. **Setup Android App**
    ```bash
    cd 487-mobile
    # Open in Android Studio
    ```

## 📄 License

This project is part of academic coursework at Bilkent University.

**Last Updated**: December 7, 2025
