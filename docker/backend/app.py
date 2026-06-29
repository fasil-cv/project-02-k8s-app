from flask import Flask
import os

app = Flask(__name__)

@app.route("/health")
def health():
    return {"status": "Backend is running"}

@app.route("/db")
def db():
    # fake DB connection success message (or real MySQL later)
    return {"message": "Connection Successful from Backend"}

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000)
