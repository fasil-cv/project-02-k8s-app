# project-02-k8s-app
Kubernetes project application deploy E2E




📁 Project Structure
```
application/
├── Dockerfile
├── nginx.conf
└── supervisord.conf
```


Build & Deployment Execution Pipeline
Navigate into your application/ folder and run the execution block below:

Bash
# 1. Build and push the image (Change 'your-registry-username' accordingly)
docker build -t your-registry-username/application:v3 .


# Database information
```
DATABASE_HOST : 'mysql-service'; 
DATABASE_NAME : 'lab-db';
DATABASE_USER : 'job';
DATABASE_PASSWORD 'lab_password_123';
```


# Database init config
```YAML
apiVersion: v1
kind: ConfigMap
metadata:
  name: db-init-script
  namespace: project-02-k8s-app
data:
  init.sql: |
    -- 1. Create the database if it doesn't exist
    CREATE DATABASE IF NOT EXISTS `lab-db`;
    USE `lab-db`;

    -- 2. Create the remote user and explicitly grant privileges
    CREATE USER IF NOT EXISTS 'job'@'%' IDENTIFIED BY 'lab_password_123';
    GRANT ALL PRIVILEGES ON `lab-db`.* TO 'job'@'%';
    FLUSH PRIVILEGES;

    -- 3. Create the target log table
    CREATE TABLE IF NOT EXISTS lab_table (
        id INT AUTO_INCREMENT PRIMARY KEY,
        worker_node VARCHAR(100) NOT NULL,
        status_metric VARCHAR(50) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
```



The Role of Each File
1. Dockerfile
The build instructions. It starts from a lightweight Linux base, installs Nginx, PHP, and Supervisor, copies your other three files into the image, and sets the startup command.

2. index.php
The application logic and user interface. It reads the database credentials from the cluster environment, establishes the secure connection, handles the button click to insert rows, and generates the dark-themed HTML table.

3. nginx.conf
The web server configuration. It listens for incoming HTTP traffic on port 80 and ensures that any request for a .php file is correctly routed internally to the PHP processing engine.

4. supervisord.conf
The process manager. Containers are designed to run only a single primary process. Supervisor acts as that single process, allowing both Nginx and PHP-FPM to run simultaneously in the background without the container crashing.

Build the Image
Whenever you make changes to your index.php file, navigate to your /application/ directory and run this command to rebuild the image before deploying it:

Bash
sudo docker build --no-cache -t fasilcv/k8sapp:v1.0.1 .



## Tech Stack
* **Frontend/App:** PHP (PDO), Tailwind CSS, Nginx, Supervisor
* **Database:** MySQL 8.0 with automated ConfigMap SQL initialization
* **Storage:** Decoupled data persistence via PersistentVolumeClaims (PVC)
