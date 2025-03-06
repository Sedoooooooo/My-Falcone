# My-Falcone

## Overview

"My-Falcone" is a web-based application to select planets and vehicles for finding Falcone across different destinations. It uses the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire).

## Setup Instructions

### Prerequisites
- **PHP** for Laravel
- **Composer** for PHP dependencies
- **Node.js** and **npm** for JavaScript dependencies

### Installation

1. **Clone the repository**:
    ```bash
    git clone https://github.com/Sedoooooooo/My-Falcone.git
    cd My-Falcone
    ```

2. **Install PHP dependencies**:
    ```bash
    composer install
    ```

3. **Install Node dependencies**:
    ```bash
    npm install
    ```

4. **Set up the `.env` file(not necessary)**:
    ```bash
    cp .env.example .env
    ```
    Configure database settings and generate the application key(not necessary):
    ```bash
    php artisan key:generate
    ```

5. **Run migrations**(not necessary):
    ```bash
    php artisan migrate
    ```

6. **Start the development server**:
    ```bash
    php artisan serve
    ```

7. **Compile assets**:
    ```bash
    npm run dev
    ```

### Production Build (optional)
```bash
npm run build
```
### Testing the Application

```bash

Open the app: http://127.0.0.1:8000

Select planets: Choose a planet for each destination (Destination 1-4).

Select vehicles: Pick one vehicle for each destination.

Check time taken: The time will dynamically update based on your selections.

Find Falcone: Once all destinations are filled, the Find Falcone! button will enable. Click it to finalize the selection.

Reset: Click Reset to clear all selections.

Code Explanation
Frontend: Tailwind CSS for styling, Alpine.js for interactivity (planet/vehicle selection, dynamic updates).
Backend: Laravel and Livewire handle the server-side logic and real-time updates between frontend and backend.

```
