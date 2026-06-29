# project-02-k8s-app
Kubernetes project application deploy E2E


- Nginx = frontend
- Python Flask = backend (very lightweight)
- MySQL = optional check



📁 Project Structure
```
project/
├── docker/
│   ├── frontend/
│   │   ├── Dockerfile
│   │   └── index.html
│   │
│   ├── backend/
│   │   ├── Dockerfile
│   │   ├── app.py
│   │   └── requirements.txt
│
└── k8s/
    ├── mysql.yaml
    ├── backend.yaml
    ├── frontend.yaml
    ├── services.yaml
```
