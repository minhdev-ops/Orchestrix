#!/bin/bash
# Start AI Plant Doctor service (FastAPI + ViT model)
PROJ=/home/couterit/Work/02.Study/Project/Orchestrix
cd $PROJ/ai_service

export AI_MODEL_PATH=$PROJ/best_vit.keras
export AI_LABELS_PATH=$PROJ/ai_service/labels.txt

echo "Starting AI service..."
echo "  Model: $AI_MODEL_PATH"
echo "  Labels: $AI_LABELS_PATH"

# Kill any existing uvicorn
pkill -f "uvicorn app.main:app" 2>/dev/null
sleep 1

# Start uvicorn
exec uvicorn app.main:app --host 0.0.0.0 --port 8501
