# KADAM API Documentation

This document details the API endpoints available in the KADAM platform.

**Base URL:** `http://localhost/projects/kadam/api`

---

## Authentication

### 1. Signup
*   **Endpoint:** `/auth/signup.php`
*   **Method:** `POST`
*   **Description:** Registers a new user.
*   **Request Body:**
    ```json
    {
        "role": "student", // or "employer"
        "email": "user@example.com",
        "password": "password123",
        "name": "John Doe",
        "phone": "9800000000", // Optional
        "address": "Kathmandu", // Optional
        "employerType": "company" // Optional, required if role is employer
    }
    ```
*   **Success Response:**
    ```json
    {
        "success": true,
        "message": "User registered successfully.",
        "data": {
            "user_id": 15
        }
    }
    ```

### 2. Login
*   **Endpoint:** `/auth/login.php`
*   **Method:** `POST`
*   **Description:** Authenticates a user and returns a JWT token.
*   **Request Body:**
    ```json
    {
        "email": "user@example.com",
        "password": "password123"
    }
    ```
*   **Success Response:**
    ```json
    {
        "success": true,
        "message": "Login successful.",
        "data": {
            "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
            "user": {
                "id": 15,
                "username": "johndoe1234",
                "email": "user@example.com",
                "name": "John Doe",
                "role": "student",
                "status": "active",
                "profile_image": null
            }
        }
    }
    ```

### 3. Get Current User (Me)
*   **Endpoint:** `/auth/me.php`
*   **Method:** `GET`
*   **Headers:** `Authorization: Bearer <token>`
*   **Description:** Retrieves the profile of the currently logged-in user.
*   **Success Response:**
    ```json
    {
        "success": true,
        "message": "User profile retrieved.",
        "data": {
            "id": 15,
            "username": "johndoe1234",
            "email": "user@example.com",
            "name": "John Doe",
            "role": "student",
            "details": {
                "user_id": 15,
                "title": null,
                "bio": null,
                ...
            }
        }
    }
    ```

---

## Tasks

### 1. Create Task
*   **Endpoint:** `/tasks/create.php`
*   **Method:** `POST`
*   **Headers:** `Authorization: Bearer <token>`
*   **Description:** Creates a new task (Employer only).
*   **Request Body:**
    ```json
    {
        "title": "Build a Website",
        "description": "Need a React developer...",
        "category": "web",
        "budget": 5000,
        "deadline": "2023-12-31",
        "difficulty_level": "intermediate"
    }
    ```
*   **Success Response:**
    ```json
    {
        "success": true,
        "message": "Task posted successfully.",
        "data": {
            "task_id": 25
        }
    }
    ```

### 2. List Tasks
*   **Endpoint:** `/tasks/list.php`
*   **Method:** `GET`
*   **Description:** Retrieves a list of open tasks.
*   **Query Parameters:**
    *   `category` (optional): Filter by category (e.g., `web`, `design`).
    *   `search` (optional): Search by title or description.
*   **Success Response:**
    ```json
    {
        "success": true,
        "message": "Tasks retrieved successfully.",
        "data": [
            {
                "id": 1,
                "title": "Build a Landing Page",
                "employer_name": "Tech Corp",
                ...
            },
            ...
        ]
    }
    ```

### 3. My Tasks
*   **Endpoint:** `/tasks/my_tasks.php`
*   **Method:** `GET`
*   **Headers:** `Authorization: Bearer <token>`
*   **Description:** Retrieves tasks created by the current employer OR tasks applied to by the current student.
*   **Success Response:**
    ```json
    {
        "success": true,
        "message": "Tasks retrieved.",
        "data": [ ... ]
    }
    ```
