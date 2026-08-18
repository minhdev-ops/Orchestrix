# AI Inference Service (FastAPI + TensorFlow cho best_vit.keras)
FROM python:3.11-slim

# Gỡ cache apt sau khi cài
ENV PYTHONUNBUFFERED=1 \
    TF_CPP_MIN_LOG_LEVEL=2 \
    PYTHONDONTWRITEBYTECODE=1 \
    PIP_NO_CACHE_DIR=1

# Cài deps runtime (libgl cho PIL, libglib cho TF)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libgl1 \
        libglib2.0-0 \
        curl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Cài dependencies trước (cache layer)
COPY requirements.txt /app/requirements.txt
RUN pip install --upgrade pip && pip install -r requirements.txt

# Copy source app
COPY app /app/app
COPY labels.txt /app/labels.txt

# Mặc định: model & labels mount từ host vào /models
ENV AI_MODEL_PATH=/models/best_vit.keras \
    AI_LABELS_PATH=/app/labels.txt \
    AI_INPUT_SIZE=224 \
    AI_TOP_K=5 \
    AI_DEVICE=auto \
    AI_STRIP_AUGMENTATION=false

EXPOSE 8501

HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD curl -fsS http://localhost:8501/health || exit 1

CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8501"]
